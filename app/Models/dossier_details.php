<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dossier_details extends Model
{
    use HasFactory;

    protected $table = 'dossier_details';
    public const CREATED_AT = null;
    public const UPDATED_AT = null;
   // protected $primaryKey = 'id';



   public function dossier()
   {
       return $this->belongsTo(dossier::class);
   }


   public function forfait()
   {
       return $this->belongsTo(forfait::class);
   }

}
