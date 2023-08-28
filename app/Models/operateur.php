<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class operateur extends Model
{
    use HasFactory;

    protected $table = 'operateur';
    public const CREATED_AT = null;
    public const UPDATED_AT = null;
    protected $primaryKey = 'id';




    public function forfait()
    {
        return $this->hasMany(forfait::class);
    }




}
