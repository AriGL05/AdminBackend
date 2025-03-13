<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $table = 'address';
    protected $primaryKey = 'address_id';
    public $timestamps = false;

    protected $fillable = [
        'address_id',
        'address',
        'address2',
        'district',
        'city_id',
        'postal_code',
        'phone',
        'last_update',
    ];
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'city_id');
    }
    public function customer()
    {
        return $this->hasMany(Customer::class, 'address_id', 'address_id');
    }
    public function staff()
    {
        return $this->hasMany(Staff::class, 'address_id', 'address_id');
    }
    public function store()
    {
        return $this->hasMany(Store::class, 'address_id', 'address_id');
    }
}
