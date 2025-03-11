<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film_Category extends Model
{
    use HasFactory;

    protected $table = 'film_category';
    protected $primaryKey = ['film_id', 'category_id'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'film_id',
        'category_id',
        'last_update',
    ];

    public function film()
    {
        return $this->belongsTo(Film::class, 'film_id', 'film_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
}
