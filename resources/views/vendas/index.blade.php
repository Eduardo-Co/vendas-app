@extends('adminlte::page')

@section('title', 'Vendas')

@section('content')
        
    @livewire('vendas')
    @livewireScripts
    @livewireStyles

@stop

