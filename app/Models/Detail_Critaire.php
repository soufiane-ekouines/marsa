<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail_Critaire extends Model
{
    use HasFactory;
    protected $table = "Detail_Critaires";
    protected $fillable = [
        'famille_enjin_id',
        'critaire_id',
        'Commentaire',
    ];
    function critaire() {
        return $this->belongsTo(critaire::class);
    }
}
