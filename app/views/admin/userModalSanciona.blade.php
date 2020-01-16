<div class="modal fade" id="modal-sanciona-usuario" tabindex="-2" role="dialog" aria-labelledby="modal-sanciona-user">

    <div class="modal-dialog modal-lg">
        
        <div class="modal-content">
            
            <div class="modal-header">
                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3><i class="fa fa-lock fa-fw" title='Sancionar'></i>Sancionar usuario </h3>
                <p>Información usuario, nombre, aplledios, uvus....</p>
            </div><!-- .modal-header -->
            
            <div class="modal-body">
              
                @if (Session::has('message'))
                        
                    <div class="alert {{ Session::has('alertType') or 'alert-warning' }} alert-dismissable">
                      
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> {{ Session::get('message') }}
                    </div>
                @endif

                {{ Form::open(array('method' => 'POST','route' => 'sancionaUser','role'=>'form')) }}
                               
          
                    <div class="form-group">
                        
                        <label for="motivo-sancion"  class="control-label" >Motivo sanción: </label> 
                        
                        <div class = "">
                            <textarea name="motivoSancion" class="form-control" rows="10">
                                {{--  --}}
                            </textarea>
                        </div>
                    </div>
     

                    <div class="form-group hidden">
            
                        <input type="text" name="userId" value="" class="form-control" />
                    </div>
      
                {{Form::close()}}
            </div><!-- .modal-body -->

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div><!-- .modal-footer -->    
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog .modal-lg-->
</div><!-- /#modal-sanciona-user