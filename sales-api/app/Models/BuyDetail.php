<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Supplier;
use App\Models\Buy;
use App\Models\Product;

class BuyDetail extends Model
{
    protected $fillable = [
        'id_buy',
        'id_product',
        'quantity',
        'price',
        'subtotal',
        'iva',
        'ice'
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'subtotal'  => 'decimal:2'
    ];

    public function Buy()
    {                                        
        return $this->belongsTo(Buy::class, 'id_buy');
    }

    public function product()
    {                                           
        return $this->belongsTo(Product::class, 'id_product');
    }

}
