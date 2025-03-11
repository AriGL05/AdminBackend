<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'category';
    protected $primaryKey = 'category_id';
    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'name',
        'last_update',
    ];

    public function film_Category()
    {
        return $this->hasMany(Film_Category::class, 'category_id', 'category_id');
    }
}
