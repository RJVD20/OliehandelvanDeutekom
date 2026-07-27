<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'provider',
        'provider_payment_id',
        'status',
        'amount',
        'currency',
        'due_date',
        'paid_at',
        'pay_link',
        'meta',
    ];

    protected $casts = [
        'status'           => PaymentStatus::class,
        'due_date'         => 'date',
        'paid_at'          => 'datetime',
        'meta'             => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(PaymentEvent::class);
    }

    public function handling(): string
    {
        $handling = $this->meta['handling'] ?? null;

        if (is_string($handling) && $handling !== '') {
            return $handling;
        }

        return $this->pay_link ? 'payment_link' : ($this->provider === 'manual' ? 'manual' : 'online');
    }

    public function handlingLabel(): string
    {
        return match ($this->handling()) {
            'payment_link', 'online' => 'Online betaallink',
            'pay_on_delivery' => 'Betalen bij levering',
            'bank_transfer' => 'Bankoverschrijving',
            'paid_cash' => 'Contant betaald',
            'paid_bank' => 'Per bank betaald',
            'paid' => 'Al betaald',
            default => 'Handmatige betaling',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            PaymentStatus::OPEN => 'Openstaand',
            PaymentStatus::PAID => 'Betaald',
            PaymentStatus::EXPIRED => 'Verlopen',
            PaymentStatus::FAILED => 'Mislukt',
            PaymentStatus::CANCELLED => 'Geannuleerd',
        };
    }

    public function canSendManualPaymentRequest(): bool
    {
        return $this->status === PaymentStatus::OPEN
            && $this->order?->source === 'manual'
            && filled($this->order->email)
            && filled($this->pay_link);
    }

}
