<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCoupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'valore',
        'min_spesa',
        'sempre_attivo',
        'attivo',
        'utilizzi',
        'usato_il',
    ];

    protected $casts = [
        'valore' => 'float',
        'min_spesa' => 'float',
        'sempre_attivo' => 'boolean',
        'attivo' => 'boolean',
        'utilizzi' => 'integer',
        'usato_il' => 'datetime',
    ];

    /**
     * Verifica se il coupon è utilizzabile per un dato subtotale.
     */
    public function isUsable(float $subtotal = 0): array
    {
        $isEn = app()->getLocale() === 'en';

        if (!$this->attivo) {
            return [
                'valid' => false,
                'message' => $isEn ? 'This coupon is no longer active.' : 'Questo coupon non è più attivo.',
            ];
        }

        // Se non è sempre attivo e ha già 1 o più utilizzi
        if (!$this->sempre_attivo && $this->utilizzi >= 1) {
            return [
                'valid' => false,
                'message' => $isEn ? 'This coupon has already been used and cannot be reused.' : 'Questo coupon è già stato utilizzato e non è più valido.',
            ];
        }

        if ($this->min_spesa > 0 && $subtotal < $this->min_spesa) {
            $formattedMin = number_format($this->min_spesa, 2, ',', '.');
            return [
                'valid' => false,
                'message' => $isEn 
                    ? "This coupon requires a minimum spend of € {$formattedMin}."
                    : "Questo coupon richiede una spesa minima di € {$formattedMin}.",
            ];
        }

        return [
            'valid' => true,
            'message' => $isEn ? 'Coupon applied successfully!' : 'Coupon applicato con successo!',
        ];
    }

    /**
     * Calcola l'importo dello sconto su un dato subtotale.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0.0;
        }

        if ($this->type === 'percentuale') {
            $discount = ($subtotal * $this->valore) / 100.0;
        } else {
            $discount = (float)$this->valore;
        }

        // Lo sconto non può superare il totale imponibile dei prodotti
        return min((float)$subtotal, max(0.0, round($discount, 2)));
    }

    /**
     * Registra l'utilizzo del coupon.
     */
    public function recordUsage(): void
    {
        $this->utilizzi += 1;
        $this->usato_il = now();

        // Se è mono-uso (non sempre attivo), disattivalo subito
        if (!$this->sempre_attivo) {
            $this->attivo = false;
        }

        $this->save();
    }
}
