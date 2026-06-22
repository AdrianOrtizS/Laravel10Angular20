<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Tarifa_ice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'codigo',            //     3 -> ICE  -  5 -> IRBPNR
        'codigo_porcentaje', //  3041 -> Perfumes 20%
        'descripcion',       //  Perfumes y aguas de tocador
        'tipo',              //  porcentaje -  especifico  -  mixto
        'tarifa',            //  5,00%      -  0,18$       -  0,16
        'unidad',            //             -  unidad      -  litro
        'estado'
    ];

    public function setCreatedAtAttribute($value){
        date_default_timezone_set('America/Lima');
        $this->attributes['created_at'] = Carbon::now();
    }

    public function setUpdatedAtAttribute($value){
        date_default_timezone_set('America/Lima');
        $this->attributes['updated_at'] = Carbon::now();
    }

    // public function products(){                                        
    //     //table categorie
    //     return $this->hasMany(Product::class, 'id');
    // }

}
