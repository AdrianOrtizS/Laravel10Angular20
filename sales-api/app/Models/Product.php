<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'cod_pro',
        'name',
        'description',
        'price',
        'imagen',
        'state',
        'id_categorie',
        'tarifa_iva'
    ];

    public function setCreatedAtAttribute($value)
    {
        date_default_timezone_set('America/Lima');
        $this->attributes['created_at'] = Carbon::now();
    }

    public function setUpdatedAtAttribute($value)
    {
        date_default_timezone_set('America/Lima');
        $this->attributes['updated_at'] = Carbon::now();
    }

    public function categorie()
    {                                            //table products
        return $this->belongsTo(Categorie::class, 'id_categorie');
    }

    // public function tarifa_iva()
    // {                                            //table tarifas_iva
    //     return $this->belongsTo(Tarifa_iva::class, 'id_tarifa_iva');
    // }


    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'inventories', 'id_product', 'id_branch')
                    ->withPivot(['stock', 'stock_min'])
                    ->withTimestamps();
    }


    public function orderdetail()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function scopeFilterCategorieProduct($query, $search, $id_categorie, $id_branch)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('cod_pro', 'like', "%{$search}%");
            });
        }

        if ($id_categorie) {
            $query->where('id_categorie', $id_categorie);
        }

        if ($id_branch) {
            // many-to-many, filtra usando whereHas
            $query->whereHas('branches', function ($q) use ($id_branch) {
                $q->where('branches.id', $id_branch);
            });
        }

        return $query;
    }

}
