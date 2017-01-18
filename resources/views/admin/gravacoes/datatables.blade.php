<!-- Page Heading -->
<link rel="stylesheet" href="/assets/js/plugins/mediaelement/build/mediaelementplayer.min.css" />
<style>
    .container-comentarios ul{
        margin-top:15px;
        padding:5px;
    }

    .container-comentarios li{
          list-style-type: none;
    }

    .timer{
        font-size:25px;
    }

</style>

<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
        <div class='col-md-4'></div>
        <div class='col-md-4'><h3>Gravações</h3></div>
        <div class='col-md-4'></div>
            <table class="table table-bordered table-hover table-striped" id="gravacoes-table">
                <thead>
                    <tr>
                        <th>Origem</th>
                        <th>Destino</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>




@push('scripts')

<script type="text/javascript" src='/assets/js/plugins/mediaelement/build/mediaelement-and-player.js'></script> 
<script>

    function TimeToSec(time){
        values = time.split(':');
        secs = parseInt(values[2]);
        min = parseInt(values[1]);
        hour = parseInt(values[0]);

        totalmin = min+hour*60;
        totalsecs = secs+totalmin*60;

        return totalsecs;
    }

    function setModal(event){
                    event.preventDefault();
                    var data_id = $(this).attr('data-id');
                    act_id = $("meta[name='_act_id']").prop('content');

                    var comentarios_json = sessionStorage.getItem('com_'+data_id);

                    if(comentarios_json != null){

                        comentarios_obj = JSON.parse(comentarios_json);
                        for(var i=0, len = comentarios_obj.length ; i <= len-1; i++){
                            obj = comentarios_obj[i];
                            dono = "{{Auth::getUser()->id}}" == obj.dono;
                            appendComment(obj.tempo, obj.txt, obj.gravacao, obj.id ,dono);
                        }
                    }

                    if(act_id == data_id){
                        $('#myModal').modal('show');
                        return;
                    }

                    var audio_link = sessionStorage.getItem(data_id),
                     comentarios = sessionStorage.getItem('com_'.data_id),
                     com_DOM = '',
                     player = $('#myModal').find('audio')[0];
                     player = $(player);

                    $("meta[name='_act_id']").prop('content', data_id);

                    player.prop('src', '/assets/audios/gravacoes/'+audio_link);
                    player[0].player.media.load();

                    $('#myModal').modal('show');
    }

    $(function() {
         sessionStorage.clear();
         mejs.i18n.locale.language = 'pt-BR'; // Setting German language

         var audio = $('audio').mediaelementplayer({
                enableAutosize: false,
         });

         var table = setDataTable();

        $("#form_filtro").on('submit', function(event){
            sessionStorage.clear();
            event.preventDefault();
            dados = $("#form_filtro").serialize();
           
            api = table.api();
            api.ajax.url("{{route('datatables.gravacoes')}}?"+dados).load();
        });

     });

     function getGravacao(event){
        event.preventDefault();
        el = $(this);

        $("#formDownload_"+el.attr('data-id')).submit();

     }


     function setDataTable(){
         oTable = $('#gravacoes-table').dataTable({
            processing: true,
            pageLength: 10,
            paging: false,
            searching: false,
            "drawCallback": function() {
                $(".openModalOuvir").on('click', setModal );
                $(".downloadGravacao").on('click', getGravacao);
            },
            ajax: {
                'url': '{!! route('datatables.gravacoes') !!}',
                'type': 'GET',
                'data': {limite : 10}
            },
            columns: [
                { data: 'src', name: 'src' },
                { data: 'dst', name: 'dst' },
                { data: 'start', name: 'start' },
                { data: 'start', name: 'start' },
                { data: 'tipo', name: 'tipo'},
                { data: 'acoes', name: 'acoes' },
            ],
            "columnDefs": [{

                targets:5,
                render: function(data, type, full, meta){
                var audio = full.audio.split('/');
                audio = audio[audio.length-1];
                
                if(full.grp_com_id != null && full.grp_com_txt != null && full.grp_com_tempo != null){
                        var com_ids = full.grp_com_id.split(','),
                         com_txts = full.grp_com_txt.split(','),
                         com_tempos = full.grp_com_tempo.split(','),
                         com_donos = full.grp_com_dono.split(','),
                         com_array = Array();
                       
                        for(var i = 0, len = com_txts.length ; i<=len-1; i++){
                            
                            com_array[i] = {
                                            'txt': com_txts[i],
                                            'tempo':com_tempos[i],
                                            'gravacao':full.gravacao,
                                            'dono':com_donos[i],
                                            'id':com_ids[i]
                                            }
                        }

                }

                sessionStorage.setItem(full.id_cdr, audio);

                if(com_array != undefined){
                     com_json = JSON.stringify(com_array);
                     sessionStorage.setItem('com_'+full.id_cdr, com_json);
                }
               
                var btsAction = '<a href="" class="openModalOuvir" data-id="'+full.id_cdr+'" > \
                <span class="glyphicon glyphicon-headphones">\
                </span> </a> &nbsp';

                btsAction += '<form class="hidden" action="{{route("admin.gravacoes.getGravacao")}}" method="GET" id="formDownload_'+full.id_cdr+'"> <input type="text" name="grav_id" value="'+full.id_cdr+'"> </form><a href="#" class="downloadGravacao" data-id="'+full.id_cdr+'" >\
                <span class="glyphicon glyphicon-download-alt"</a>';

                return btsAction;
                }
            }, {
                targets:4,
                render: function(data, type, full, meta){
                    array_tipos = ['internas', 'saintes', 'entrantes']
                    if(full.audio != undefined && full.audio != ''){
                        array_nome = full.audio.split('/'); 
                        tipo = array_nome[ array_nome.length-4 ]; 
                        if(array_tipos.indexOf(tipo) != -1){
                            len = tipo.length-1;
                            return tipo.substr(0, len);
                        }
                    } 
                    return '?';
                }

            }, {
                targets: 2, 
                render: function(data){
                    data = data.split(' ')[0];
                    campos = data.split('-');
                    data_formatada = campos[2]+'/'+campos[1]+'/'+campos[0];
                    return data_formatada;
                }
            }, {
                targets: 3, 
                render: function(data){
                    hora = data.split(' ')[1];
                    return hora;
                }
            }
            ]
        });
     
         return oTable;
     }
</script>
@endpush
