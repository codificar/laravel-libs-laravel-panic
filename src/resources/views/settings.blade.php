@extends('admin_painel.painel_layout')

@section('content')
<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor m-b-0 m-t-0">Panic Settings</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item">Panic</li>
            <li class="breadcrumb-item active">Settings</li>
        </ol>
    </div>
</div>
<div>
    <form>
        @csrf
        <h4>Segup Email</h4>
        <input name='email' type='email' placeholder='Email' />
        <br>
        <h4>Segup Password</h4>
        <input name='password' type='password' placeholder='Password' />
        <br>
        <h4>Segup true</h4>
        <input name='password_confirmation' type='password' placeholder='Confirm Password' />
    </form>
</div>
@endsection
