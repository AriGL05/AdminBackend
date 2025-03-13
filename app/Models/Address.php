<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'address';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'address_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address',
        'address2',
        'district',
        'city_id',
        'postal_code',
        'phone',
        'last_update'
    ];

    /**
     * Get the city that owns the address.
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * Get the customers for the address.
     */
    public function customer()
    {
        return $this->hasMany(Customer::class, 'address_id', 'address_id');
    }

    /**
     * Get the staff for the address.
     */
    public function staff()
    {
        return $this->hasMany(Staff::class, 'address_id', 'address_id');
    }

    /**
     * Get the stores for the address.
     */
    public function store()
    {
        return $this->hasMany(Store::class, 'address_id', 'address_id');
    }
}
