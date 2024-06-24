<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'is_active',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($question) {
    //         $question->slug = Str::slug($question->name);
    //     });
    // }
}
