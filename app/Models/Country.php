<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'country';
    protected $primaryKey = 'country_id';
    public $timestamps = false;

    protected $fillable = [
        'country_id',
        'country',
        'last_update',
    ];

    public function city()
    {
        return $this->hasMany(City::class, 'country_id', 'country_id');
    }
}
