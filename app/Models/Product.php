<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
      'name',
      'description',
      'price',
      'categories_id',
      'tags',
    ];

    // Relation of galleries
    public function galleries(){
      return $this->hasMany(ProductGallery::class, 'products_id', 'id');
    }

    // Relation of category
    public function category(){
      return $this->belongsTo(ProductCategory::class, 'categories_id', 'id');
    }
}
