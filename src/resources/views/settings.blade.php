<?php $layout = '.master'; ?>

@extends('layout'.$layout)

@section('content')
<div id='VueJS'>
</div>
@endsection


@section('breadcrumbs')
<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">

        <h3 class="text-themecolor m-b-0 m-t-0">SETTINGS</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">HOME</a></li>
            <li class="breadcrumb-item active">SETTINGS HOME ACTIVE</li>
        </ol>
    </div>
</div>
@stop

@section('javascripts')
{{-- <script src="{{ elixir('vendor/codificar/laravel-panic/panic.vue.js') }}"> </script> --}}
@endsection
