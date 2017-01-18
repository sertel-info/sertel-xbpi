@extends('admin.base')

@push("headers")
    <link rel="stylesheet" type="text/css" href="/assets/css/loading.css">
@endpush
@section('content')
    <!-- Page Heading -->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                Ramais
            </h1>
            <ol class="breadcrumb">
                <li class="active">
                    <i class="fa fa-list"></i>  Ramais
                </li>                

                <a href='{{route("admin.grupos.index")}}'><span style="float:right; margin-right:10px" class="label label-primary"><i class="fa fa-users fa-2x" aria-hidden="true"></i>&nbsp Grupos de Atendimento </span></a>

                <a href='{{route("admin.ramais.profiles.index")}}'><span style="float:right; margin-right:10px" class="label label-primary"><i class="fa fa-users fa-2x" aria-hidden="true"></i>&nbsp Perfis de ramais </span></a>

                <a href='{{route('admin.voice_mail.index')}}'><span style="float:right; margin-right:10px" class="label label-primary"><i class="fa fa-volume-down fa-2x" aria-hidden="true"></i>&nbsp Voice Mail </span></a>

                <a href='{{route("admin.custos.index")}}'><span style="float:right; margin-right:10px" class="label label-primary"><i class="fa fa-usd fa-2x" aria-hidden="true"></i>&nbsp Centro de Custos </span></a>

                <a href='{{route("admin.codigos.index")}}'><span style="float:right; margin-right:10px" class="label label-primary"><i class="fa fa-asterisk fa-2x" aria-hidden="true"></i>&nbsp Código de Contas </span></a>

                <a href='{{route("admin.uras.index")}}'><span style="float:right; margin-right:10px" class="label label-primary"><i class="fa fa-comment fa-2x" aria-hidden="true"></i>&nbsp Uras </span></a>
            </ol>                   
      </div>
    </div>

    <!-- /.row -->
    <div class='row'> 
    <!--<a href="{{route('admin.ramais.create')}}" class="btn btn-block btn-info"><span class="glyphicon glyphicon-plus"></span> Criar Novo Ramal</a><br> -->
    <a id='createButton' class="btn btn-block btn-info btn btn-info gsm_khomp digital_khomp" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-plus"></span> Criar Novo Ramal</a><br> 

    {{-- para carregar listagem normal sem data table --}}
    {{-- @include('admin.accounts.list') --}}

    {{-- para carregar listagem com data table --}}
    @include('admin.ramais.datatable')
  <!-- <button type="button" class="btn btn-info gsm_khomp digital_khomp" data-toggle="modal" data-target="#myModal">Nova Cadência</button> -->
  
  
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><br><br>
              <h4 class="modal-title" id="modalDefaultTitle"></h4>
              
              <div id='msgFeedBack' class='alert alert-danger hidden'>
                   <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                   <ul id='listaerros'>
                   
                   </ul> 
              </div>
             
            </div>
              {!! Form::open(array('route' => 'admin.ramais.store', 'id' => 'formRamais', 'method' => 'get')) !!}

            <div class="modal-body" id="modalDefaultBody">
                   <!--{!! Form::open( array('method'=>'get', 'data-toggle'=>'validator', 'id'=>'formRamal') ) !!} -->

                   @include('admin/ramais/formfields')  
                    
          
            </div>
            
            <div class="modal-footer">
                 <div id='cadFooter'>
                  <button type="button" class="btn btn-default" data-dismiss="modal" >Cancelar</button>
                  <button type="button" id="enviar" class="btn btn-primary" >Cadastrar</button>
                 </div>
                
                 <div id='editFooter'> 
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                  <button type="button" id="edit" class="btn btn-primary">Salvar</button>
                 </div>
                  </div>
              {!! Form::close()!!}
                </div>
              </div>
        </div>
     </div>        
  
@endsection

@push('scripts')
<script src="/assets/js/controleRamais.js"></script>
<script src="/assets/js/validador.js"></script>
<script>

     function resetaForm(){
               console.log('resetaForm()');   
               $('#editFooter').hide();
               $('#cadFooter').show();
               $("#formRamais").attr('action', "{{route('admin.ramais.store')}}");
               
               $('#myModal')
                          .find("input[type=text]")
                             .val('')
                             .removeAttr('disabled')
                             .end()
                          .find("input[type=checkbox], input[type=radio]")
                             .prop("checked", "")
                             .removeAttr('disabled')
                             .end()
                          .find("option")
                             .show()
                             .end()
                          .find("select")
                              .not('#profile')
                            .val('0')
                            .removeAttr('disabled')
                            .end();
                

                if ($("#profile option[value=0]").length > 0)
                {
                    $('#myModal')
                          .find("select")
                             .val('0')
                             .removeAttr('disabled')
                             .end()
                } 
                       
                resetaCheckBoxes();
                $('.form_din').hide();
                $('.perguntaddr').hide();
                $('.form_dac').hide();
                $('.form_pabx').hide();
                $('.profile').hide();
                $('#editForm').hide();
                $('#cadForm').show();
                $('#noexterno').hide();
                $('.porta').hide();

              $('#msgFeedBack').hide();

                          removeErrors();
   }

   function showEdit(id){
    var $formRamais = $('#formRamais');
    $formRamais.attr('action', '{{route("admin.ramais.update")}}/'+id);
    
    var $form_children = $formRamais.contents();
    $form_children.hide();

    $formRamais.append('<div class="spinner"></div>');
    
    $.get("{{route('admin.ramais.get')}}", {id : id}, function(resp){
        json = JSON.parse(resp);

        $('#cadFooter').hide(),      
        $('#editFooter').show();
        $('input[name=antigos]').val(json.nome+';'+json.numero);                            
        $('#tipo').val(json.tipo),
        $('#tec').val(json.tecnologia),
        $('#app').val(json.aplicacao),
        $('#nome').val(json.nome),
        $('#number').val(json.numero),
        $('#ddr').val(json.ddr),
        $('#habDDR').val(json.ddr != null ? 1 : 0),
        $('#detour').val(json.desvio_para),
        $('#password').val(json.senha),
        $('#deviation').val(json.desvio_tipo),
        $('#central').val(json.central),
        $('#no_externo').val(json.no_externo),
        $('#conference').bootstrapSwitch('state', json.conferencia == '1'),
        $('#centercost').bootstrapSwitch('state', json.centro_custo == '1'),
        $('#intercomaccess').bootstrapSwitch('state', json.Porteiro == '1'),
        $('#accountcode').bootstrapSwitch('state', json.codigo_conta == '1'),
        $('#notdisturb').bootstrapSwitch('state', json.nao_perturbe == '1'),
        $('#capture').bootstrapSwitch('state', json.capturar == '1'),
        $('#parking_calls').bootstrapSwitch('state', json.estacionar_chamadas == '1'),      
        $('#nat').bootstrapSwitch('state', json.nat == '1');
        $('#profile').val(json.profile_ramal_id).trigger('change');        
        $('#myModal').modal('toggle');

        $('input[name=fxs_port][value='+json.porta+']').prop('checked', true);


        mostrarFormApp();
        setDeviation();
        setDesvio();
        setType();
        setTec();
        verificaDDR();    
        setDesvioPara();

        $form_children.show();
        $formRamais.find('.spinner').remove();
    });
}  

</script>

<script>
    $(function(){
        sessionStorage.clear();
  
        $('#editFooter').hide();
        $('.detour').hide();
        $('#noexterno').hide();

        //resetaForm();
      
       $('body').on('hidden.bs.modal','.modal', function(){
          console.log('hidden Modal');
          resetaForm();
       });
       
       $('#enviar').on('click',function(){
              if(valida()){
                $("tec").removeAttr('disabled');
                $("app").removeAttr('disabled');
                $("#formRamais").submit();
              } 
       });

       $('#edit').on('click',function(){
              if(valida()){
                $("tec").removeAttr('disabled');
                $("app").removeAttr('disabled');
                $("#formRamais").submit();
              } 
       });


             
});
</script>
@endpush