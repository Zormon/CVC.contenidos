<?php
global $login;

if ( isset($_POST['mode']) ) {
    switch ($_POST['mode']) {
        case 'details':
            echo json_encode( events\details( array($_POST['id']) ) );
        break;

        case 'add': 
            if ( $login->can['editEvents'] ) {
                events\add($_POST);
                echo json_encode(["status" => 'ok' ]);
            } else {
                echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
            }
        break;
        
        case 'edit':
            if ( $login->can['editEvents'] ) {
                events\edit($_POST);
                \cache\clear( array($_POST['id']), \cache\type::deploy);

                echo json_encode(["status" => 'ok' ]);
            } else {
                echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
            }
        break;

        case 'delete':
            if ( $login->can['editEvents'] ) {
                events\delete( $_POST['id'] );
                \cache\clear( array($_POST['id']), \cache\type::deploy );

                echo json_encode(["status" => 'ok' ]);
            }  else {
                echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
            }
        break;

        case 'list':
            echo json_encode(events\query( false, array_column($login->devices, 'id') ), JSON_HEX_QUOT);
        break;
    }
}

?>