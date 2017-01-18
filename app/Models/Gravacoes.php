<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Gravacoes extends Model
{
    
    protected $table = 'cdr';

    protected $fillable = [
                "id",
				"src",
				"ddr",
				"dst",
				"atendedor",
				"dcontext",
				"clid",
				"channel",
				"dstchannel",
				"lastapp",
				"lastdata",
				"calldate",
				"dataHora",
				"start",
				"answer",
				"end",
				"duration",
				"billsec",
				"disposition",
				"amaflags",
				"linkedid",
				"uniqueid",
				"peeraccount",
				"sequence",
				"nome",
				"dnid",
				"comentario"
     ];

 }
