<?php
global $login;

if ( isset($_POST['mode']) ) {
    switch ($_POST['mode']) {
        case 'add': 
            if ( $login->can['editShops'] ) {
                $img = ( isset($_FILES['imagen']) && is_uploaded_file($_FILES['imagen']['tmp_name']) )? $_FILES['imagen'] : false;

                shops\add($_POST, $img);
                echo json_encode(["status" => 'ok' ]);
            } else {
                echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
            }
        break;
        
        case 'edit':
            if ( $login->can['editShops'] ) {
                $img = ( isset($_FILES['imagen']) && is_uploaded_file($_FILES['imagen']['tmp_name']) )? $_FILES['imagen'] : false;

                shops\edit($_POST, $img);
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