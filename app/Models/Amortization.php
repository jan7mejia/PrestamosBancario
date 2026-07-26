<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amortization extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'installment_number',
        'due_date',
        'installment_amount', // Cuota total
        'principal_amount',   // Amortización de capital
        'interest_amount',    // Interés
        'remaining_balance',  // Saldo residual
        'status',             // 'pending', 'paid', 'late'
        'paid_at',            // Fecha real de pago
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at'  => 'datetime',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
