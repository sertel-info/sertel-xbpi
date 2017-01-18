@extends('admin.base')
@section('content')
<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Listagem de ramas
        </h1>
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-dashboard"></i>  <a href="{{route('admin.ramais.index')}}">Ramais</a>
            </li>
            <li class="active">
                <i class="fa fa-list-alt"></i> Listagem de ramais
            </li>
        </ol>
    </div>
</div>
<!-- /.row -->
<!-- Page Heading -->
<div class="row">
    <div class="col-lg-8">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Ramal 1</td>
                        <td>
                            <a href="{{route('admin.ramais.update')}}" class="controlls-edit" data-toggle="tooltip" title="Editar"><i class="fa fa-pencil-square-o"></i></a>
                            <a href="#" class="controlls-del" data-toggle="tooltip" title="Excluir">
                            <i class="fa fa-times"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
  
    </div>
</div>
<!-- /.row -->
@endsection
