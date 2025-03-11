<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';
    protected $primaryKey = 'staff_id';
    public $timestamps = false;

    protected $fillable = [
        'staff_id',
        'first_name',
        'last_name',
        'address_id',
        'picture',
        'email',
        'store_id',
        'active',
        'username',
        'password',
        'last_update',
    ];

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'address_id');
    }
    public function store()
    {
        return $this->hasOne(Store::class, 'manager_staff_id', 'staff_id');
    }
    public function payment()
    {
        return $this->hasMany(Payment::class, 'staff_id', 'staff_id');
    }
    public function rental()
    {
        return $this->hasMany(Rental::class, 'staff_id', 'staff_id');
    }
}
