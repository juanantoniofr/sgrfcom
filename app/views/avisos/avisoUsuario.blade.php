@extends('layout')

@section('title')
    Acceso para administradores: Inicio
@stop

@section('content')
<div class="container">
    <div class="row">
        
        <div class="panel panel-info">
            
            <div class="panel-heading">
                    <h1><i class="fa fa-exclamation-triangle fa-fw"></i> Aviso: {{ $title }} </h1>
            </div><!-- ./panel-heading -->

            <div class="panel-body">
                  
                <div class="alert alert-{{ $alert or 'warning' }}" role="alert" id="aviso">
                        
                    <p id="msgUser"> {{ $msg or '' }}</p>

                </div>
            </div><!-- ./panel-body --> 
            
        </div><!-- /.panel-info -->
    </div><!-- /.row -->
</div> <!-- /.container -->
@stop