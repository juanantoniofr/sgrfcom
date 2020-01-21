<!-- modal elimina Sanción -->
<div class="modal fade" id="modalEliminaSancion" tabindex="-4" role="dialog" aria-labelledby="eliminaSancion" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
      
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title">Eliminar Sanción</h4>
            </div>

            <div class="modal-body">
        
                <div class="alert alert-danger" role = "alert">¿Estás seguro que deseas <b>eliminar  la sanción</b> del usuario: "<b><span id="infouser"></span>"</b> ?</div>

                <div class="alert alert-info" role = "info"><span id="infosancion"></span></div>
                
            </div><!-- ./.modal-body -->

            <div class="modal-footer">
                <a class="btn btn-primary" href="" role="button" id="btnEliminaSancion"><i class="fa fa-unlock fa-fw"></i> Eliminar</a>

                
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div><!-- ./.modal-content -->
    </div><!-- ./.modal-dialog -->
</div><!-- ./#modalborrarRecurso -->