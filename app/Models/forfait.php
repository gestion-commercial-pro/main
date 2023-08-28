<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class forfait extends Model
{
    use HasFactory;

    protected $table = 'forfait';
    public const CREATED_AT = null;
    public const UPDATED_AT = null;
    protected $primaryKey = 'id';


    public function operateur()
    {
        return $this->belongsTo(operateur::class);
    }

}
