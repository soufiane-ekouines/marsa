<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Famille_Enjin extends Model
{
    use HasFactory;
    protected $table = "Famille_Enjins";
    protected $fillable = ['Nom_famille'];
}
