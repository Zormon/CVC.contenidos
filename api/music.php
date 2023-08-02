<?php
global $login;

if ( isset($_POST['mode']) ) {
    switch ($_POST['mode']) {
        case 'addSong': 
            if ( !$login->can['editMusic'] ) { exit( '{"status": "ko", "error": "Permiso denegado"}' ); }

            try {
                music\song\add($_POST, $_FILES);
                echo json_encode(["status" => 'ok' ]);
                if (isset($_POST['addToChannel'])) { \cache\clearAll(\cache\type::deploy); }
            } catch( Exception $e) {
                exit( '{"status": "ko", "error": '.$e->getMessage().'}' );
            }
        break;
        
        case 'editSong':
            if ( !$login->can['editMusic'] ) { exit( '{"status": "ko", "error": "Permiso denegado"}' ); }

            try {
                music\song\edit($_POST);
                echo json_encode(["status" => 'ok' ]);
                \cache\clearAll(\cache\type::deploy);
            } catch( Exception $e) {
                exit( '{"status": "ko", "error": '.$e->getMessage().'}' );
            }
        break;
    
        case 'deleteSongs':
            if ( !$login->can['editMusic'] ) { exit( '{"status": "ko", "error": "Permiso denegado"}' ); }

            music\song\delete( $_POST['ids'] );
            echo json_encode(["status" => 'ok' ]);
            \cache\clearAll(\cache\type::deploy);
        break;
    
        case 'listSong':
            echo json_encode(music\song\listado(false, true));
        break;


        // Playlists
        case 'listPlaylists':
            echo json_encode( \music\playlist\listado() );
        break;

        case 'savePlaylist':
            if ( $login->can['editMusic'] && \music\playlist\save($_POST)) {
                echo json_encode(["status" => 'ok' ]);
            } else {
                echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
            }
        break;

        case 'deletePlaylist':
            if ( $login->can['editMusic'] && \music\playlist\delete( $_POST['id'] )) {
                echo json_encode(["status" => 'ok' ]);
            }  else {
                echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
            }
        break;

        case 'm3u8Upload':
            if ( $login->can['editMusic'] ) {
                music\playlist\m3u8Upload($_POST['channel'], file_get_contents($_FILES['m3u8File']['tmp_name']));
                echo json_encode(["status" => 'ok' ]);
				\cache\clearAll(\cache\type::deploy);
            } else {
                echo json_encode(["status" => 'ko', "error" => 'Permiso denegado' ]);
            }
        break;

    }
}

?>