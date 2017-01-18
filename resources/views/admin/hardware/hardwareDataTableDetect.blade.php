<!-- Page Heading -->

    <div class="col-lg-12">
        <div class="table-responsive">
            <div class="panel-body">         
             <table id="tabela" class="table tgHardware troll-border sorting_1">          
                 <thead>
                    <th class="harder dtcenter">Serial </th>
                    <th class="harder dtcenter">Tipo </th> 
                    <th class="harder dtcenter">IP</th>
                    <th class="harder dtcenter">Perfil</th> 
                    <th class="harder dtcenter">Ações </th>
                  </thead>
                  <tbody>
                @foreach ($final as $final)               
                  <tr>                                 
                    <td class="">{{ $final['serial'] }}</td>
                    <td class="">{{ $final['tipo']}} - {{ $final['portas'] }}</td>
                    <td class=""><form action="{{ route('admin.hardware.write') }}" method="get">
                                 <input type='text' id='stringbuffer' name='stringbuffer' class='hidden' value="{{ $final['serial'].' '.$final['hardware'].' '.$final['tipo'].' '.$final['portas'] }}"/>
                                 <input type="text" id="ip" placeholder="Digite um IP !" name="ip" class='ip' required/></td>
                    <td class=""><select id='perfil' name='perfil' class="{{$final['tipo'] == 'E1' ? 'hidden' : ''}}" > 
                                 <option class="{{$final['tipo']=='E1' ? 'hidden' : ''}}" value="{{$final['tipo']}}"> {{$final['tipo']}} </option></select>
                                  
                                 <div class="{{$final['tipo']=='E1' ? '' : 'hidden'}}">
                                 <p class="{{ $final['portas'] >= 30 ? '' : 'hidden' }} ">Link 0 </label>
                                 <select id='perfisLinks0' name='perfisLinks0' class=" {{$final['portas'] >= 30 ? '' : 'hidden'}} ">
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="Deactivated" selected> Desativado </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="R2"> R2 </option> <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="R2Passive">R2Passive </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDN"> ISDN </option> <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDNNetwork"> ISDNNetwork </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDNPassive"> ISDNPassive </option>    
                                 </select><br>
                                 <p class="{{ $final['portas'] >= 60 ? '' : 'hidden' }}">Link 1 </label>
                                   <select id='perfisLinks1' name='perfisLinks1' class="{{ $final['portas'] >= 60 ? '' : 'hidden' }}">
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="Deactivated" selected> Desativado </option>                                  
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="R2"> R2 </option> <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="R2Passive">R2Passive </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDN"> ISDN </option> <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDNNetwork"> ISDNNetwork </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDNPassive"> ISDNPassive </option>    
                                 </select><br>
                                 <p class="{{ $final['portas'] >= 90 ? '' : 'hidden' }}">Link 2 </label>
                                 <select id='perfisLinks2' name='perfisLinks2' class="{{$final['portas'] >= 90 ? '' : 'hidden'}}">
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="Deactivated" selected> Desativado </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="R2"> R2 </option> <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="R2Passive">R2Passive </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDN"> ISDN </option> <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDNNetwork"> ISDNNetwork </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDNPassive"> ISDNPassive </option>    
                                 </select><br>
                                 <p class="{{ $final['portas'] >= 120 ? '' : 'hidden' }}">Link 3 </label>
                                 <select id='perfisLinks3' name='perfisLinks3' class="{{$final['portas'] == 120 ? '' : 'hidden'}}">
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="Deactivated" selected> Desativado </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="R2"> R2 </option> <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="R2Passive">R2Passive </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDN"> ISDN </option> <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDNNetwork"> ISDNNetwork </option> 
                                 <option class="{{$final['tipo']=='E1' ? '' : 'hidden'}}" value="ISDNPassive"> ISDNPassive </option>    
                                 </select>
                                 </div>
                                  
                                  </td>
                                 
                    <td class=""><input type="submit" value="Adicionar" onClick=""></form></td>
                  </tr>
    
                
                @endforeach
                                  <tbody>

                   </table>

                  <br>
                  <br>
           
        </div>
        </div>
    </div>
</div>
@push('scripts')


    <script>
    $(function() {


        $('#perfislinks0').on('change', function(){
              $('#strbufferperfil').val('x[0 '+$('#perfislinks0').val()+' ]');
        });
        $('#perfislinks1').on('change', function(){
              $('#strbufferperfil').val('x[1 '+$('#perfislinks1').val()+' ]');
        });  

        $('#perfislinks2').on('change', function(){
              $('#strbufferperfil').val('x[2'+$('#perfislinks2').val()+ ']');
        });
        $('#perfislinks3').on('change', function(){
              $('#strbufferperfil').val('x[3'+$('#perfislinks3').val()+ ']');
        });  

     });
    </script>
@endpush
