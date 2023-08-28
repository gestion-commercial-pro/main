<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dossier extends Model
{
    use HasFactory;

    protected $table = 'dossier';
    public const CREATED_AT = null;
    public const UPDATED_AT = null;
    protected $primaryKey = 'id';



   public function commerciale()
   {
       return $this->belongsTo(commerciale::class);
   }

   public function clients()
   {
       return $this->belongsTo(clients::class);
   }

   public function dossier_details()
   {
       return $this->hasMany(dossier_details::class);
   }

   public function admin()
   {
       return $this->belongsTo(Admin::class);
   }

   public function forait()
   {
       return $this->hasManyThrough(forfait::class,dossier_details::class);
   }

   public function statu()
   {
       return $this->belongsTo(statu::class);
   }



}
