<?php
global $login;

if ( isset($_POST['mode']) ) {
    switch ($_POST['mode']) {
        case 'details':
            echo json_encode( devices\details( array($_POST['id']) ) );
        break;

        case 'add': 
            if ( $login->can['editDevices'] ) {
                devices\add($_POST);
                echo json_encode(["status" => 'ok' ]);
            } else {
                echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
            }
        break;
        
        case 'edit':
            if ( $login->can['editDevices'] ) {
                devices\edit($_POST);
                \cache\clear( array($_POST['id']), \cache\type::deploy);

                echo json_encode(["status" => 'ok' ]);
            } else {
                echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
            }
        break;

        case 'delete':
            if ( $login->can['editDevices'] ) {
                devices\delete( $_POST['id'] );
                \cache\clear( array($_POST['id']), \cache\type::all );

                echo json_encode(["status" => 'ok' ]);
            }  else {
                echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
            }
        break;

        case 'list':
            echo json_encode(devices\listado( false, array_column($login->shops, 'id') ), JSON_HEX_QUOT);
        break;
    }
}

?>