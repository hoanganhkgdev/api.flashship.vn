<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
