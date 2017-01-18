@extends('admin.base')
@section('content')
        <!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Detectar Hardwares
        </h1>
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-list"></i>  <a href="{{route('admin.troncos.index')}}">Troncos</a>
            </li>
            <li>
                 <a href="{{route('admin.hardware.index')}}">Cadastrar Hardwares</a>
            </li>
            <li class="active">
                </i> KHOMP
            </li>
        </ol>
        <div class=''>
            @include('admin.hardware.hardwareDataTableDetect')
    </div>
    </div>
  
</div>

@endsection

@push('scripts')
<script src="/assets/js/jquery.maskedinput.min.js"> 

</script>


<script>

$(".ip").mask("999.999.999.999");

</script>
@endpush