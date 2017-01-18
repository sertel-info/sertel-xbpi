<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ComentarioGravacao extends Model
{
    
    protected $table = 'coment_gravacao';

    protected $fillable = [
                'txt',
                'tempo',
                'gravacao',
                'dono'
     ];

 }
