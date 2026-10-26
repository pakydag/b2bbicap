<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class B2bOrder extends Model
{
    protected $fillable = [
        'order_group_id',
        'agent_id',
        'agent_name',
        'b2b_customer_id',
        'customer_business_name',
        'customer_code',
        'customer_vat_number',
        'customer_contact_name',
        'customer_phone',
        'customer_email',
        'customer_porto',
        'price_list_name',
        'price_list_snapshot',
        'internal_reference',
        'contact_id',
        'total_amount',
        'status',
        'payment_method',
        'notes',
        'admin_notes',
        'is_modified',
    ];

    protected $casts = [
        'price_list_snapshot' => 'array',
    ];

    public function groupOrders()
    {
        if (empty($this->order_group_id)) {
            return collect([$this]);
        }
        return self::where('order_group_id', $this->order_group_id)->orderBy('id')->get();
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function customer()
    {
        return $this->belongsTo(B2bCustomer::class, 'b2b_customer_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function items()
    {
        return $this->hasMany(B2bOrderItem::class, 'b2b_order_id');
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'In Attesa',
            'revision_pending' => 'Attesa Cliente',
            'customer_approved' => 'Approvato da Cliente',
            'customer_rejected' => 'Rifiutato da Cliente',
            'confirmed' => 'Confermato',
            'cancelled' => 'Annullato',
            default => strtoupper($this->status),
        };
    }

    public function getDisplayCustomerBusinessNameAttribute(): string
    {
        return $this->customer_business_name ?: ($this->customer?->business_name ?? 'Cliente N.D.');
    }

    public function getDisplayCustomerVatNumberAttribute(): ?string
    {
        return $this->customer_vat_number ?: ($this->customer?->vat_number ?? null);
    }

    public function getDisplayCustomerCodeAttribute(): ?string
    {
        return $this->customer_code ?: ($this->customer?->code ?? null);
    }

    public function getDisplayCustomerContactAttribute(): ?string
    {
        if ($this->customer_contact_name) {
            return $this->customer_contact_name;
        }
        if ($this->customer) {
            $c = trim(($this->customer->contact_name ?? '') . ' ' . ($this->customer->contact_surname ?? ''));
            return $c ?: null;
        }
        return null;
    }

    public function getDisplayCustomerPhoneAttribute(): ?string
    {
        return $this->customer_phone ?: ($this->customer?->phone ?? null);
    }

    public function getDisplayCustomerEmailAttribute(): ?string
    {
        return $this->customer_email ?: ($this->customer?->email ?? null);
    }

    public function getDisplayCustomerPortoAttribute(): ?string
    {
        return $this->customer_porto ?: ($this->customer?->porto ?? null);
    }

    public function getDisplayAgentNameAttribute(): string
    {
        if ($this->agent_name) {
            return $this->agent_name;
        }
        if ($this->agent) {
            return trim($this->agent->name . ' ' . $this->agent->surname);
        }
        return 'Assegnazione Diretta';
    }

    public function buildPriceListSnapshot($customer = null, $products = null): ?array
    {
        $customer = $customer ?: $this->customer;
        if (!$customer || !$customer->b2b_price_list_id) {
            return null;
        }

        $priceList = B2bPriceList::find($customer->b2b_price_list_id);
        if (!$priceList) {
            return null;
        }

        $uniqueProducts = collect();
        if ($products instanceof \Illuminate\Support\Collection) {
            $uniqueProducts = $products;
        } elseif (is_iterable($products)) {
            $uniqueProducts = collect($products);
        } else {
            $this->loadMissing('items.product.brand');
            $uniqueProducts = $this->items->pluck('product')->filter()->unique('id');
        }

        $productsData = [];
        foreach ($uniqueProducts as $p) {
            if (!$p) continue;
            $prodId = is_array($p) ? ($p['id'] ?? $p['product_id'] ?? null) : $p->id;
            $prodModel = ($p instanceof B2bProduct) ? $p : B2bProduct::find($prodId);
            if (!$prodModel) continue;

            $specificTiers = B2bPriceListItem::where('b2b_price_list_id', $priceList->id)
                ->where('b2b_product_id', $prodModel->id)
                ->orderBy('min_quantity', 'asc')
                ->get()
                ->map(fn($t) => [
                    'min_quantity' => $t->min_quantity,
                    'max_quantity' => $t->max_quantity,
                    'discount_type' => $t->discount_type,
                    'discount_value' => (float)$t->discount_value,
                    'discount_2' => $t->discount_2 ? (float)$t->discount_2 : null,
                    'discount_3' => $t->discount_3 ? (float)$t->discount_3 : null,
                    'rule_summary' => $t->rule_summary,
                    'tier_price' => $t->calculateUnitPrice($prodModel->price),
                ])->toArray();

            $generalTiers = empty($specificTiers)
                ? B2bPriceListItem::where('b2b_price_list_id', $priceList->id)
                    ->whereNull('b2b_product_id')
                    ->orderBy('min_quantity', 'asc')
                    ->get()
                    ->map(fn($t) => [
                        'min_quantity' => $t->min_quantity,
                        'max_quantity' => $t->max_quantity,
                        'discount_type' => $t->discount_type,
                        'discount_value' => (float)$t->discount_value,
                        'discount_2' => $t->discount_2 ? (float)$t->discount_2 : null,
                        'discount_3' => $t->discount_3 ? (float)$t->discount_3 : null,
                        'rule_summary' => $t->rule_summary,
                        'tier_price' => $t->calculateUnitPrice($prodModel->price),
                    ])->toArray()
                : [];

            $productsData[] = [
                'id' => $prodModel->id,
                'name' => $prodModel->name,
                'code' => $prodModel->code,
                'brand' => $prodModel->brand?->name,
                'price' => (float)$prodModel->price,
                'specific_tiers' => $specificTiers,
                'general_tiers' => $generalTiers,
            ];
        }

        return [
            'id' => $priceList->id,
            'name' => $priceList->name,
            'general_discount_percent' => (float)$priceList->general_discount_percent,
            'products' => $productsData,
        ];
    }

    public function snapshotCurrentData(): void
    {
        $this->loadMissing(['customer.priceList', 'agent', 'items.product.brand', 'items.variant']);
        $cust = $this->customer;
        $agent = $this->agent;

        $updates = [];
        if ($cust) {
            $updates['customer_business_name'] = $cust->business_name;
            $updates['customer_code'] = $cust->code;
            $updates['customer_vat_number'] = $cust->vat_number;
            $contactName = trim(($cust->contact_name ?? '') . ' ' . ($cust->contact_surname ?? ''));
            $updates['customer_contact_name'] = $contactName ?: null;
            $updates['customer_phone'] = $cust->phone;
            $updates['customer_email'] = $cust->email;
            $updates['customer_porto'] = $cust->porto;
            $updates['price_list_name'] = $cust->priceList?->name;
        }

        if ($agent) {
            $updates['agent_name'] = trim($agent->name . ' ' . $agent->surname);
        }

        $updates['price_list_snapshot'] = $this->buildPriceListSnapshot($cust);

        $this->update($updates);

        $orderTotalQty = (int) $this->items->sum('quantity');

        foreach ($this->items as $item) {
            $itemUpdates = [];
            $prod = $item->product;
            $variant = $item->variant;

            if ($prod) {
                if (empty($item->product_name)) $itemUpdates['product_name'] = $prod->name;
                if (empty($item->product_code)) $itemUpdates['product_code'] = $prod->code;
                if (empty($item->brand_name)) $itemUpdates['brand_name'] = $prod->brand?->name;
                if ($item->base_price === null) $itemUpdates['base_price'] = (float)$prod->price;

                if (empty($item->discount_rule_summary) && $cust) {
                    $details = $prod->getPriceDetailsForCustomer($cust, $orderTotalQty);
                    if (!empty($details['rule_summary'])) {
                        $itemUpdates['discount_rule_summary'] = $details['rule_summary'];
                        $itemUpdates['discount_type'] = $details['discount_type'] ?? null;
                        $itemUpdates['discount_value'] = $details['discount_value'] ?? null;
                    }
                }
            } else {
                if ($item->base_price === null) $itemUpdates['base_price'] = (float)$item->price;
            }

            if ($variant) {
                if (empty($item->variant_size)) $itemUpdates['variant_size'] = $variant->size;
                if (empty($item->variant_color)) $itemUpdates['variant_color'] = $variant->color;
            }

            if (!empty($itemUpdates)) {
                $item->update($itemUpdates);
            }
        }
    }
}
