
<link rel="stylesheet" href="<?php echo lars_pos_PLUGIN_DIR_URL . 'public/css/video-js.css' ?>  ">
<script src="<?php echo lars_pos_PLUGIN_DIR_URL . 'public/js/video.js'?>"></script>
<link rel="stylesheet" href="<?php echo lars_pos_PLUGIN_DIR_URL . 'public/css/estilos.css' ?>">
<link rel="stylesheet" href="<?php echo lars_pos_PLUGIN_DIR_URL . 'public/css/tutorial.css' ?>">

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content modal-sc-tutorial">
      <div class="modal-header">
        <h5 class="modal-title text-sp"><b>Ver video tutorial</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <video class="fm-video video-js vjs-16-9 vjs-big-play-centered" data-setup="{}" controls id="fm-video">
				<!-- <source src="<?php echo lars_pos_PLUGIN_DIR_URL . 'resources/tutorial.mp4'?>" type="video/mp4"> -->
			</video>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary-sp" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>