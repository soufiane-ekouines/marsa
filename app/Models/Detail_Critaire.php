<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail_Critaire extends Model
{
    use HasFactory;
    protected $fillable = [
        'famille_enjin_id',
        'critaire_id',
        'Commentaire',
    ];
}
