<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paiement_details extends Model
{
    use HasFactory;

    protected $table = 'paiement_details';
    public const CREATED_AT = null;
    public const UPDATED_AT = null;
   // protected $primaryKey = 'id';

   public function paiement()
    {
        return $this->belongsTo(paiement::class);
    }

    public function dossier()
    {
        return $this->belongsTo(dossier::class);
    }
}
