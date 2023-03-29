<link rel="stylesheet" href="<?php echo BILL_PLUGIN_DIR_URL . 'admin/css/faq.css' ?>">


<div id="accordion">
  <?php
    if($faq){
        foreach($faq as $key){ ?>            
            <div class="card card-faq">
                <div class="card-header header-faq" id="heading-<?php echo $key->id;?>">
                    <h5 class="mb-0 title-card-faq">
                      <button class="btn btn-link title-card-faq" data-toggle="collapse<?php echo $key->id;?>" data-target="#collapseOne" aria-expanded="true" aria-controls="collapse<?php echo $key->id;?>">
                        <?php echo $key->pregunta_faq; ?>
                      </button>
                    </h5>
                </div>

                <div id="collapse<?php echo $key->id;?>" class="collapse collapse-faq show" aria-labelledby="heading-<?php echo $key->id;?>" data-parent="#accordion">
                    <div class="card-body">
                        <?php echo $key->answer; ?>
                    </div>
                </div>
            </div>
        <?php
        }
    }
  ?>
</div>