<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class B2bProduct extends Model
{
    protected $fillable = ['name', 'code', 'b2b_brand_id', 'season', 'description', 'image', 'has_stock', 'is_active', 'price', 'characteristics'];

    protected $casts = [
        'characteristics' => 'array',
    ];

    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return asset('storage/logo/logo-bicap.png');
        }

        if (\Illuminate\Support\Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        if (file_exists(public_path('storage/' . $this->image))) {
            return asset('storage/' . $this->image);
        }

        if (file_exists(public_path($this->image))) {
            return asset($this->image);
        }

        return asset('storage/' . $this->image);
    }

    public function brand()
    {
        return $this->belongsTo(B2bBrand::class, 'b2b_brand_id');
    }

    public function variants()
    {
        return $this->hasMany(B2bProductVariant::class, 'b2b_product_id');
    }

    public function priceListItems()
    {
        return $this->hasMany(B2bPriceListItem::class, 'b2b_product_id');
    }

    /**
     * Ritorna i dettagli del prezzo calcolato per un dato cliente e quantità.
     */
    public function getPriceDetailsForCustomer($customer, int $quantity = 1): array
    {
        $basePrice = (float) $this->price;
        $result = [
            'unit_price' => $basePrice,
            'original_price' => $basePrice,
            'discount_type' => null,
            'discount_value' => 0,
            'discount_2' => null,
            'discount_3' => null,
            'tier' => null,
            'is_product_exception' => false,
            'price_list' => null,
            'price_list_name' => null,
            'rule_summary' => null,
        ];

        if (!$customer) {
            return $result;
        }

        $priceListId = $customer->b2b_price_list_id;
        if (!$priceListId) {
            return $result;
        }

        $priceList = B2bPriceList::find($priceListId);
        if (!$priceList) {
            return $result;
        }

        $result['price_list'] = $priceList;
        $result['price_list_name'] = $priceList->name;

        // 1. Cerca prima fascia specifica valida per questo prodotto (con discount_value > 0)
        $tier = B2bPriceListItem::where('b2b_price_list_id', $priceList->id)
            ->where('b2b_product_id', $this->id)
            ->where('min_quantity', '<=', $quantity)
            ->where(function ($q) use ($quantity) {
                $q->whereNull('max_quantity')
                  ->orWhere('max_quantity', '>=', $quantity);
            })
            ->where('discount_value', '>', 0)
            ->orderBy('min_quantity', 'desc')
            ->first();

        $isProductException = false;
        if ($tier) {
            $isProductException = true;
        } else {
            // 2. Se non c'è una fascia specifica valida, cerca la fascia GENERALE per tutti i prodotti (b2b_product_id null)
            $tier = B2bPriceListItem::where('b2b_price_list_id', $priceList->id)
                ->whereNull('b2b_product_id')
                ->where('min_quantity', '<=', $quantity)
                ->where(function ($q) use ($quantity) {
                    $q->whereNull('max_quantity')
                      ->orWhere('max_quantity', '>=', $quantity);
                })
                ->where('discount_value', '>', 0)
                ->orderBy('min_quantity', 'desc')
                ->first();
        }

        if ($tier) {
            $result['tier'] = $tier;
            $result['is_product_exception'] = $isProductException;
            $result['rule_summary'] = $tier->rule_summary;
            $result['discount_type'] = $tier->discount_type;
            $result['discount_value'] = (float) $tier->discount_value;
            $result['discount_2'] = $tier->discount_2 ? (float) $tier->discount_2 : null;
            $result['discount_3'] = $tier->discount_3 ? (float) $tier->discount_3 : null;
            $result['unit_price'] = $tier->calculateUnitPrice($basePrice);
        } elseif ($priceList->general_discount_percent > 0) {
            $result['discount_type'] = 'percentage';
            $result['discount_value'] = (float) $priceList->general_discount_percent;
            $result['rule_summary'] = 'Sconto Listino: -' . floatval($priceList->general_discount_percent) . '%';
            $discountAmount = $basePrice * ($priceList->general_discount_percent / 100);
            $result['unit_price'] = max(0, round($basePrice - $discountAmount, 2));
        }

        return $result;
    }
}
