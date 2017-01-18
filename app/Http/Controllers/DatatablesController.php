<?php

namespace App\Http\Controllers;

use App\Models\RamalSetting;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Datatables;
use \Yajra\Datatables\Request as RequestDatatables;

use App\Models\User;
use App\Models\ViewUsers;
use App\Models\ViewGroups;
use App\Models\Ramal;
use App\Models\ViewProfileRamal;
use App\Models\attr_avanc_khomp;
use App\Models\attr_troncos_ip;
use App\Models\ProfileRamal;
use App\Models\Centrais;
use App\Models\hardwares;
use App\Models\Troncos;
use App\Models\Juntor;
use App\Models\BlackList;
use App\Models\Custos;
use App\Models\Uras;
use App\Models\Audios;
use App\Models\Grupos;
use App\Models\Saudacoes;
use App\Models\Codigos;
use App\Models\Gravacoes;
use App\Models\Voice_mail;
use App\Models\WhiteList;
use DB;

class DatatablesController extends Controller
{
    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        return view('admin.datatables.index');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */

     public function getJuntor()
     {
        $data = DB::table('troncos', 'juntores')
                     ->select('*')
                     ->where('juntores.id', '=', '64');

        return ($data);
     }

    public function anyData($data)
    {
        $request = new RequestDatatables;
        $teste = new Datatables($request);

        return Datatables::of($data)->make(true);
    }

    public function dataUras(){
        $ura = new Uras;
        //$data = $ura::all()->join('horarios', 'uras.id', '=', 'horarios.id');
        $data = DB::table('uras')->select('uras.*' ,'ramais.nome as nome_ramal')
                                 ->join('horarios', 'uras.id', '=', 'horarios.id')
                                 ->join('ramais', 'uras.nome', '=','ramais.id');
        
        return $this->anyData($data);
    }

    public function dataAudios(){
        $audio = new Audios;
        $data = $audio->all();

        return $this->anyData($data);
    }

    public function dataSaudacoes()
    {
        $saudacoes_class = new Saudacoes;

        $data = $saudacoes_class->all();
   
        return $this->anyData($data);
    }

    public function dataBlackList(BlackList $bl)
    {
        $data = $bl->all();
        $entity = new Troncos;
        foreach ($data as &$d){
            $d->nomeTronco = '';
            $troncos = explode(',', $d->tronco);
                foreach($troncos as &$tronco){
                    $tr = $entity->find($tronco);
                    if($tr){
                    $d->nomeTronco .= $d->nomeTronco != '' ? ', '.$tr->nome : $tr->nome;
                    }
                }           
        }
   
        return $this->anyData($data);
    }

    public function dataWhiteList(WhiteList $wh)
    {
        $data = $wh->all();
        $entity = new Troncos;
        foreach ($data as &$d){
            $d->nomeTronco = '';
            
            $troncos = explode(',', $d->tronco);
                foreach($troncos as &$tronco){
                    $tr = $entity->find($tronco);
                    if($tr){
                    $d->nomeTronco .= $d->nomeTronco != '' ? ', '.$tr->nome : $tr->nome;
                    }
                }           
        }
   
        return $this->anyData($data);
    }

    public function dataCentrais()
    {
        $data = Centrais::select('centrais.*', DB::raw('troncos.nome as nome_tronco'))->join('troncos', 'troncos.id', '=', 'centrais.tronco');
   
        return $this->anyData($data);
    }

    public function dataCdr(){
        $data = DB::table('cdr')->select('*');
        return $this->anyData($data);
    }

    public function dataUsers(){
        $data = ViewUsers::select('*');
        return $this->anyData($data);
    }
    
    public function dataGroups(){
        $data = ViewGroups::select('*');
        return $this->anyData($data);
    }

    public function dataGrupos(){
        $grupos = Grupos::all();
        $ramais = Ramal::select('nome', 'id')->get();
        #dd($ramais);
        
        foreach ($grupos as &$grupo){
             $grupo->ramais = explode(',', $grupo->ramais);
             $array = [];
             
             foreach ($ramais as $ramal){
                  foreach ($grupo->ramais as $ramal_grupo){
                        if ($ramal_grupo == $ramal->id){
                              array_push($array, $ramal->nome);
                        }
                  }
             }

             $grupo->nomesRamais = implode(',', $array);
        }      
        #$grupo->nomesRamais = implode(',', $array); 
        return $this->anyData($grupos);
    } 

    public function dataRamais(Ramal $ramal){
        $data = $ramal::all();
        return $this->anyData($data);
    }

    public function dataProfilesRamais(ProfileRamal $profileRamal){
        /*$data = $profileRamal::all();
        return $this->anyData($data);*/
        $ramal = new Ramal;
        $ramais = $ramal->select('profile_ramal_id', 'nome')->get();
        $todos_profiles = $profileRamal::all();
        //dd($profiles_em_ramais);
       
         foreach ($todos_profiles as &$t){
                $t['dependentes'] = '';
                foreach ($ramais as $p){
                    if ($p->profile_ramal_id == $t->id)
                    {
                         $t['dependentes'] .= ';'.$p->nome;
                    } 
             }
        }
       //dd($todos_profiles);

        return $this->anyData($todos_profiles);
    }


    public function dataCodigos(){
         $codigo = new Codigos;
         $data = $codigo::all();
         return $this->anyData($data);
    }

    public function dataVoiceMail(){
         $voice_mail = new Voice_mail;
         
         $data = DB::table('voice_mail')
                      ->join('ramais', 'voice_mail.ramal','=', 'ramais.id')    
                      ->select(DB::raw("voice_mail.*,ramais.nome as nome_ramal, ramais.id as id_ramal, ramais.numero as numero_ramal"))->orderBy('voice_mail.created_at');

         //return json_encode($data);
         
         return $this->anyData($data);
    }

    public function dataCustos(){
         $custos = new Custos;
         $ramais = new Ramal;

         $data = $custos->all();
         
         foreach ($data as $key=>$custo){
            $custo->ramais = $ramais->select('id', 'nome')->where('centro_de_custo_id', '=', $custo->id)->get();
         }
         
         return $this->anyData($data);
    }

    public function dataHardwares(Hardwares $hardwares){
        $boards = $hardwares->orderBy('board', 'asc')->get();
        $juntores  = DB::table('juntores')->where('board_serial', '<>', '' )->get();
        
        $seriaisUsados = array();
        
        foreach($juntores as $j){
            if($j->board_serial != null){
                array_push($seriaisUsados, $j->board_serial);
            }       
        }  
        
        foreach ($boards as &$b){
            $b->dependencia = in_array($b->serial, $seriaisUsados);   
        }

        return $this->anyData($boards);
    }

    public function dataRamaisSettings(RamalSetting $ramalSetting){
        $data = $ramalSetting::all();
        return $this->anyData($data);
    }
    
    public function dataTroncos(Troncos $troncos){
       $troncosr = new Troncos;
       $attr_avanc_khomp = new attr_avanc_khomp;
       $attr_troncos_ip = new attr_troncos_ip;
       $attrarr = array();

       $resul = $troncosr::all();
       $attrs = $attr_avanc_khomp::all();
       $ip = $attr_troncos_ip::all();

       $troncosarray = $resul->toArray(); //array de troncos
       $attarray = $attrs->toArray(); //array de atributos avancados khomp
       $iparray = $ip->toArray(); //array de atributos avancados ip
       
       foreach($troncosarray as &$tronco){
             foreach ($attarray as $attkhomp){
                if($tronco['id'] == $attkhomp['id']){
                  foreach($attkhomp as $key=>$a){
                     $tronco[(String)$key] = $a;
                  }
                }
             }
             foreach($iparray as $ip){
                 if ($tronco['id'] == $ip['id']){
                     foreach ($ip as $key=>$i){
                     $tronco[(String)$key] = $i;
                     }
                 }
             }
       }

       $output = json_encode(array('data' => ($troncosarray)));
       return $output;
    }

    public function dataJuntor(Juntor $juntores){
        $data = $juntores::all();
        return $this->anyData($data);
    }

    public function dataJuntorMin(Juntor $juntores){
        $data =  DB::table('juntores')->get(); 
        return ($data);
    }


    public function dataGravacoesFiltrar(Request $request){
        $gravacoes = new Gravacoes;
        $query = 'SELECT *, cdr.id AS id_cdr,
                            coment_gravacao.id AS gravacao_id,
                            GROUP_CONCAT( coment_gravacao.id ) AS grp_com_id,
                            GROUP_CONCAT( coment_gravacao.txt ) AS grp_com_txt,
                            GROUP_CONCAT( coment_gravacao.tempo ) AS grp_com_tempo,
                            GROUP_CONCAT( coment_gravacao.dono ) AS grp_com_dono
                            FROM `cdr` ';
        
        $array_condicoes = array();

        if(isset($request->filtro_orig) && $request->filtro_orig != ''){
            $numero = preg_replace("/\D+/", "", $request->filtro_orig);
            
            $cond_orig = 'src ='.$request->filtro_orig;

            if(strlen($numero) > 9 && strlen($numero) < 13 ){ 
                if(substr($numero, 0, 1) == 0){
                    $numero = substr($numero, 1);
                }
                //$ddd = substr($numero, 0, 3);
                $numero = substr($numero, 2);
                $cond_orig .= ' OR src = '.$numero;
            }


            array_push($array_condicoes, '('.$cond_orig.')');
        }

        if(isset($request->filtro_dest) && $request->filtro_dest != ''){
            $numero = preg_replace("/\D+/", "", $request->filtro_dest);

            $cond_dest = 'src ='.$request->filtro_dest;

            if(strlen($numero) > 9 && strlen($numero) < 13 ){ 
                if(substr($numero, 0, 1) == 0){
                    $numero = substr($numero, 1);
                }
                //$ddd = substr($numero, 0, 3);
                $numero = substr($numero, 2);
                $cond_dest .= ' OR src = '.$numero;
            }
            array_push($array_condicoes, 'dst = '.$cond_dest);
        }

        if(isset($request->filtro_data_ini) && $request->filtro_data_ini != ''){
            //diminui um dia no dia indicado
            $date = explode('/', $request->filtro_data_ini);
            $data_ini = $date[2].'-'.$date[1].'-'.$date[0];
            //$data_ini = date('Y-m-d', strtotime($data_formatada.'-1 day'));

            $cond_data = 'DATE_FORMAT(start, "%Y-%m-%d") >= "'.$data_ini.'"';

            if(isset($request->filtro_data_fim) && $request->filtro_data_fim != ''){
                //soma um dia na data final
                $date = explode('/', $request->filtro_data_fim);
                $data_fim = $date[2].'-'.$date[1].'-'.$date[0];
                //$data_fim = date('Y-m-d', strtotime($data_formatada.'+1 day'));
                $cond_data .= ' AND DATE_FORMAT(start, "%Y-%m-%d") <= "'.$data_ini.'"';

             } 

             array_push($array_condicoes, '('.$cond_data.')' );
        }
       
        if(isset($request->filtro_duracao_ini) && $request->filtro_duracao_ini != '' ){
            //converte do formato 00:00 para segundos
            $duracao_array_ini = explode(':',$request->filtro_duracao_ini);
            $duracao_ini = (int)($duracao_array_ini[0])*60 + (int)$duracao_array_ini[1];
            
            $cond_duracao = 'duration >= '.($duracao_ini);

            if(isset($request->filtro_duracao_fim) && $request->duracao_fim != ''){
                 $duracao_array_fim = explode(':', $request->filtro_duracao_fim);
                 $duracao_fim = (int)($duracao_array_fim[0])*60  + (int)$duracao_array_fim[1];

                 $cond_duracao .= ' AND "'.($duracao_fim).'" >= duration';
            }


            array_push($array_condicoes, '('.$cond_duracao.')');
        }

        if(isset($request->filtro_comentario) && $request->filtro_comentario != ''){

            $cond_comentario = '( SELECT count(*) FROM coment_gravacao WHERE coment_gravacao.gravacao = cdr.id AND coment_gravacao.txt LIKE "%'.$request->filtro_comentario.'%" ) > 0'; 

            array_push($array_condicoes, '('.$cond_comentario.')');
        } 

        if(isset($request->filtro_hora_ini) && $request->filtro_hora_ini != ''){ 
            //$hora_ini = date('H:m', strtotime($request->filtro_hora_ini.'-1 minute'));
            $hora_ini = $request->filtro_hora_ini;

            $cond_hora = 'DATE_FORMAT(start, "%H:%i") >=  "'.$hora_ini.'" ';

            if(isset($request->filtro_hora_fim) && $request->filtro_hora_fim != ''){
                $hora_fim = $request->filtro_hora_fim;
                $cond_hora .= 'AND "'.$hora_fim.'" >= DATE_FORMAT(start, "%H:%i") ';
            }

            array_push($array_condicoes, '('.$cond_hora.')');
        }

        $join = 'LEFT JOIN coment_gravacao ON coment_gravacao.gravacao = cdr.id';

        $string_condicoes = ' WHERE audio IS NOT NULL ';

        if(count($array_condicoes) > 0){
            $string_condicoes .= 'AND '.implode(' AND ', $array_condicoes);
        }

        //(cdr.start BETWEEN '2016-10-05' AND '2016-10-09')
        $condicao_agrupamento = 'GROUP BY `cdr`.`linkedid` ORDER BY `cdr`.`id` DESC';
        $limite = '';
        if(isset($request->limite)){
            $limite = 'LIMIT '.$request->limite;
        }

        $query .= $join.' '.$string_condicoes.' '.$condicao_agrupamento.' '.$limite;

        $data = DB::select($query);
        $json_data["data"] = $data;
        return json_encode($json_data);
        //return $this->anyData($data);
        // filtro_orig
        // filtro_dest
        // filtro_data_ini
        // filtro_data_fim
        // filtro_comentario
        // filtro_duracao_ini
        // filtro_duracao_fim
        // filtro_hora_ini
        // filtro_hora_fim

    }

    public function dataPermissoes(){
         $data = DB::table('ramais')->select('*', DB::raw('ramais.id as ramal_id, perm_gravacao.id as id_permissao'))->leftjoin('perm_gravacao', 'perm_gravacao.ramal', '=', 'ramais.id');
        
         return $this->AnyData($data);
    }
    
}
