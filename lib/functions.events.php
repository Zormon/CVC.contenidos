<?php
namespace events;

/* -------------------------------------------------------------------------- */
/*                                  ACCIONES                                  */
/* -------------------------------------------------------------------------- */

/**
 * Edita el evento en la base de datos
 * * @return bool true si la consulta fue exitosa
 */
function edit (&$post) {
    global $mysql;
    if (!isset($post['devices'])) { $post['devices']=[]; }

    $sql = 'UPDATE events SET'.
    " `name` = '" . $post['name'] . "'" .
    ", `dateFrom` = '" . $post['dateFrom'] . "'" .
    ", `dateTo` = '" . $post['dateTo'] . "'" .
    ", `time` = '" . $post['time'] . "'" .
    ", `weekdays` = " . $post['weekdays'] .
    ", devices = '" . implode(',', $post['devices']) . "'" .
    ", `media` = '" . $post['media'] . "'" .
    " WHERE id = " . $post['id'];

    // Cache
    $oldDevices = explode(',', find(fields:'devices',id:$post['id'])[0]['devices'] );
    $affectedDevices = array_unique(array_merge($oldDevices, $post['devices']));
    \cache\clear($affectedDevices,\cache\type::deploy);

    return $mysql->consulta($sql, false);
}

 /**
 * Añade el evento a la base de datos
 * * @return bool true si la consulta fue exitosa
 */
function add (&$post) {
    global $mysql;
    if (!isset($post['devices'])) { $post['devices']=[]; }

    $sql = "INSERT INTO events(name,dateFrom,dateTo,time,weekdays,devices,media) VALUES(".
    "'" . $post['name'] . "'" .
    ",'" . $post['dateFrom'] . "'" .
    ",'" . $post['dateTo'] . "'" .
    ",'" . $post['time'] . "'" .
    "," . $post['weekdays'] .
    ",'" . implode(',', $post['devices']) . "'" .
    ",'" . $post['media'] . "')";

    \cache\clear($post['devices'],\cache\type::deploy);

    return $mysql->consulta($sql, false);
}

/**
 * Borra el evento de la base de datos
 * * @param int $id
 * * @return bool true si la consulta fue exitosa
 */
function delete ( $id ) {
    global $mysql;
    $sql = 'DELETE FROM events WHERE id=' . $id;

    // Cache
    $devices = explode(',', find(fields:'devices',id:$id)[0]['devices'] );
    \cache\clear($devices,\cache\type::deploy);

    return $mysql->consulta($sql, false);
    //TODO: Borrar el resto de informacion de el equipo en contenidos, usuarios, listas, etc
}

/* -------------------------------------------------------------------------- */
/*                                  CONSULTA                                  */
/* -------------------------------------------------------------------------- */


/**
 * Busca elementos en la base de datos
 *
 * @param $fields Campos a recuperar de la base de datos
 * @param $id
 * @param $name Nombre en expresion SQL 'LIKE'
 * @param $media Id del contenido
 * @param $dateFrom Expresión de comparación de fecha desde, e.j. `> '2022-09-01`
 * @param $dateTo Expresión de comparación de fecha hasta, e.j. `<= '2023-09-01`
 * @param $time ID del dispositivos a buscar
 * @param $device ID del dispositivos a buscar
 */
function find(
    string $fields = '*',
    int|null $id = null,
    string|null $name = null,
    int|null $media = null,
    string|null $dateFrom = null,
    string|null $dateTo = null,
    string|null $time = null,
    int|null $device = null
):array {
    global $mysql;
    $q = array_fill(0,6,'');

    if ($id)        { $q[0] = " AND id = $id"; }
    if ($name)      { $q[1] = " AND name LIKE '$name'"; }
    if ($media)     { $q[2] = " AND media = $media"; }
    if ($dateFrom)  { $q[3] = " AND dateFrom $dateFrom"; }
    if ($dateTo)    { $q[4] = " AND dateTo $dateTo"; }
    if ($time)      { $q[5] = " AND time $time"; }
    if ($device)    { $q[6] = " AND find_in_set($device, devices)"; }

    $sql = "SELECT $fields FROM events WHERE 1".implode('',$q);
    return $mysql->consulta($sql);
}

/**
 * Devuelve información de eventos
 * * @param array $IDs el id de los eventos que se necesita consultar
 * * @param array $devices los dispositivos que entran en la busqueda
 * * @param int $style tipo de consulta
 * * @return array la información de los eventos
 */
function query ($IDs = false, $devices=[], $style = QUERY_STD) {
    global $mysql;
    global $login;

    $sql = 'SELECT *, TIME_FORMAT(time, "%H:%i") AS time FROM events WHERE 1';
    if ($IDs) { $sql .= ' AND id IN (' . implode(',', $IDs) . ')'; }

    $dbData = $mysql->consulta( $sql );

    $events = array();
    foreach ($dbData as $dbRow) {
        $dbRow['id'] = (int)$dbRow['id'];
        $dbRow['weekdays'] = (int)$dbRow['weekdays'];
        $dbRow['devices'] = array_map( 'intval', explode(',', $dbRow['devices']) );
        if ($style == QUERY_STD) { $dbRow['ndevices'] = count($dbRow['devices']); }
        if ($style != QUERY_STD) { unset($dbRow['version']); }
        
        if ( !empty($devices)  && ( empty( array_intersect($dbRow['devices'], $devices) ) ) ) { 
            unset( $dbRow ); // Descartar los que no estan en la lista de devices
        } else {
            $media = $mysql->consulta("SELECT id, file, duration, volume FROM media WHERE id=".$dbRow['media']);
            if (!empty($media)) { 
                $media = $media[0];
                $media['duration'] = (int)$media['duration']; $media['volume'] = (int)$media['volume'];
                $dbRow['media'] = $media;
                }
            $events[$dbRow['id']]= $dbRow;
        }
    }


    return $events;
}

?>