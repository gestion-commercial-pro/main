<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sesion extends Model
{
    use HasFactory;

    protected $table = 'sesion';
    public const CREATED_AT = null;
    public const UPDATED_AT = null;


    public function commerciale()
    {
        return $this->belongsTo(commerciale::class);
    }
}
