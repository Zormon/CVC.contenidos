<?php
switch ($_POST['mode']) {
    case 'pushNews':
        $news = array(
            'pending'       => $mysql->consulta( "SELECT count(id) AS num FROM media WHERE estado=" . \media\DISABLED )[0]['num'],
            'emision'       => $mysql->consulta( "SELECT count(id) AS num FROM media WHERE estado=" . \media\ENABLED . " AND dateFrom <= CURDATE() AND dateTo >= CURDATE()" )[0]['num'],
            'futuros'   => $mysql->consulta( "SELECT count(id) AS num FROM media WHERE estado=" . \media\ENABLED . " AND dateFrom > CURDATE()" )[0]['num']
        );
        echo json_encode( $news );
    break;
}

?>