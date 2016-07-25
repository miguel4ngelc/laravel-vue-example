@extends('layouts.app')

@section('content-header')
Nivel de agua en plantas
@endsection


@section('content-breadcrumb')
<li><a href="{{ route('plantas.niveles.index') }}">Niveles de agua</a></li>
@endsection


@section('content')

<div class="row">
  <div class="col-xs-12">

    <div class="box">
      <div class="box-header with-border">
        <h1>Lista de niveles</h1>
        @include('flash::message')
      </div>

      <div class="box-body">

        <div class="row">
          <div class="col-xs-12">
            <button class="btn btn-primary pull-right" type="button" onclick="$('#crear-nivel-modal').modal('show');">
              <span class="fa fa-plus"></span> Nuevo nivel
            </button>
          </div>
        </div>

        @if($niveles->isEmpty())
          <div class="well text-center">No hay niveles definidos</div>
        @else
          @include('plantas.niveles.index-table')
        @endif
      </div>

      <div class="box-footer">

      </div>
    </div> <!-- .box -->

  </div>
</div>

@include('plantas.niveles.create')

@endsection
