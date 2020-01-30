<!-- / Modal msg for user  -->
<div class="modal fade alert-warning" id="modalMsg" tabindex="-7" role="dialog" aria-labelledby="modalMsg" aria-hidden="true">
    
  <div class="modal-dialog " role="document">
      
    <div class="modal-content " >
        
      <div class="modal-header ">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h1 class="modal-title" id="modalMsgTitle">{{ $titulo or '' }}</h1>
      </div>
        

      <div class="modal-body">
        <div  id = "textMsg" >{{ $msg or '' }}</div>
      </div>
    
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
      </div>
      
    </div><!-- ./modal-content -->
  </div><!-- ./modal-dialog -->
</div><!-- ./modal -->