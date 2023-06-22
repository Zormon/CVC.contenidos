<?php
if ( isset($_POST['mode']) ) {
    switch ($_POST['mode']) {
        case 'overview':
           echo json_encode( home\overview() );
        break;

    }
}

?>