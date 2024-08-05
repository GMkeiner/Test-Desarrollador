<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = ['nombre','email','telefono'];

    public function direcciones()
    {
        return $this->hasMany(Direccion::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
