<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paiement extends Model
{
    use HasFactory;

    protected $table = 'paiement';
    public const CREATED_AT = null;
    public const UPDATED_AT = null;
    protected $primaryKey = 'id';



    public function commerciale()
    {
        return $this->belongsTo(commerciale::class);
    }

    public function paiement_details()
    {
        return $this->hasMany(paiement_details::class);
    }

    public function dossier()
    {
        return $this->hasManyThrough(dossier::class,paiement_details::class);
    }


}
