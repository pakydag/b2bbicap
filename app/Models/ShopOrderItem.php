<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ShopOrder;
use App\Models\ShopVariant;

class ShopOrderItem extends Model
{
    protected $fillable = [
        'shop_order_id', 'shop_variant_id', 'nome_prodotto', 'sku', 'colore', 'taglia',
        'quantita', 'prezzo_unitario', 'subtotale'
    ];

    public function order()
    {
        return $this->belongsTo(ShopOrder::class, 'shop_order_id');
    }

    public function variant()
    {
        return $this->belongsTo(ShopVariant::class, 'shop_variant_id');
    }

    public function getImageAttribute()
    {
        if ($this->variant && !empty($this->variant->foto)) {
            return $this->variant->foto;
        }

        if ($this->variant && $this->variant->product && !empty($this->variant->product->foto_aggiuntive)) {
            $photos = $this->variant->product->foto_aggiuntive;
            return is_array($photos) ? ($photos[0] ?? null) : $photos;
        }

        if (!empty($this->sku) || !empty($this->nome_prodotto)) {
            $b2b = \App\Models\B2bProduct::where(function ($q) {
                if ($this->sku) {
                    $q->where('code', $this->sku);
                }
                if ($this->nome_prodotto) {
                    $q->orWhere('name', $this->nome_prodotto);
                }
            })->first();

            if ($b2b && !empty($b2b->image)) {
                return $b2b->image;
            }

            $article = \App\Models\Article::where('titolo', 'like', '%' . $this->nome_prodotto . '%')->first();
            if ($article && !empty($article->foto)) {
                return asset('storage/' . $article->foto);
            }
        }

        return null;
    }
}
