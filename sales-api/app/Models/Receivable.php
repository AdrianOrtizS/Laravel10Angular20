<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Receivable extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_sale',
        'secuencial',
        'num_comprobante_abono',      //autoincrement
        'valor_abono',
        'observacion',
    ];

    public function setCreatedAtAttribute($value){
        date_default_timezone_set('America/Lima');
        $this->attributes['created_at'] = Carbon::now();
    }

    public function setUpdatedAtAttribute($value){
        date_default_timezone_set('America/Lima');
        $this->attributes['updated_at'] = Carbon::now();
    }

    public function sale()
    {                                        
        return $this->belongsTo(Sale::class, 'id_sale');
    }


    public static function generarNumeroReceived()
    {
        $establecimiento = '001';
        $punto = '001';

        // Obtener último secuencial
        $ultimo = static::query()->max('secuencial') ?? 0;
        // $ultimo = self::max('secuencial');
        
        $nuevoSecuencial = $ultimo ? $ultimo + 1 : 1;
        
        // Formato con ceros a la izquierda
        $numero = str_pad($nuevoSecuencial, 9, '0', STR_PAD_LEFT);
        
        return [
            'secuencial'    => $nuevoSecuencial,
            'numero_ceros'  => $numero,
            'numero_cobro' => "$establecimiento-$punto-$numero"
        ];
    }


    // public function products(){                                        
    //     //table categorie
    //     return $this->hasMany(Product::class, 'id');
    // }

}
