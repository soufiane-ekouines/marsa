<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrer extends Model
{
    use HasFactory;
    protected $table = 'entrer';

    protected $fillable = [
        'matricle',
        'societe',
        'nom',
        'prenom',
        'compteur',
        'observation',
        'engin_id',
        'Critaire_id'
    ];

}
