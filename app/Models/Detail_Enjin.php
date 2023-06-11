<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail_Enjin extends Model
{
    use HasFactory;
    protected $table = 'Detail_Enjins';
    protected $fillable = [
        'famille_enjin_id',
        'demande_id',
        'date_sortie',
        'date_entrer',
    ];
}
