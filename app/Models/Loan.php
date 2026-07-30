<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'amount',
        'interest_rate', // Annual or monthly rate, typically annual
        'term_months',
        'amortization_system', // 'frances', 'aleman', 'americano'
        'status', // 'active', 'paid', 'defaulted'
        'start_date',
        'guarantee_type',
        'guarantee_details',
        'contract_type',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function amortizations()
    {
        return $this->hasMany(Amortization::class);
    }
}
