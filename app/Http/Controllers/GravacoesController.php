<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Gravacoes;
use App\Models\ComentarioGravacao;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class GravacoesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {   
        $this->entity  = new Gravacoes;
        $this->middleware('auth');
    }

    public function index(){
        return view('admin.gravacoes.index');
    }


    public function addComentario(Request $request){
        
        $comm = new ComentarioGravacao;
        
        $res = $comm->create([
            'txt'=>$request->com_txt,
            'tempo'=>$request->com_tempo,
            'dono'=>$request->com_dono,
            'gravacao'=>$request->com_gravacao
        ]);

        if($res){
            echo $res->id;
            return;
        }
        
        echo 0;
        return;
    }

    public function removeComentario(Request $request){

        $comm = new ComentarioGravacao;

        $comentario = $comm->find($request->com_id);

        try{ 

            if(Auth::user()->id == $comentario->dono){
                $res = $comentario->delete();
            }
            else {
                echo -1;
                return;
            }
        
        } catch (\Exception $e) {
            echo 0;
            return;
        }

        if($res){
            echo 1;
            return;
        }
        
        echo 0;
        return;
    }

    public function getGravacao(Request $request){

         $cdr = $this->entity->find($request->grav_id);
         $caminho_array = explode('/', $cdr->audio);
         $nome = end($caminho_array);
         return response()->download($cdr->audio, $nome);
    }

}
