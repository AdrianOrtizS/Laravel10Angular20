<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Pay extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        'num_comprobante_abono',
        'valor_abono',
        'imagen',
        'id_buy',
        'state'
    ];

    public function setCreatedAtAttribute($value){
        date_default_timezone_set('America/Lima');
        $this->attributes['created_at'] = Carbon::now();
    }

    public function setUpdatedAtAttribute($value){
        date_default_timezone_set('America/Lima');
        $this->attributes['updated_at'] = Carbon::now();
    }

    public function buy()
    {                                        
        return $this->belongsTo(Buy::class, 'id_buy');
    }

    // public function products(){                                        
    //     //table categorie
    //     return $this->hasMany(Product::class, 'id');
    // }

}
