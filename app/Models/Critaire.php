<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Critaire extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'Klaxon',
        'Essuie_glase',
        'Frein',
        'Pneu',
        'Pare_Brise',
        'detail_enjin_id',
        'commentaireK',
        'commentairee',
        'commentairef',
        'commentairepn',
        'commentairepa',
    ];
    function dettail_engin()  {
        return $this->belongsTo(Detail_Enjin::class,'detail_enjin_id');
    }
}
