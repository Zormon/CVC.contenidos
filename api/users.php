<?php
global $login;

if ( isset($_POST['mode']) ) {
    if ($login->can['editUsers']) {
        switch ($_POST['mode']) { // User admins
            case 'add':
                if ( $login->can['editUsers'] ) {
                    $img = is_uploaded_file($_FILES['imagen']['tmp_name'])? $_FILES['imagen'] : false;
                    $tmpPass = users\add($_POST, $img);
                    echo json_encode(["status" => 'ok', "tmpPass" => $tmpPass ]);
                } else {
                    echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
                }
            break;

            case 'edit':
                if ( $login->can['editUsers'] ) {
                    $img = is_uploaded_file($_FILES['imagen']['tmp_name'])? $_FILES['imagen'] : false;
                    if ( users\edit($_POST, $img) !== false ) { echo json_encode(["status" => 'ok' ]); }
                } else {
                    echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
                }
            break;

            case 'delete':
                if ( $login->can['editShops'] ) {
                    //TODO: borrar informacion derivada
                    users\delete( $_POST['id'] );
                    echo json_encode(["status" => 'ok' ]);
                }  else {
                    echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
                }
            break;
        }

        switch ($_POST['mode']) { // Normal users
            case 'list':
                echo json_encode( users\listado() );
            break;
        }
    }
}

?>