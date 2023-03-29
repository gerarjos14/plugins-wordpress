<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<?php

require_once plugin_dir_path(__FILE__) . '/handle-db.php';

   $urlAPI = 'http://tpv-chelo.test/api/api/api_categories/list';
   $args = array('method'=>'GET');
   $response = wp_remote_request($urlAPI, $args);
   $categoriesList = json_decode(wp_remote_retrieve_body($response), true);
   $categories = $categoriesList;
    if(empty($categories)){
        echo'<div class="row">';
                echo'<div class="col-md-6" style="margin: auto; display:block">';
                    echo'<div class="card alert alert-danger error-api">';
                        echo'<div class="card-body">';
                            echo'<h6 class="text-sp"><b>Error al mostrar las categorías. Por favor, revisa tu conexión a internet</b></h6>';
                        echo'</div>';
                    echo'</div>                   ';
                echo'</div>';
            echo'</div>';
        
    }else{
        echo'<table class="table table-bordered">';
        echo'<thead class="thead-dark thead-dark-sp">';
        echo'<th class="text-sp txt-tables" style="text-align:center;" width="10%">#</th>';
        echo'<th class="text-sp txt-tables" style="text-align:center;" width="30%">Nombre</th>';
        echo'</tr></thead><tbody>';

        foreach($categories as $key => $category)
        {
            

            $data_category = check_percentage($category['id']);
            if(!($data_category->increase)) 
                $increase = 0;
            else 
                $increase = $data_category->increase;
            
            //echo $increase;
            echo"<tr>";
                echo'<td style="text-align: center;">'. ($key + 1) . '</td>';
                echo'<td style="text-align: center;"> <b>'.$category['name'].'</b></td>';                
                
            echo"</tr>";
        }
        echo"</tbody></table>";
    }
?>

