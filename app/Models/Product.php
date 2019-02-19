<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_name' ,
        'product_image',
        'regular_price' ,
        'sale_price' ,
        'stock' ,
        'product_sku' ,
        'user_id'
    ];

    // public function getProductImageAttribute($value){
    //     return 'mypath/'.$value;
    // }
}
