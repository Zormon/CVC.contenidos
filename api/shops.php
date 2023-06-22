<?php
global $login;

if ( isset($_POST['mode']) ) {
    switch ($_POST['mode']) {
        case 'add': 
            if ( $login->can['editShops'] ) {
                shops\add($_POST);
                echo json_encode(["status" => 'ok' ]);
            } else {
                echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
            }
        break;
        
        case 'edit':
            if ( $login->can['editShops'] ) {
                shops\edit($_POST);
                echo json_encode(["status" => 'ok' ]);
            } else {
                echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
            }
        break;

        case 'delete':
            if ( $login->can['editShops'] ) {
                //TODO: borrar la shop del campo de todos los usuarios
                shops\delete( $_POST['id'] );
                echo json_encode(["status" => 'ok' ]);
            }  else {
                echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
            }
        break;

        case 'list':
            echo json_encode( shops\listado( array_column($login->shops, 'id') ) );
        break;
    }
}

?>