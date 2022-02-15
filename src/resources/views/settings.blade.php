<?php $layout = '.master'; ?>
       
@extends('layout'.$layout)

@section('breadcrumbs')
	<div class="row page-titles">
		<div class="col-md-6 col-8 align-self-center">
			<h3 class="text-themecolor m-b-0 m-t-0">{{ trans('settingsTrans::setting.conf')}}</h3>
			
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="javascript:void(0)">{{ trans('settingsTrans::setting.home') }}</a></li>
				<li class="breadcrumb-item active">{{ trans('panic::panic.panic_button') }}</li>
			</ol>
		</div>
	</div>	
@stop

@section('content')
	<div id="panic">
		<panicsettings/>		
	</div>
@stop

@section('javascripts')
	<script src="/libs/panic/lang.trans/panic"> </script> 
	<script src="{{ asset('vendor/codificar/panic/panic.vue.js') }}"> </script> 
@stop
