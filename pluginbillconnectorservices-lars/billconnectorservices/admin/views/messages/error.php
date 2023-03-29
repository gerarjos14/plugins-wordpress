<div class="col-sm-12">
    <div class="card text-white bg-warning card-no-plugin">
        <div class="card-body">
            <h4 class="text-sp card-title">Alerta</h4>
            <h6 class="text-sp"><b>Los siguientes servicios se encuentran vencidos</b></h6>
            
            <ul>
                <?php
                    foreach($check_services_defeated as $key){?>
                    <li> - <?php echo $key->name; ?></li>
                        
                    <?php
                    }
                ?>
            </ul>
        </div>
    </div>
</div>