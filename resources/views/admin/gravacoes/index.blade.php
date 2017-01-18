@extends('admin.base')
@section('content')
 <meta name='_token' content='{{csrf_token()}}'>
 <meta name='_act_id' content=''>
        <!-- Page Heading -->
<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                Gravações
            </h1>
            <ol class="breadcrumb">
                <li class="active">
                    <i class="fa fa-list"></i> &nbsp Gravações
                </li>
            </ol>                   
      </div>
</div>

@include('admin.gravacoes.formfiltro')

@include('admin.gravacoes.datatables')

@include('admin.gravacoes.modalform')

@endsection

@push('scripts')
@endpush
        <!-- Simple List -->

<!-- /.row -->
