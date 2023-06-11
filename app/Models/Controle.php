<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Controle extends Model
{
    use HasFactory;
    protected $fillable = [
        'detail_critaire_id',
        'detail_enjin_id',
        'confirmation',
    ];
}
