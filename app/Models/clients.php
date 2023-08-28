<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clients extends Model
{
    use HasFactory;

    protected $table = 'clients';
    public const CREATED_AT = "CREATED_AT";
    public const UPDATED_AT = "UPDATED_AT";
    protected $primaryKey = 'id';







}
