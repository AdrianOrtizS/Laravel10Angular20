<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use App\Models\Customer;
use App\Models\SaleDetail;
use App\Models\Branch;
use App\Models\Receivable;

class Sale extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id_customer', 
        // 'num_venta',
        'subtotal',
        'iva',
        'total',
        'discount',
        'type_receivable',
        'state',
        // 'id_branch',
        'id_point_of_sale',
        'establecimiento',
        'punto_emision',
        'secuencial',
        'numero_factura',
        'clave_acceso'
        // 'notes'
    ];

    public function setCreatedAtAttribute($value){
        date_default_timezone_set('America/Lima');
        $this->attributes['created_at'] = Carbon::now();
    }

    public function setUpdatedAtAttribute($value){
        date_default_timezone_set('America/Lima');
        $this->attributes['updated_at'] = Carbon::now();
    }
    
    protected $casts = [
        // 'date' => 'date',
        'total'     => 'decimal:2',
        'subtotal'  => 'decimal:2',
        'iva'       => 'decimal:2',
        'discount'  => 'decimal:2'
    ];


    public static function generarNumeroFactura()
    {
        $establecimiento = '001';
        $punto = '001';

        // Obtener último secuencial
        $ultimo = static::query()->max('secuencial') ?? 0;
        
        $nuevoSecuencial = $ultimo ? $ultimo + 1 : 1;
        
        // Formato con ceros a la izquierda
        $numero = str_pad($nuevoSecuencial, 9, '0', STR_PAD_LEFT);
        
        return [
            'secuencial'    => $nuevoSecuencial,
            'numero_ceros'  => $numero,
            'numero_factura' => "$establecimiento-$punto-$numero"
        ];
    }

    // public function branch()  //sucursal
    // {                                           
    //     return $this->belongsTo(Branch::class,'id_branch');
    // }
    
    public function point_of_sale()  //punto de venta  -  sucursal
    {                                           
        return $this->belongsTo(PointOfSale::class,'id_point_of_sale');
    }

    public function customer()
    {                                           
        return $this->belongsTo(Customer::class,'id_customer');
    }

    public function details()
    {                                             
        return $this->hasMany(SaleDetail::class, 'id_sale');
    }

    public function receivables()
    {                                             
        return $this->hasMany(Receivable::class, 'id_sale');
    }

    public function scopeFilterSale($query, $search)
    {
        $query->where(function ($q) use ($search) {
            $q->where('numero_factura', 'like', "%{$search}%")
              ->orWhereHas('customer', function ($q2) use ($search) {
                  $q2->where('name', 'like', "%{$search}%")
                     ->orWhere('num_identificador', 'like', "%{$search}%");
              });
        });


        return $query;
    }


}
