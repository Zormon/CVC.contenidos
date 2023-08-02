<?php
global $login;

switch ($_POST['mode']) {
    case 'list':
        echo json_encode( media\listado($_POST['status']) );
    break;

    case 'details':
        echo json_encode( media\details($_POST['id']) );
    break;

    case 'parrilla':
        echo json_encode( media\parrilla($_POST['equipo']) );
    break;

    case 'enable':
        media\status( $_POST['id'], media\ENABLED );
        \cache\clearAll(\cache\type::deploy);

        echo json_encode( ["status" => 'ok', "id" => $_POST['id']] );
    break;

    case 'disable':
        media\status( $_POST['id'], media\DISABLED );
        \cache\clearAll(\cache\type::deploy);

        echo json_encode( ["status" => 'ok', "id" => $_POST['id']] );
    break;

    case 'edit':
        if ( !$login->can['editMedia'] ) { exit( '{"status": "ko", "error": "Permiso denegado"}' ); }
        
        try {
            media\edit($_POST, $_FILES);
            echo json_encode( ["status" => 'ok'] );
            \cache\clear($_POST['devices'], \cache\type::deploy);
        } catch( Exception $e ) {
            exit( '{"status": "ko", "error": "'.$e->getMessage().'"}' );
        }
    break;

    case 'add':
        if ( !$login->can['editMedia'] ) { exit( '{"status": "ko", "error": "Permiso denegado"}' ); }

        try {
            media\add($_POST, $_FILES);
            echo json_encode( ["status" => 'ok'] );
            \cache\clear($_POST['devices'], \cache\type::deploy);
        } catch( Exception $e) {
            exit( '{"status": "ko", "error": "'.$e->getMessage().'"}' );
        }
    break;

    case 'delete':
        $ids = explode(',', $_POST['ids']);
        media\delete($ids);
        \cache\clearAll(\cache\type::deploy);

        echo json_encode(["status" => 'ok', "ids" => $_POST['ids']]);
    break;


    // Playlists
    case 'listPlaylists':
        echo json_encode( \media\playlist\listado() );
    break;

    case 'savePlaylist':
        if ( $login->can['editMusic'] && \media\playlist\save($_POST)) { 
            echo json_encode( ["status" => 'ok'] );
        } else {
            echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]); 
        }
    break;

    case 'deletePlaylist':
        if ( $login->can['editMusic'] && \media\playlist\delete($_POST['id'])) {
            echo json_encode( ["status" => 'ok'] );
        } else { 
            echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]); 
        }
    break;
}

?>