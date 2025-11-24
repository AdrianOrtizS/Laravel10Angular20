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

// APP_NAME=Parabrisas_Libertadores
// APP_ENV=local
// APP_KEY=base64:h8PEWh+izb8JiG2C4SgxcrJ4HZoP2PskKl7XqJjc9AY=
// APP_DEBUG=true
// APP_URL=http://localhost:8000/
// URL_FRONT=http://localhost:4200/

// LOG_CHANNEL=stack
// LOG_DEPRECATIONS_CHANNEL=null
// LOG_LEVEL=debug

// DB_CONNECTION=mysql
// DB_HOST=127.0.0.1
// DB_PORT=3306
// DB_DATABASE=db_sales
// DB_USERNAME=root
// DB_PASSWORD=

// BROADCAST_DRIVER=log
// CACHE_DRIVER=file
// FILESYSTEM_DISK=local
// QUEUE_CONNECTION=sync
// SESSION_DRIVER=file
// SESSION_LIFETIME=120

// MEMCACHED_HOST=127.0.0.1

// REDIS_HOST=127.0.0.1
// REDIS_PASSWORD=null
// REDIS_PORT=6379

// MAIL_MAILER=smtp
// MAIL_HOST=smtp.gmail.com
// MAIL_PORT=465
// MAIL_USERNAME=vicadortiz@gmail.com
// MAIL_PASSWORD=podpjmaqpsljxmvw
// MAIL_ENCRYPTION=ssl
// MAIL_FROM_ADDRESS=vicadortiz@gmail.com
// MAIL_FROM_NAME="${APP_NAME}"

// TWILIO_SID=ACac7da5a7088b9ae9c869038396b8965f
// TWILIO_TOKEN=3383b719c8466b9a7dca1b778aae0eee
// TWILIO_FROM=whatsapp:+14155238886
            
// AWS_ACCESS_KEY_ID=
// AWS_SECRET_ACCESS_KEY=
// AWS_DEFAULT_REGION=us-east-1
// AWS_BUCKET=
// AWS_USE_PATH_STYLE_ENDPOINT=false

// PUSHER_APP_ID=
// PUSHER_APP_KEY=
// PUSHER_APP_SECRET=
// PUSHER_HOST=
// PUSHER_PORT=443
// PUSHER_SCHEME=https
// PUSHER_APP_CLUSTER=mt1

// VITE_APP_NAME="${APP_NAME}"
// VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
// VITE_PUSHER_HOST="${PUSHER_HOST}"
// VITE_PUSHER_PORT="${PUSHER_PORT}"
// VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
// VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

// SRI_AMBIENTE=1
// CERTIFICADO_CLAVE=123456

// JWT_SECRET=1E9POhctXA56phSlqy3s2kWUZz35foNu3klYneh6KJIBXwuD6SWPdb2JJF71gqi2

}
