<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\hardwares;
use App\Models\Juntor;
use Auth;
use Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DatatablesController;
use App\Http\Controllers\JuntorController;
use DB;
use View;

class HardwareController extends Controller
{
    public function __construct(Hardwares $entity){
        $j = new Juntor;
        $this->jController = new JuntorController($j, $entity);
        
        $this->entity = new $entity;
        $this->middleware('auth');
    }

    public function create(){
         return view('admin.troncos.create');
    }

    public function firstListHardwares(){ 
     //   $arquivo = "/home/sistema/public/assets/hardwares.txt";
        $final = array('khomp' => null,
                       'dahdi' => null,
                       'dgv' => null
                      );

        $handle = fopen('/home/sistema/public/assets/hardwares', 'r');                
        $device = fgets($handle, 1024);
        $devicesSep = explode(';', $device);
   
        foreach ($devicesSep as $devSep){
                  switch($devSep){
                    case 'khomp': {$final['khomp'] = 1; break; }
                    case 'dgv':   {$final['dgv'] = 1; break; }
                    case 'dahdi': {$final['dahdi'] = 1; break; }
                  }
  
         }
       fclose($handle);
       return view('admin.hardware.index', compact('final'));
  }
   
   public function index(){
   //$resul = DB::table('troncos')->get();
   return view('admin.hardware.index');
   }

    function before($esse, $inthat)
    {
        return substr($inthat, 0, strpos($inthat, $esse));
    }

    public function edit($id){
      #$finalprov = explode(" ", $request->stringbuffer1);
      
      $hardware = $this->entity->find($id);


      $final = array('serial'=> $hardware->serial, 'tipo'=>$hardware->tipo, 'ip'=>$hardware->ip, 'portas'=> $hardware->portas);
      
      #$ip = $this->getIp($final['serial']);      
      

      return view('admin.hardware.edit', compact('final'));   
    }

    public function editSPX($id){
      
      $hardware = $this->entity->find($id);

      $final['tipo'] = $hardware->tipo;
      $final['portas'] = $hardware->portas;
      $final['ip'] = $hardware->ip;
      $final['serial'] = $hardware->serial;

      
     # $final['portas'] = $this->getPortas($final['serial']);
      return view('admin.hardware.edit', compact('final'));
    }

     public function update(Request $request){
       
    }   
      
      
    public function addip($serial, Request $request){               
      //$devicesyaml = "/etc/sistema/public/assets/arquivoteste.txt";

      $handle = fopen($devicesyaml, "r");
      $lines = file($devicesyaml);
      $flag = 0;
      $countl = count($lines);
      //return($request->ip);
      for ($i = 0; $i < $countl; $i++){
          if(strripos($lines[$i], $serial))
           {        
            $flag = 1;
                 for($j = $i; $j < $countl; $j++){
                   if(strripos($lines[$j], 'IP') != false){
                       fclose($handle);
                       $handle = fopen($devicesyaml, 'w');
                       $lines[$j] = chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'IP:'.chr(32).$request->ip.chr(13).chr(10);
                        file_put_contents($devicesyaml, $lines);
                       fclose($handle);
                       break;
                   }
                 }
             } 
      }
     return ($this->detectkhomp());
    }
     
     
     public function updatePosicoes(){
         if (isset($_GET['newpos']))
         {
              foreach ($_GET['newpos'] as $key=>$p) 
              {
                $board = (DB::table('hardwares')->where('serial', '=', $p['serial']));
                      if ($board !== null)
                      {
                         $re = $board->update([
                            'board' => $key
                         ]);    
                      }          
              }
              $this->updateBoardJuntores();
              $this->atualizaArquivo();
        }
      }
     
     public function destroy(Request $request){                   
        $id = isset($_GET['id']) ? $_GET['id'] : 0;  
       
        $thisSerial = DB::table('hardwares')->where('id', '=', $id)->value('serial');
        
        DB::table('juntores')->where('board_serial', '=', $thisSerial)->delete();
        $this->entity->find($id) != null ? $status = $this->entity->find($id)->delete() : '';

        $this->updateBoardJuntores();
        $this->atualizaArquivo();
        return response()->json(['status'=>$id]);

    } 

    public function after($chave, $inthat) {
     if (!is_bool(strpos($inthat, $chave)))
     return substr($inthat, strpos($inthat,$chave)+strlen($chave));
    }

    public function getIp($serial){
     $devicesyaml = "/home/sistema/public/assets/arquivoteste.txt";  
     $lines = file($devicesyaml);
     $countl = count($lines);
     for ($i = 0; $i < $countl; $i++){
          if(strripos($lines[$i], $serial)){
                 for($j = $i; $j < ($i+4); $j++){
                   if(strripos($lines[$j], 'IP:') != false && strlen($lines[$j])>3){
                       return ($this->after('IP: ', $lines[$j]));
                       fclose($handle);
                   }
                 }   
          }         
      }
    }
    
      public function isCadastrado($serial){
        $h = $this->entity->where('serial', '=', $serial)->first(); 
        if($h != null){
          return 1;
        }
        return 0;
      }
      
      #???
      public function detectdahdi(){ 
        return ('configurar Dahdi');
      }
    
      public function detectdgv(){ 
          #$arquivo = '/etc/asterisk/digivoice.conf';
          $arquivoDigiAdd = '/etc/asterisk/digivoice.conf';
  
          $handle = fopen ($arquivoDigiAdd,'r');
          
          if(file_exists($arquivoDigiAdd)){
              $text = file_get_contents($arquivoDigiAdd); 
              //return(dd($text));  
          } 
         
          return view('admin.hardware.configdgv', compact('text', 'txtDigiAdd'));
      }
      
      public function configdahdi(){ 
          $output= array();
          $r = exec('dahdi_genconf 2>&1', $output);
          
          //return (strpos($r, 'Empty'));
            
             if (strpos($r, 'Empty') !== false){
               $msg = "Ocorreu uma falha na leitura.  Erro: ".$r.'';
               $err = 1;
             }

         $arquivoSystem_conf = '/etc/dahdi/system.conf';
         $arquivoChannel_conf = '/etc/asterisk/dahdi-channels.conf';


          if(file_exists($arquivoSystem_conf)){
          $system_conf = file_get_contents($arquivoSystem_conf);
          } 

          if(file_exists($arquivoChannel_conf)){
          $channels_conf = file_get_contents($arquivoChannel_conf);
          } 


          return view('admin.hardware.configdahdi'  ,  compact('msg', 'err', 'system_conf', 'channels_conf')); 
      }
      

      public function savedahdi (Request $request){
        $arquivoSystem_conf = '/etc/dahdi/system.conf';
        $arquivoChannel_conf = '/etc/asterisk/dahdi-channels.conf';
        
        file_put_contents($arquivoSystem_conf,$request->system_conf);
        file_put_contents($arquivoChannel_conf,$request->channels_conf);
  
        #return view('admin.hardware.configdahdi', compact('msg', 'system_conf', 'channel_conf'));

        Session::flash('message_type', 'success');
        Session::flash('message_text', 'Arquivo salvo com sucesso');

        return redirect()->route('admin.hardware.detectdahdi');
      }

      public function savedgv(Request $request){ 
          $arquivo = '/etc/asterisk/digivoice_additional.conf';
          
          if( file_exists($arquivo) ){
                file_put_contents($arquivo, $request->dgvfile); 
          } 
          
          return redirect()->route('admin.hardware.detectdgv');
      }


      public function detectkhomp(){               
        $resul = '';
        $stringbuffer = '';
        $final = array();
        $handle = fopen('/etc/khomp/devices','r');
        if ($handle) {
        while (($buffer = fgets($handle, 1024)) !== false) {
         $resul = $buffer;
         $result = explode(' ',$resul, 7);
         $result = array(
                   'ts'=>date("d/m/Y G:i:s", $result[0]),
                   'serial'=>$result[1],
                   'hardware' => $result[2],//$this->after('-', $this->before('_', $result[2])),
                   'numero'=>$result[3],
                   'portas'=>($this->after_last('_', $result[2])/10),
                   'portas2'=>$result[5],
                   'numero2'=>$result[6],
                   'tipo' => $this->before('_', $this->after('-', $result[2])),
                   'ip' => ($this->getIp($result[1])>1 ? $this->getIp($result[1]) : null),
                        );
         
         if(!$this->isCadastrado($result['serial'])){
          array_push($final, $result);
         }

       } 
        fclose($handle);
        return view('admin.hardware.detect', compact('final', 'stringbuffer'));     
      } 
           
      }

     public function listar(){               
           $hardwares = $this->getAll();
           $e1 = array();
           if(isset($hardwares[0]->id)){
               foreach($hardwares as $key=>$h){
                  if ($h->tipo=='E1'){
                     $novoe1['hardware'] = $h;
                     $novoe1['perfis'] = explode(';',$h->perfis);
                     array_push($e1, $novoe1);
                     unset($hardwares[$key]);
                  }
               }
           } else {
              unset($hardwares);
              unset($e1);
           }

           return view('admin.hardware.list', compact('hardwares', 'e1'));
     } 
           
     function updateBoardJuntores(){
         $juntoresKhomp = DB::table('juntores')->where('fabricante','=', 11)->get();

         foreach ($juntoresKhomp as $jun){
           $boardAtual = DB::table('hardwares')->where('serial','=', $jun->board_serial)->value('board');
           $boardVector = explode('c', $jun->juntor);
           $boardVector[0] = 'b'.$boardAtual;
           $boardString = implode('c', $boardVector);
           DB::table('juntores')->where('id', '=', $jun->id)->update([
            'juntor' => $boardString
            ]); 
         }

        $this->jController->atualizaArquivo(); 
     }

     function write(Request $request){ 
            $vetorInfos = explode(" ", $request->stringbuffer); //recebe os parametros que não vem no formulário.
            $perfisVetor = '';

            if(stripos($vetorInfos[1], 'V2')){
                    $version = 'V2';
              } else if(stripos($vetorInfos[1], 'V3')){
                    $version = 'V3';
              } else {
                    $version = 'V1';
              } 
            
            //o próximo hardware vai ter a board igual a (maior índice das boards atuais + 1)
            $maior_board_atual = (DB::table('hardwares')->orderBy('board', 'desc')->value('board')); 

            $boardNum = $maior_board_atual != null ? ($maior_board_atual)+1 : 0;

            $hardware = $this->entity->create([
                'serial' => $vetorInfos[0],
                'ip' => $request->ip,
                'tipo' => $vetorInfos[2],
                'portas' => $vetorInfos[3],
                'version' => $version,
                'board' => $boardNum
            ]); 
             
             if ($vetorInfos[2] == 'E1') //se for E1, guarda os perfis
             {
                 for ($i = 0 ; $i<$hardware->portas/30 ; $i++)//pega os perfis;
                 { 
                      $a = "perfisLinks".$i;
                      if($request->$a != 'null')
                      {
                         $perfisVetor[$i]= $request->$a;
                      }
                 }
                  $perfisString =implode($perfisVetor,';');
                  $hardware->update([
                  'perfis'=>$perfisString
                  ]);
            }
            $this->atualizaArquivo();
            $this->updateBoardJuntores();
            return redirect()->route('admin.hardware.list');
   }

   function getAll(){
       $tudo = new DataTablesController;
       $all = $tudo->dataHardwares($this->entity);
       $para_corrigir =  $this->before('}]', $this->after('[{',$all));
       $corrigido = '[{'.$para_corrigir.'}]';
       return json_decode($corrigido);
   }

    function resetaArquivo(){
          $handle = fopen("/etc/khomp/config/devices.yaml", "w");
          fwrite($handle, '');
          fclose($handle);
    }

    function atualizaArquivo(){
       $this->resetaArquivo();
       $devicesyaml = "/etc/khomp/config/devices.yaml"; 
       
       $handle = fopen($devicesyaml, "a");
       $hardwares = $this->getAll();
       $count = count($hardwares[0]);
       #dd($hardwares[0]->id);
         if(isset($hardwares[0]->id)) #este if verifica a função retornou algum elemento
            foreach ($hardwares as $h){
               if($h->tipo == 'E1' ){
                           $portas = $h->portas;
                           $qtdLinks = ($portas/300);
                           $perfisVetor = explode(';',$h->perfis);

                           $links = '';
                           //$bufferperfis = explode('x', $request->strbufferperfil);   
                          
                           for($i = 0; $i < $qtdLinks; $i++){
                                $links .= chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'-'.chr(13).chr(10).
                                                chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'"'.$i.'":'.chr(13).chr(10).
                                                chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'Type: E1'.chr(13).chr(10).
                                                chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'"Name": '.chr(13).chr(10).
                                                chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'Profile: '.$perfisVetor[$i].chr(13).chr(10); 
                           }
                          

                           fwrite($handle, 
                                    chr(13).chr(10).
                                    chr(32).chr(32).'-'.chr(13).chr(10).
                                    chr(32).chr(32).chr(32).chr(32).chr(34).$h->serial.chr(34).':'.chr(13).chr(10).
                                    chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'Type: '.$h->tipo.chr(13).chr(10).
                                    chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'IP:'.chr(32).$h->ip.chr(13).chr(10).
                                    chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'CustomTonesProfiles:'.chr(32).'Profile1'.chr(13).chr(10).
                                    chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'"LinkOperationMode": E1'.chr(13).chr(10).
                                    chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'"Replacer": '.chr(13).chr(10).
                                    chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'"Links": '.chr(13).chr(10).
                                    $links.
                                    chr(13).chr(10)
                               );

              } else if ( $h->tipo == 'FXS' || $h->tipo == 'FXO' ||  $h->tipo == 'GSM')
                      {
                             
                      fwrite($handle, ''.chr(13).chr(10).
                                      chr(32).chr(32).'-'.chr(13).chr(10).
                                      chr(32).chr(32).chr(32).chr(32).chr(34).$h->serial.chr(34).':'.chr(13).chr(10).
                                      chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'Type: '.$h->tipo.chr(13).chr(10).
                                      chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'IP:'.chr(32).$h->ip.chr(13).chr(10).
                                      chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'CustomTonesProfiles:'.chr(32).'Profile1'.chr(13).chr(10).
                                      chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'"Replacer": '.chr(13).chr(10).
                                      chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'Version:'.chr(32).$h->version.chr(13).chr(10).
                                      chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'"ChannelGroups": '.chr(13).chr(10).
                                      chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'-'.chr(13).chr(10).
                                      chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'"0-'.($h->portas-1).' ": '.chr(13).chr(10).
                                      chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'Type:'.chr(32).$h->tipo.chr(13).chr(10).
                                      chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'Profile:'.chr(32).$h->tipo.chr(13).chr(10)
                       );
                    }
          }
                fclose($handle);
          
     }
    function after_last ($esse, $inthat)
    {
        if (!is_bool($this->strrevpos($inthat, $esse)))
        return substr($inthat, $this->strrevpos($inthat, $esse)+strlen($esse));
    }
    
    function strrevpos($instr, $needle)
    {
         $rev_pos = strpos (strrev($instr), strrev($needle));
         if ($rev_pos===false) return false;
         else return strlen($instr) - $rev_pos - strlen($needle);
    }
    

  public function getLinks($serial){
     $devicesyaml = "/home/sistema/public/assets/arquivoteste.txt";  
     $lines = file($devicesyaml);
     $countl = count($lines);
     $linkbody = array();
     for ($i = 0; $i < $countl; $i++){
          if(strripos($lines[$i], $serial)){
                 for($j = $i+8; $j < $i+26 && $j<=$countl; $j+=5){
                       if(is_numeric($this->before('"',  $this->after('"', $lines[$j])))){
                          $bodybuffer = array('link' => $lines[$j],
                                       'tipo' => $lines[$j+1],
                                       'nome' => $lines[$j+2],
                                       'perfil' => $this->after('Profile: ', $lines[$j+3]) ,
                          );
                          array_push($linkbody, $bodybuffer);
                       } else { 
                        break;
                       } 
                 }     
        }  

     }   return ($linkbody);          
  } 

  public function setLink(Request $request){
     $devicesyaml = "/home/sistema/public/assets/arquivoteste.txt";  
     $lines = file($devicesyaml);
     $countl = count($lines);

     $linkbody = array();

     $final = $this->para_array($request->stringbuffer);
     $serial = $final['serial'];
     $portas = $final['portas'];

     for ($i = 0; $i < $countl; $i++){
          if(strripos($lines[$i], $serial)){
                 for($j = $i+8; $j < $i+26 && $j<=$countl; $j+=5){
                      $numerolink = ($this->before('"',  $this->after('"', $lines[$j])));
                      if( is_numeric($numerolink) ){
                               switch ($numerolink){
                               case 0:  
                                  if(strpos('Profile:',$lines[$j+3]) == 0){
                                  $lines[$j+3] = chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'Profile: '.$request->perfisLinks0.chr(13).chr(10);                                              
                                  if($portas == 30){ break;}
                                  break;
                               }
                               case 1: 
                                  if(strpos('Profile:',$lines[$j+3])  == 0){
                                  $lines[$j+3] = chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'Profile: '.$request->perfisLinks1.chr(13).chr(10); 
                                  if($portas == 60){ break;}
                                  break;
                                  }
                               case 2: 
                                  if(strpos('Profile:',$lines[$j+3])  == 0 ){
                                  $lines[$j+3] = chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'Profile: '.$request->perfisLinks2.chr(13).chr(10); 
                                  if($portas == 90){ break;} 
                                  break;                                 
                                  }
                               case 3: 
                                  if(strpos('Profile: ',$lines[$j+3]) == 0 ){
                                  $lines[$j+3] = chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).chr(32).'Profile: '.$request->perfisLinks3.chr(13).chr(10); 
                                  if($portas == 120){ break;}
                                  break;
                                  }
                               }   

                       }
                    else { 
                       } 
                 }     
        }  
     }   
    file_put_contents($devicesyaml, $lines); 
            return ($this->listar());
  }
  
  

  function para_array($texto) {
    $resultado = array();
  
    $partes = explode(",", $texto); // quebra em assinalamentos
    foreach ($partes as $parte) {
        list($chave, $valor) = explode("=", $parte); // separa chave e valor
        $resultado[$chave] = $valor;
    }
    return $resultado;
  }

} 
   

  