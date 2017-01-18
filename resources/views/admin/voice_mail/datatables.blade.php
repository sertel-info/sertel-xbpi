<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="groups-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Remetente</th>
                        <th>Destinatário</th>
                        <th>Ramal</th>
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
    <script>
    $(function() {
        $('#groups-table').dataTable({
            processing: true,
            serverSide: true,
            "ajax": {
                'url': '{!! route('datatables.voice_mail') !!}',
                'type': 'GET',
                
            },
            columns: [
                { data: 'nome', name: 'nome' },
                { data: 'remetente', name: 'remetente' },
                { data: 'destino', name: 'destino' },
                { data: 'numero_ramal', name: 'numero_ramal' }
            ],
            "columnDefs": [{
                "targets": 4,
                "render": function ( data, type, full, meta ) {
                  json = JSON.stringify(full);
                  sessionStorage.setItem(full.id, json);
                  
                  var btsActions = '<a href="javascript:showEdit('+full.id+')" class="controlls-edit" data-toggle="tooltip" title="Editar"><i class="fa fa-pencil-square-o"></i></a>';
                  //if(full.count_users<1){
                      btsActions += '<a data-id="'+full.id+'" data-action="confirm-delete" data-title="Exclusão" data-text="Deseja realmente deletar esse grupo?" href="{{route('admin.voice_mail.destroy')}}" class="controlls-del" data-toggle="tooltip" title="Excluir"><i class="fa fa-times"></i></i></a>';
                  //}else{
                      // btsActions += '<a data-title="Ação negada" class="controlls-del" data-toggle="tooltip" title="Ação negada"><i class="fa fa-times"></i></i></a>';
                  //}
                  return btsActions;
                }
              }]
        });
    });
    </script>
@endpush
