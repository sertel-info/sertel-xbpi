<center>
        <audio src="/assets/audios/dsadas.wav"></audio>
        <br>
        <a id='com_add' href="#"> <span style='margin-bottom:20px' class="glyphicon glyphicon-comment" aria-hidden="true"></span> Adicionar Comentário </a>
        <div id='controlComent' style='display:none'>
            <form id='formComGravacao' method="POST">
                <br>
                <label> Tempo </label>
                <input style='text-align:center' class='form-control' type='text' name='com_tempo'><br>
                <label> Comentário </label>
                <textarea class='form-control' name='com_txt' maxlength="150" style="max-width:100%; min-height:15%; resize:vertical"></textarea>
                <input type='submit' class='btn btn-primary' value='Ok'> 
            </form>
        </div>
         <div class='col-md-12' id='com_container'> </div>
</center>


@push('scripts')
<script type="text/javascript">
    function removeComment(event){
        event.preventDefault();
        token = $("meta[name=_token]").prop('content');



        $.post('{{route("admin.gravacoes.comentarios.remove")}}', {"com_id" : event.data.id, "_token" : token} , function(resp){
            if(resp){
                event.data.el.closest('.well').remove();
                var coments = JSON.parse(sessionStorage.getItem('com_'+event.data.gravacao));

                console.log(coments, event.data.id);
                for(var i = 0 ; i<=coments.length-1; i++){
                    if( coments[i].id == event.data.id ){
                        coments.splice(i, 1);
                    }
                }

                if(coments.length)
                    sessionStorage.setItem('com_'+event.data.gravacao, JSON.stringify(coments));
                else
                    sessionStorage.removeItem('com_'+event.data.gravacao);

                tipo = "success";
                msg = "Comentário removido com sucesso";
            } else if(resp == -1){
                tipo = "warning";
                msg = "Você não possui permissão para excluir este comentário";
            } else if(resp == 0){
                tipo = "warning";
                msg = "Um erro inesperado aconteceu, tente novamente";
            }

            toastr[tipo](msg);
        });   
    }
    
    function appendComment(tempo, txt, gravacao,id, dono=false){
        var removeBtn = '';
        var player = $('#myModal').find('audio')[0];
        player = $(player);
        

        if(dono){
            removeBtn = '<a class="c_coment" href="#" style="float:right"> <i class="fa fa-times" aria-hidden="true"></i> </a>';
        }
        
        novo_com_DOM = "<div class='row well'>\
                                             "+removeBtn+"<a href='#' class='c_time'><label>"+tempo+"</label></a>\
                                             <p>"+txt+"</p>\
                                             \
                                        </div>";

        dom = $(novo_com_DOM);
            
        $("#com_container").append(dom);
        
        if(dono){
            el = dom.find('.c_coment');
            el.on('click', {'el' : el, 'id': id, 'gravacao': gravacao}, removeComment);
        }
        
        dom.find('.c_time').on('click',function(event){
                        event.preventDefault();
                        player[0].player.media.currentTime = TimeToSec(tempo);
        });
        
        return novo_com_DOM;
    }

    function SecToTime(totalSeconds){
        hours = Math.floor(totalSeconds / 3600);
        hours = parseInt(hours);

        if(hours<10){
            hours = '0'+hours;
        }

        totalSeconds %= 3600;
        minutes = Math.floor(totalSeconds / 60);

        if(minutes<10){
            minutes = '0'+minutes;
        }

        seconds = totalSeconds % 60;
        seconds = parseInt(seconds);

        if(seconds<10){
            seconds = '0'+seconds;
        }

        return hours+':'+minutes+':'+seconds;
    }

    $('#myModal').on('hidden.bs.modal', function(){
        modal = $(this);
        player = modal.find('audio')[0];
        player = $(player);
        player[0].player.load();

        $("input[name=com_tempo").val('');
        $("textarea[name=com_txt").val('');
        $("#controlComent").hide();
        $("#com_container").empty();
        
    });


    $(function(){
        $("#com_add").on('click', function(event){
            player = $('#myModal').find('audio')[0];
            player = $(player);
            
            var atual = player[0].player.media.currentTime;

            $("input[name=com_tempo]").val( SecToTime(atual) );

            $("#controlComent").toggle();
        });

        $("#formComGravacao").on('submit', function(event){
            event.preventDefault();
            
            player = $('#myModal').find('audio')[0];
            player = $(player);

            token = $("meta[name=_token]").prop('content');
            tempo = $("input[name=com_tempo]").val();
            gravacao = $("meta[name=_act_id]").prop('content');
            dono = "{{Auth::User()->id}}";
            txt = $("textarea[name=com_txt]").val();

            if(txt == undefined || txt == ''){
                toastr["warning"]("Digite um comentário");
                return;
            } 
            if(tempo == undefined || tempo.length < 5 || TimeToSec(tempo) >= player[0].player.media.duration ){
                toastr["warning"]("Tempo inválido");
                return;
            }

            data = {
                "com_txt": txt,
                "com_gravacao": gravacao,
                "com_tempo": tempo,
                "com_dono": dono,
                "_token":token
            }

            $.post("{{route('admin.gravacoes.comentarios.add')}}", data, function(resp){
                if(resp){
                    
                    comentarios = sessionStorage.getItem("com_"+data.com_gravacao);
                    //comentarios_json = JSON.parse(comentarios);

                    var novo = {"txt":data.com_txt,
                        "tempo":data.com_tempo,
                        "dono":data.com_dono,
                        "id":resp
                    };
                    
                    if(comentarios != null ){
                       comentarios = comentarios.slice(1, comentarios.length-1);
                       comentarios += ','+JSON.stringify(novo);
                    } else {
                       comentarios = JSON.stringify(novo);
                    }

                    comentarios = '['+comentarios+']';

                    sessionStorage.setItem("com_"+data.com_gravacao, comentarios);
                    toastr["success"]("Comentário adicionado com sucesso");

                    appendComment(data.com_tempo, data.com_txt, data.com_gravacao,  resp, true);
                    
                    var comentForm = $("#controlComent");
                    comentForm.hide();
                    comentForm.find('textarea').val('');

                } else {
                    toastr["danger"]("Um erro inesperado ocorreu, tente novamente");
                }
            });


        });
    });
</script>
@endpush