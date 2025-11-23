<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use App\Models\Sale;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        // 'surname',
        'num_identificador',
        'email',
        'phone',
        'address',
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

    public function sale(){
        return $this->hasMany(Sale::class, 'id_customer');
    }

}
