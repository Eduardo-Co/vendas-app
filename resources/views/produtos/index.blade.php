@extends('adminlte::page')

@section('title', 'Produtos')

@section('content')
        
    @livewire('produtos')
    @livewireScripts
    @livewireStyles

@stop

