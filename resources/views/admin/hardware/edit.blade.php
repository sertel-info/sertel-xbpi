@extends('admin.base')
@section('content')
        <!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Listar Hardwares
        </h1>
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-list"></i>  <a href="{{route('admin.hardware.index')}}">Listagem</a>
            </li>
            <li class="active">
                <i class="fa fa-plus-circle"></i> Editar Hardware
            </li>
        </ol>
      
    </div>
    <div class=''>
           <table id="tabela" class="table tgHardware"> 
                  <tr>
                    <th class="harder dtcenter">Serial </th>
                    <th class="harder dtcenter">Tipo </th> 
                    <th class="harder dtcenter">IP</th>
                    <th class="harder dtcenter">{{$final['tipo'] == 'E1' ? 'Perfis' : 'Perfil'}}</th> 
                    <th class="harder dtcenter">Ações </th>
                  </tr>
            <td> {{$final['serial']}}</td>
            <td> {{$final['tipo']}}</td>
            <td> <form action="{{ $final['tipo']=='E1' ? route('admin.hardware.setLink') : route('admin.hardware.addip')}}" method="get"><input id="ip" type='text' name="ip" value="{{ isset($final['ip']) ? $final['ip'] : 'Digite um IP!' }}" /> </td>
            <td>  <select id='perfil' name='perfil' class="{{$final['tipo'] == 'E1' ? 'hidden' : ''}}" > 
                                 <option class="{{$final['tipo']=='E1' ? 'hidden' : ''}}" value="{{$final['tipo']}}"> {{$final['tipo']}} </option></select>
                  <div class="{{$final['tipo']=='E1' ? '' : 'hidden'}}">         
                                 <p class="{{ isset($final['portas']) && $final['portas'] >= 30 ? '' : 'hidden' }} ">Link 0 </label>
                                 <select id='perfisLinks0' name='perfisLinks0' class=" {{ isset($final['portas']) &&$final['portas'] >= 30 ? '' : 'hidden'}} ">
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="Deactivated" selected> Desativado </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="R2"> R2 </option> <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="R2Passive">R2Passive </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDN"> ISDN </option> <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDNNetwork"> ISDNNetwork </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDNPassive"> ISDNPassive </option>    
                                 </select><br>
                                 <p class="{{ isset($final['portas']) && $final['portas'] >= 60 ? '' : 'hidden' }}">Link 1 </label>
                                   <select id='perfisLinks1' name='perfisLinks1' class="{{ isset($final['portas']) && $final['portas'] >= 60 ? '' : 'hidden' }}">
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="Deactivated" selected> Desativado </option>                                  
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="R2"> R2 </option> <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="R2Passive">R2Passive </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDN"> ISDN </option> <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDNNetwork"> ISDNNetwork </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDNPassive"> ISDNPassive </option>    
                                 </select><br>
                                 <p class="{{ isset($final['portas']) && $final['portas'] >= 90 ? '' : 'hidden' }}">Link 2 </label>
                                 <select id='perfisLinks2' name='perfisLinks2' class="{{ isset($final['portas']) && $final['portas'] >= 90 ? '' : 'hidden'}}">
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="Deactivated" selected> Desativado </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="R2"> R2 </option> <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="R2Passive">R2Passive </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDN"> ISDN </option> <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDNNetwork"> ISDNNetwork </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDNPassive"> ISDNPassive </option>    
                                 </select><br>
                                 <p class="{{ isset($final['portas']) && $final['portas'] >= 120 ? '' : 'hidden' }}">Link 3 </label>
                                 <select id='perfisLinks3' name='perfisLinks3' class="{{isset($final['portas']) && $final['portas'] == 120 ? '' : 'hidden'}}">
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="Deactivated" selected> Desativado </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="R2"> R2 </option> <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="R2Passive">R2Passive </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDN"> ISDN </option> <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDNNetwork"> ISDNNetwork </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDNPassive"> ISDNPassive </option>    
                                 </select>
                                 
                                 </div>
            </td> 
            <td> <input class="hidden" id="stringbuffer" name="stringbuffer" type="text" value="serial={{$final['serial']}},portas={{isset($final['portas']) ? $final['portas'] : ''}},tipo={{$final['tipo']}}"/> <input type='submit' value="Salvar" /></form></td>
           </table>   
     </div>
</div>
  

@push('scripts')
<script src="/assets/js/jquery.maskedinput.min.js"> 

</script>
<script>
$(function() {
 $('#ip').mask("999.999.999.999");    


});

</script>
@endpush
        <!-- Simple List -->

@endsection
