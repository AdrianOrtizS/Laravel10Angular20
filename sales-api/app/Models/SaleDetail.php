<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Product;

class SaleDetail extends Model
{
     protected $fillable = [
        'id_sale',
        'id_product',
        'quantity',
        'price',
        'discount',
        'subtotal',
        'impuesto'
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'subtotal'  => 'decimal:2'
    ];

    public function Sale()
    {                                        
        return $this->belongsTo(Sale::class, 'id_sale');
    }

    public function product()
    {                                           
        return $this->belongsTo(Product::class, 'id_product');
    }

    
}
