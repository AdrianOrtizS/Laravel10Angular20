<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Inventory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id_branch',
        'id_product',
        'stock',
        'stock_min'
    ];

    public function setCreatedAtAttribute($value){
        date_default_timezone_set('America/Lima');
        $this->attributes['created_at'] = Carbon::now();
    }

    public function setUpdatedAtAttribute($value){
        date_default_timezone_set('America/Lima');
        $this->attributes['updated_at'] = Carbon::now();
    }


    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'id_branch');
    }

}
