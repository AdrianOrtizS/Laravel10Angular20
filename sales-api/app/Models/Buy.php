<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use App\Models\Supplier;
use App\Models\BuyDetail;
use App\Models\Pay;

class Buy extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        'id_supplier', 
        'fecha_ingreso',
        'numero_factura',
        'type_pay',
        // 'id_branch',
        'id_point_of_sale',
        'type_doc',
        'subtotal',
        'iva',
        'iva0',
        'total',
        'state',
    ];

    public function setCreatedAtAttribute($value){
        date_default_timezone_set('America/Lima');
        $this->attributes['created_at'] = Carbon::now();
    }

    public function setUpdatedAtAttribute($value){
        date_default_timezone_set('America/Lima');
        $this->attributes['updated_at'] = Carbon::now();
    }

    // public function branch()  //sucursal
    // {                                           
    //     return $this->belongsTo(Branch::class,'id_branch');
    // }

    public function point_of_sale()  //punto de venta  -  sucursal
    {                                           
        return $this->belongsTo(PointOfSale::class,'id_point_of_sale');
    }

    public function supplier()  //proveedor
    {                                           
        return $this->belongsTo(Supplier::class,'id_supplier');
    }

    public function details()
    {                                             
        return $this->hasMany(BuyDetail::class, 'id_buy');
    }

    public function pays()
    {                                             
        return $this->hasMany(Pay::class, 'id_buy');
    }

    public function scopeFilterBuy($query, $search)
    {
        $query->where(function ($q) use ($search) {
        $q->where('numero_factura', 'like', "%{$search}%")
          ->orWhereHas('supplier', function ($q2) use ($search) {
              $q2->where('name', 'like', "%{$search}%")
                 ->orWhere('num_identificador', 'like', "%{$search}%");
          });
        });

        return $query;
    }
}
