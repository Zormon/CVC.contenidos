<?php
global $login;

switch ($_POST['mode']) {
    case 'details':
        echo json_encode( groups\details( array($_POST['id']) ) );
    break;

    case 'add': 
        if ( $login->can['editGroups'] ) {
            $img = is_uploaded_file($_FILES['imagen']['tmp_name'])? $_FILES['imagen'] : false;
            
            groups\add($_POST, $img);
            echo json_encode(["status" => 'ok' ]);
        } else {
            echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
        }
    break;
    
    case 'edit':
        if ( $login->can['editGroups'] ) {
            $img = is_uploaded_file($_FILES['imagen']['tmp_name'])? $_FILES['imagen'] : false;
            groups\edit($_POST, $img);
            echo json_encode(["status" => 'ok' ]);
        } else {
            echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
        }
    break;

    case 'delete':
        if ( $login->can['editGroups'] ) {
            groups\delete( $_POST['id'] );
            echo json_encode(["status" => 'ok' ]);
        }  else {
            echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
        }
    break;

    case 'list':
        echo json_encode(groups\listado());
    break;
}

?>