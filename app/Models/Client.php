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

    public function getPhotoUrlAttribute()
    {
        if (!$this->photo_path) {
            return null;
        }

        if (str_starts_with($this->photo_path, 'http')) {
            return $this->photo_path;
        }

        return asset($this->photo_path);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
