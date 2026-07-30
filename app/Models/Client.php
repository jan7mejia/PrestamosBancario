<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ci',
        'phone',
        'address',
        'latitude',
        'longitude',
        'photo_path',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
