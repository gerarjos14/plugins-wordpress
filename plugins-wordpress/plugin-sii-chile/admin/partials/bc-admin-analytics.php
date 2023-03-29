<?php

/**
  * Proporcionar una vista de área de administración para el plugin
  *
  * Este archivo se utiliza para presentar los informes con los análisis correspondientes de las imágenes que 
 * conforman el sitio.
  *
  * @link http://misitioweb.com
  * @since desde 1.0.0
  *
  * @package Billconnector
  * @subpackage Billconnector/admin/parcials
 * @author Matías 
  */
global $wpdb;
$query = "SELECT * FROM {$wpdb->prefix}bc_results ORDER BY promedio DESC";
$results = $wpdb->get_results($query);
?>
<link href='https://fonts.googleapis.com/css?family=Baloo+2' rel='stylesheet'>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<link rel="stylesheet" href="<?php echo BC_PLUGIN_DIR_URL . 'admin/css/bc-analytics.css' ?>">

<script type="application/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<script type="application/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="application/javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<div class="wrap">

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-home">
                    <div class="card-body">
                        <h5 class="card-title text-analytics title-bc">Resultados análisis imágenes</h5>
                        <p class="card-text text-analytics subtitle-bc">Recuerda que se actualiza cada 24 horas.</p>
                        <button class="btn btn-analytics" data-toggle="modal" data-target=".bd-example-modal-lg">
                            Saber más
                        </button>
                    </div>
                </div>              
            </div>

            <div class="col-md-12">
            <div class="modal fade modal-faq-bc bd-example-modal-lg"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="max-width: max-content; margin-left: 20rem;">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title title-bc" id="exampleModalLabel">BillConnector Analytics</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h5 class="card-title text-analytics title-bc-modal">Informes imágenes</h5>
                            <p class="card-text text-analytics subtitle-bc">
                                Los informes se enviarán a diario, vía email al correo del administrador
                                del sitio. <br>
                                En dicho email se enviarán los reportes en formato pdf de las 5 imágenes más vistas del sitio.
                            </p>
                        <hr>
                            <h5 class="card-title text-analytics title-bc">Recomendaciones</h5>
                            <p class="card-text text-analytics subtitle-bc">Las recomendaciones se clasifican en:</p>
                            <div class="row">
                                <div class="col-sm-6">
                                    <ul>
                                        <li> <b> Tipo A:</b> Excelente</li>
                                        <li> <b>Tipo B:</b> Muy bien</li>
                                        <li> <b>Tipo C:</b> Bien</li>
                                        <li> <b>Tipo D:</b> Regular</li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <ul>
                                        <li> <b>Tipo E:</b> Regular - </li>
                                        <li> <b>Tipo F:</b> Precaución</li>
                                        <li> <b>Tipo G:</b> Cambios</li>
                                        <li> <b>Tipo H:</b> Cambios urgentes</li>
                                        <li> <b>Tipo M:</b> Neutro</li>
                                    </ul>
                                </div>
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary-bc text-bc" data-dismiss="modal">Cerrar</button>
                        </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-12">
                <div class="card card-images">
                    <table id="example" class="table table-responsive" style="width: 100% !important;" aria-describedby="example_info">
                        <thead>
                            <tr>
                                <th style="
                                    width: 100rem !important; 
                                    border-bottom: 0px !important;
                                    border-top: 0px !important;
                                ">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($results as $key) { ?>
                                <tr>
                                    <td style="border-top: 0px !important;">
                                        <div class="card card-data-r" style="width: 50rem !important; display:block; margin:auto;">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <img src="<?php echo $key->url_image ?>" alt="" width="200px" height="200px">
                                                        <p class="text-analytics text-type"> <b> Recomendaciones de bill: </b> </p>
                                                        <p class="text-analytics text-bill">
                                                            <?php
                                                            echo $key->bill_dice;
                                                            ?>
                                                        </p>
                                                    </div>
                                                    <div class="col-sm-8" style="padding: 0rem;">
                                                        <button class="btn btn-type-recommendation-<?php echo $key->type_recomendation; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $key->legend_recomendation; ?>">
                                                            Recomendación tipo: <?php echo $key->type_recomendation; ?>
                                                        </button>
                                                        <p class="text-analytics text-type">
                                                            Fecha:
                                                            <?php echo date("d/m/Y H:i:s", strtotime($key->created_at)); ?>
                                                        </p>
                                                        <h6 class="card-text text-analytics text-type">
                                                            <b> Un usuario en promedio ve esta imagen: </b> <?php echo $key->promedio; ?>
                                                        </h6>
                                                        <h5 class="card-text text-analytics text-type">
                                                            <b> Cantidad de usuarios: </b> <?php echo $key->cant_usuarios ?>
                                                        </h5>
                                                        <h5 class="card-text text-analytics text-type">
                                                            <?php
                                                            echo "Nuevos: " . round(($key->nuevos / $key->cant_usuarios * 100), 2) . "% ";
                                                            echo "Recurrentes: " . round(($key->recurrentes / $key->cant_usuarios *  100), 2) . "%";
                                                            ?>
                                                        </h5>
                                                        <h5 class="card-text text-analytics text-type">
                                                            <b>Intención de clic: </b>
                                                        </h5>
                                                        <img src="<?php echo BC_PLUGIN_DIR_URL . $key->image_producto ?>" alt="" width="500px" height="300px">


                                                    </div>
                                                </div>


                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            <?php
                            }


                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            

        </div>
    </div>
</div>





<script type="application/javascript">
    $(document).ready(function() {
        $('#example').DataTable({
            stateSave: true,
            "aLengthMenu": [
                [5, 10, 25, -1],
                [5, 10, 25, "All"]
            ],
            "iDisplayLength": 5,
            "bDestroy": true,
            "language": {
                "processing": "Procesando...",
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "emptyTable": "Ningún dato disponible en esta tabla",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "infoThousands": ",",
                "loadingRecords": "Cargando...",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "aria": {
                    "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad",
                    "collection": "Colección",
                    "colvisRestore": "Restaurar visibilidad",
                    "copyKeys": "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione escape.",
                    "copySuccess": {
                        "1": "Copiada 1 fila al portapapeles",
                        "_": "Copiadas %d fila al portapapeles"
                    },
                    "copyTitle": "Copiar al portapapeles",
                    "csv": "CSV",
                    "excel": "Excel",
                    "pageLength": {
                        "-1": "Mostrar todas las filas",
                        "_": "Mostrar %d filas"
                    },
                    "pdf": "PDF",
                    "print": "Imprimir"
                },
                "autoFill": {
                    "cancel": "Cancelar",
                    "fill": "Rellene todas las celdas con <i>%d<\/i>",
                    "fillHorizontal": "Rellenar celdas horizontalmente",
                    "fillVertical": "Rellenar celdas verticalmentemente"
                },
                "decimal": ",",
                "searchBuilder": {
                    "add": "Añadir condición",
                    "button": {
                        "0": "Constructor de búsqueda",
                        "_": "Constructor de búsqueda (%d)"
                    },
                    "clearAll": "Borrar todo",
                    "condition": "Condición",
                    "conditions": {
                        "date": {
                            "after": "Despues",
                            "before": "Antes",
                            "between": "Entre",
                            "empty": "Vacío",
                            "equals": "Igual a",
                            "notBetween": "No entre",
                            "notEmpty": "No Vacio",
                            "not": "Diferente de"
                        },
                        "number": {
                            "between": "Entre",
                            "empty": "Vacio",
                            "equals": "Igual a",
                            "gt": "Mayor a",
                            "gte": "Mayor o igual a",
                            "lt": "Menor que",
                            "lte": "Menor o igual que",
                            "notBetween": "No entre",
                            "notEmpty": "No vacío",
                            "not": "Diferente de"
                        },
                        "string": {
                            "contains": "Contiene",
                            "empty": "Vacío",
                            "endsWith": "Termina en",
                            "equals": "Igual a",
                            "notEmpty": "No Vacio",
                            "startsWith": "Empieza con",
                            "not": "Diferente de"
                        },
                        "array": {
                            "not": "Diferente de",
                            "equals": "Igual",
                            "empty": "Vacío",
                            "contains": "Contiene",
                            "notEmpty": "No Vacío",
                            "without": "Sin"
                        }
                    },
                    "data": "Data",
                    "deleteTitle": "Eliminar regla de filtrado",
                    "leftTitle": "Criterios anulados",
                    "logicAnd": "Y",
                    "logicOr": "O",
                    "rightTitle": "Criterios de sangría",
                    "title": {
                        "0": "Constructor de búsqueda",
                        "_": "Constructor de búsqueda (%d)"
                    },
                    "value": "Valor"
                },
                "searchPanes": {
                    "clearMessage": "Borrar todo",
                    "collapse": {
                        "0": "Paneles de búsqueda",
                        "_": "Paneles de búsqueda (%d)"
                    },
                    "count": "{total}",
                    "countFiltered": "{shown} ({total})",
                    "emptyPanes": "Sin paneles de búsqueda",
                    "loadMessage": "Cargando paneles de búsqueda",
                    "title": "Filtros Activos - %d"
                },
                "select": {
                    "cells": {
                        "1": "1 celda seleccionada",
                        "_": "$d celdas seleccionadas"
                    },
                    "columns": {
                        "1": "1 columna seleccionada",
                        "_": "%d columnas seleccionadas"
                    },
                    "rows": {
                        "1": "1 fila seleccionada",
                        "_": "%d filas seleccionadas"
                    }
                },
                "thousands": ".",
                "datetime": {
                    "previous": "Anterior",
                    "next": "Proximo",
                    "hours": "Horas",
                    "minutes": "Minutos",
                    "seconds": "Segundos",
                    "unknown": "-",
                    "amPm": [
                        "AM",
                        "PM"
                    ],
                    "months": {
                        "0": "Enero",
                        "1": "Febrero",
                        "10": "Noviembre",
                        "11": "Diciembre",
                        "2": "Marzo",
                        "3": "Abril",
                        "4": "Mayo",
                        "5": "Junio",
                        "6": "Julio",
                        "7": "Agosto",
                        "8": "Septiembre",
                        "9": "Octubre"
                    },
                    "weekdays": [
                        "Dom",
                        "Lun",
                        "Mar",
                        "Mie",
                        "Jue",
                        "Vie",
                        "Sab"
                    ]
                },
                "editor": {
                    "close": "Cerrar",
                    "create": {
                        "button": "Nuevo",
                        "title": "Crear Nuevo Registro",
                        "submit": "Crear"
                    },
                    "edit": {
                        "button": "Editar",
                        "title": "Editar Registro",
                        "submit": "Actualizar"
                    },
                    "remove": {
                        "button": "Eliminar",
                        "title": "Eliminar Registro",
                        "submit": "Eliminar",
                        "confirm": {
                            "_": "¿Está seguro que desea eliminar %d filas?",
                            "1": "¿Está seguro que desea eliminar 1 fila?"
                        }
                    },
                    "error": {
                        "system": "Ha ocurrido un error en el sistema (<a target=\"\\\" rel=\"\\ nofollow\" href=\"\\\">Más información&lt;\\\/a&gt;).<\/a>"
                    },
                    "multi": {
                        "title": "Múltiples Valores",
                        "info": "Los elementos seleccionados contienen diferentes valores para este registro. Para editar y establecer todos los elementos de este registro con el mismo valor, hacer click o tap aquí, de lo contrario conservarán sus valores individuales.",
                        "restore": "Deshacer Cambios",
                        "noMulti": "Este registro puede ser editado individualmente, pero no como parte de un grupo."
                    }
                },
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros"
            }
        });
    });
</script>