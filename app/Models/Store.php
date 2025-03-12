<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $table = 'store';
    protected $primaryKey = 'store_id';
    public $timestamps = false;

    protected $fillable = [
        'store_id',
        'manager_staff_id',
        'address_id',
        'last_update',
    ];

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'address_id');
    }
    public function staff()
    {
        return $this->hasOne(Staff::class, 'staff_id', 'manager_staff_id');
    }
    public function customer()
    {
        return $this->hasMany(Customer::class, 'store_id', 'store_id');
    }
    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'store_id', 'store_id');
    }
    public function payment()
    {
        return $this->hasManyThrough(Payment::class, Customer::class, 'store_id', 'customer_id', 'store_id', 'customer_id');
    }
}
