<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail_Demande extends Model
{
    use HasFactory;
    protected $table="Detail_Demandes";
    protected $fillable = [
        'famille_enjin_id',
        'demande_id',
        'Description',
        'qte',
    ];

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }

    public function familleEnjin()
    {
        return $this->belongsTo(Famille_Enjin::class);
    }

    public function detailEnjin()
    {
        return $this->belongsTo(Detail_Enjin::class);
    }
}
