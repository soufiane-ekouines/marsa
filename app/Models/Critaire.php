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
    ];
}
