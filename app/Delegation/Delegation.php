<?php

namespace App\Delegation;

use Cknow\Money\Casts\MoneyIntegerCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperDelegation
 */
class Delegation extends Model
{
    use HasFactory;

    protected $fillable = ['start', 'end', 'worker_id', 'country'];

    protected $casts = [
        'amount_due' => MoneyIntegerCast::class.':currency',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function toArray(): array
    {
        return [
            'start' => $this->start->format('Y-m-d H:i:s'),
            'end' => $this->end->format('Y-m-d H:i:s'),
            'country' => $this->country,
            'amount_due' => $this->amount_due->formatByDecimal(),
            'currency' => $this->currency
        ];
    }
}
