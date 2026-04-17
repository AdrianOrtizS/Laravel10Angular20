<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class PointsOfSale extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id_branch',
        'codigo_punto_emision',
        'secuencial_actual',
        'secuencial_actual_receivable',
        'descripcion',
    ];

    public function setCreatedAtAttribute($value){
        date_default_timezone_set('America/Lima');
        $this->attributes['created_at'] = Carbon::now();
    }

    public function setUpdatedAtAttribute($value){
        date_default_timezone_set('America/Lima');
        $this->attributes['updated_at'] = Carbon::now();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'id_branch');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'id_point_of_sale');
    }
}
