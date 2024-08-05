<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $fillable = ['cliente_id','direccion'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
