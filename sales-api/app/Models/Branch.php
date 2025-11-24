<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Branch extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'num_establecimiento',    //primer 001
        'phone',
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

    public function products()
    {
        return $this->belongsToMany(Product::class, 'inventaries', 'id_branch', 'id_product')
                    ->withPivot(['quantity', 'stock_min'])
                    ->withTimestamps();
    }

    public function pointsOfSale()
    {
        return $this->hasMany(PointOfSale::class, 'id_branch');
    }

    // public function users()
    // {
    //     return $this->hasMany(User::class, 'id_branch');
    // }
}
