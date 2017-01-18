<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Ramal;
use App\Models\Grupos;
use App\Models\ProfileRamal;
use App\Models\RamalSetting;
use App\Http\Controllers\DatatablesController;
use Response;
use DB;

class RamalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
  public function __construct(Ramal $entity, ProfileRamal $profileRamal){

        $this->entity = new $entity;
        $this->profileRamal = new $profileRamal;
        $this->middleware('auth');

  }

  public function index(){
      $fxs_ports = $this->getFxsPorts();
      return view('admin.ramais.index', ['fxs_ports'=>$fxs_ports]);
  }
  

  public function create(){       
      return view('admin.ramais.create', compact ('name'));
  }

  public function store(Request $request){

      $ramal = $this->entity->create([         
          'tipo' => $request->tipo,
          'nome' => $request->nome,
          'tecnologia' => $request->tec,
          'aplicacao' => $request->app,
          'ddr' => $request->ddr,
          'no_externo' => $request->no_externo,
          'numero' => $request->number,
          'senha' => $request->password,
          'capturar' => $request->capture,
          'estacionar_chamadas' => $request->parking_calls,
          'desvio_tipo' => $request->deviation,
          'desvio_para' => $request->detour,
          'nao_perturbe' => $request->notdisturb,
          'conferencia' => $request->conference,
          'codigo_conta' => $request->accountcode,
          'central' => $request->central,
          'centro_custo' => $request->centercost,
          'acesso_porteiro' => $request->intercomaccess,
          'nat'=> $request->nat,
          'porta' => $request->fxs_port,
          'profile_ramal_id'=>$request->profile
      ]);
      
      /*if($ramal->tipo == 39 && in_array($ramal->app, [111,116]))
      {
        dd($request->profile);
        $ramal->update([
            'profile_ramal_id' => $request->profile
        ]);
      }*/

      $this->atualizaArquivo();        

     return redirect()->route('admin.ramais.index');
  }
     

  public function edit($id){
        
        //$masters = RamalSetting::getMasters();
        $entity = $this->entity->find($id);
        //$profiles = $this->profileRamal->to_form_select('id');
    
        echo (json_encode($this->entity->find($id)));
       
        //return view('admin.ramais.edit', compact('entity', 'profiles','masters'));
  }


  public function update(Request $request, $id) {
     
       $ramal = $this->entity->find($id);
       
       $ramal->update([
            'tipo' => $request->tipo,
            'nome' => $request->nome,
            'tecnologia' => $request->tec,
            'aplicacao' => $request->app,
            'ddr' => $request->ddr,
            'no_externo' => $request->no_externo,
            'numero' => $request->number,
            'senha' => $request->password,
            'capturar' => $request->capture,
            'estacionar_chamadas' => $request->parking_calls,
            'desvio_tipo' => $request->deviation,
            'desvio_para' => $request->detour,
            'nao_perturbe' => $request->notdisturb,
            'conferencia' => $request->conference,
            'codigo_conta' => $request->accountcode,
            'central' => $request->central,
            'centro_custo' => $request->centercost,
            'acesso_porteiro' => $request->intercomaccess,
            'nat'=> $request->nat,
            'porta' => $request->fxs_port,
            'profile_ramal_id' =>$request->profile
        ]);

        
       $this->atualizaArquivo();        
        
       Session::flash('message_type', 'success');
       Session::flash('message_text', 'Ramal atualizado com sucesso!');

       return redirect()->route('admin.ramais.index');
  }


  public function destroy(){


        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $status = $this->entity->find($id)->delete();  
        
        $this->atualizaArquivo();
        //deleta os ramais dos grupos
        $grupos = new Grupos;
        $todos_grupos = $grupos->all();
        
        foreach ($todos_grupos as $grupo){
          $ramais = explode(',',$grupo->ramais);
          if( ($item = array_search($id, $ramais)) !== FALSE ){
             unset($ramais[$item]);
             $grupo->update(['ramais' => implode(',',$ramais) ]);
          }  
        }

        return json_encode(['status'=>$status]);
       
  }   
  
  //pega os atributos do perfil que vão no arquivo
  public function getGrupos($profileid){ 
    
    $grupos = $this->profileRamal->where("id", '=', $profileid)
                                 ->select('grupo_captura', 'captura_grupos')
                                 ->first();


    if(!$grupos){
      return 0;
    }

    $txt = 'pickupgroup='.$grupos->grupo_captura.chr(13).chr(10).
           'callgroup='.$grupos->captura_grupos.chr(13).chr(10).chr(13).chr(10);
    
    return (Object)["grupos"=>$grupos, "txt"=>$txt];
  }


  public function escreveSip($ramal){
    $handle = fopen("/etc/asterisk/sip_ramais.conf", "a");
    $grupos = isset($ramal->profile_ramal_id) ? $this->getGrupos($ramal->profile_ramal_id) : null;
     
    fwrite($handle, chr(13).chr(10).'[' .$ramal->numero. ']'.'(template)' .chr(13).chr(10).
                                 'username='.  $ramal->numero  .chr(13).chr(10).
                                 'secret='.    $ramal->senha   .chr(13).chr(10).
                                 'nat='.       ($ramal->nat == 1 ? 'yes' : 'no')   .chr(13).chr(10).
                                 'callerid="'. $ramal->nome.'" '. '<'. $ramal->numero .'>'.chr(13).chr(10).
                                 ($grupos ? $grupos->txt : '')
     );

    fclose($handle);
  }


  public function escreveIax($ramal){
    $handle = fopen("/etc/asterisk/iax_ramais.conf", "a");
    $grupos = isset($ramal->profile_ramal_id) ? $this->getGrupos($ramal->profile_ramal_id) : null;

    fwrite($handle, chr(13).chr(10).'[' .$ramal->numero. ']'.'(template)' .chr(13).chr(10).
                                 'username='.  $ramal->numero  .chr(13).chr(10).
                                 'secret='.    $ramal->senha   .chr(13).chr(10).
                                 'nat='.       ($ramal->nat == 1 ? 'yes' : 'no')   .chr(13).chr(10).
                                 'callerid="'. $ramal->nome.'" '. '<'. $ramal->numero .'>'.chr(13).chr(10).
                                 ($grupos ? $grupos->txt : '')
    );
                    
    fclose($handle);
  }


  public function escreveFxs($ramal){
    $handle = fopen("/etc/asterisk/khomp_fxs.conf", "a");
    $grupos = $ramal->profile_ramal_id ? $this->getGrupos($ramal->profile_ramal_id)->grupos : null;
    //00=calleridnum: 220 |calleridname:220| callgroup: 1 | pickupgroup: 1
    
    fwrite($handle,
             chr(13).chr(10).$ramal->porta." = ".
                             "calleridnum: ".$ramal->numero." | ".
                             "calleridname: ".$ramal->nome." | ".
                             "callgroup: ".$grupos->grupo_captura." | ".
                             "pickupgroup: ".$grupos->captura_grupos);

    fclose($handle);
  }       


  public function getAll(){
       $ramais = $this->entity->all();
       return $ramais;
  }
   
  
  public function resetaArquivos(){
      $arqs = ['/etc/asterisk/sip_ramais.conf',
               '/etc/asterisk/iax_ramais.conf',
               '/etc/asterisk/khomp_fxs.conf'
              ];


      foreach ($arqs as $arq){
         $handle = fopen($arq, 'w');
         fwrite($handle,'');
         fclose($handle);
      }
  }


  public function getFxsPorts(){
    //são 24 portas para cada FXS adicionado
    $portas_total = DB::table("hardwares")->where('tipo' ,'=' , 'FXS')->count() * 24;

    return (Object)['total'=>$portas_total];
  }


  public function getUsedFxsPorts(){
    $portas_usadas = $this->entity->where('tecnologia', '=', 15)
                    ->get()
                    ->pluck('porta')
                    ->toArray();

    return json_encode($portas_usadas);
  }

  public function atualizaArquivo(){
      $this->resetaArquivos();
      $ramais = $this->getAll();
    
      foreach($ramais as $r){
         
        switch($r->tecnologia){
            case '12':
                $this->escreveSip($r);
            break;
            case '13':
                $this->escreveIax($r);
            break;
            case '14':
                $this->escreveSip($r);
                $this->escreveIax($r);
            break;
            case '15':
                $this->escreveFxs($r);
            break;
        }
      }
  }


  public function get(Request $request){
      $ramal = $this->entity->find($request->id);
      
      return $ramal->toJson();
  }


  public function after_last ($esse, $inthat)
  {
          if (!is_bool($this->strrevpos($inthat, $esse)))
          return substr($inthat, $this->strrevpos($inthat, $esse)+strlen($esse));
  }
     

    public function after($chave, $inthat) {
     if (!is_bool(strpos($inthat, $chave)))
     return substr($inthat, strpos($inthat,$chave)+strlen($chave));
    }
    
    public function before($esse, $inthat){
       return substr($inthat, 0, strpos($inthat, $esse));
    }
    
    public function strrevpos($instr, $needle)
        {
             $rev_pos = strpos (strrev($instr), strrev($needle));
             if ($rev_pos===false) return false;
             else return strlen($instr) - $rev_pos - strlen($needle);
        }

    public function before_last ($esse, $inthat)
        {
            return substr($inthat, 0, $this->strrevpos($inthat, $esse));
        }
    

}