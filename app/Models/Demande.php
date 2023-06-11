<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    use HasFactory;
    protected $fillable = [
        'date_embauche',
        'Shift',
        'Sortie_preveue',
        'entite_id',
        'user_id',
        'Commentaire',
        'etat'
    ];

    function user() {
        return $this->belongsTo(user::class);
    }
}
