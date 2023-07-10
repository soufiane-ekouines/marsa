<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail_Enjin extends Model
{
    use HasFactory;
    protected $table = 'Detail_Enjins';
    protected $fillable = [
        'enjin_id',
        'demande_id',
        'date_sortie',
        'date_entrer',
        'user_id'
    ];

    function demande()  {
        return $this->belongsTo(demande::class,"demande_id");
    }

    function enjin()  {
        return $this->belongsTo(Enjin::class);
    }

    function Conducteur()  {
        return $this->belongsTo(User::class);
    }

    function Critaire()  {
        return $this->hasOne(Critaire::class,'detail_enjin_id');
        
    }
}
