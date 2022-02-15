<?php $layout = '.master'; ?>
@extends('layout'.$layout)

@section('breadcrumbs')
	<div class="row page-titles">
		<div class="col-md-6 col-8 align-self-center">
			<h3 class="text-themecolor m-b-0 m-t-0">Relatório do botão de pânico</h3>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
				<li class="breadcrumb-item active">Relatório do botão de pânico</li>
			</ol>
		</div>
	</div>
@stop

@section('content')
	<div id="panic">
		<panicreport/>		
	</div>
@stop

@section('javascripts')
	<script src="/libs/panic/lang.trans/panic"> </script> 
	<script src="{{ asset('vendor/codificar/panic/panic.vue.js') }}"> </script> 
@stop

