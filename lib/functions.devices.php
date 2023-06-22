<?php
namespace devices;

function fill_power_array(&$post) {
    switch ($post['hFormato']) {
        case 'LV': // Lunes a viernes
            $power = array( 
                "L" =>  array( "on" => $post['LVon'], "off" => $post['LVoff'] ),
                "M" =>  array( "on" => $post['LVon'], "off" => $post['LVoff'] ),
                "X" =>  array( "on" => $post['LVon'], "off" => $post['LVoff'] ),
                "J" =>  array( "on" => $post['LVon'], "off" => $post['LVoff'] ),
                "V" =>  array( "on" => $post['LVon'], "off" => $post['LVoff'] ),
                "S" =>  array( "on" => '', "off" => '' ),
                "D" =>  array( "on" => '', "off" => '' )
            );
        break;
        case 'LV+S': // Lunes a viernes, sabados
            $power = array( 
                "L" =>  array( "on" => $post['LVon'], "off" => $post['LVoff'] ),
                "M" =>  array( "on" => $post['LVon'], "off" => $post['LVoff'] ),
                "X" =>  array( "on" => $post['LVon'], "off" => $post['LVoff'] ),
                "J" =>  array( "on" => $post['LVon'], "off" => $post['LVoff'] ),
                "V" =>  array( "on" => $post['LVon'], "off" => $post['LVoff'] ),
                "S" =>  array( "on" => $post['Son'],  "off" => $post['Soff'] ),
                "D" =>  array( "on" => '', "off" => '' )
            );
        break;
        case 'LV+S+D':
            $power = array( 
                "L" =>  array( "on" => $post['LVon'], "off" => $post['LVoff'] ),
                "M" =>  array( "on" => $post['LVon'], "off" => $post['LVoff'] ),
                "X" =>  array( "on" => $post['LVon'], "off" => $post['LVoff'] ),
                "J" =>  array( "on" => $post['LVon'], "off" => $post['LVoff'] ),
                "V" =>  array( "on" => $post['LVon'], "off" => $post['LVoff'] ),
                "S" =>  array( "on" => $post['Son'],  "off" => $post['Soff'] ),
                "D" =>  array( "on" => $post['Don'],  "off" => $post['Doff'] )
            );
        break;
        case 'LJ+VD':
            $power = array( 
                "L" =>  array( "on" => $post['LJon'], "off" => $post['LJoff'] ),
                "M" =>  array( "on" => $post['LJon'], "off" => $post['LJoff'] ),
                "X" =>  array( "on" => $post['LJon'], "off" => $post['LJoff'] ),
                "J" =>  array( "on" => $post['LJon'], "off" => $post['LJoff'] ),
                "V" =>  array( "on" => $post['VDon'], "off" => $post['VDoff'] ),
                "S" =>  array( "on" => $post['VDon'], "off" => $post['VDoff'] ),
                "D" =>  array( "on" => $post['VDon'], "off" => $post['VDoff'] )
            );
        break;
        case 'L+M+X+J+V+S+D':
            $power = array( 
                "L" =>  array( "on" => $post['Lon'], "off" => $post['Loff'] ),
                "M" =>  array( "on" => $post['Mon'], "off" => $post['Moff'] ),
                "X" =>  array( "on" => $post['Xon'], "off" => $post['Xoff'] ),
                "J" =>  array( "on" => $post['Jon'], "off" => $post['Joff'] ),
                "V" =>  array( "on" => $post['Von'], "off" => $post['Voff'] ),
                "S" =>  array( "on" => $post['Son'], "off" => $post['Soff'] ),
                "D" =>  array( "on" => $post['Don'], "off" => $post['Doff'] )
            );
        break;
    }

    $power['format'] = $post['hFormato'];
    $power['mode'] = $post['sleepType'];
    $power['ex'] = $post['exDays'];

    return $power;
}

/* -------------------------------------------------------------------------- */
/*                                  ACCIONES                                  */
/* -------------------------------------------------------------------------- */

/**
 * Edita el equipo en la base de datos
 * * @return bool true si la consulta fue exitosa
 */
function edit (&$post) {
    global $mysql;

    $power = fill_power_array($post);

    $sql = "UPDATE devices SET " .
    "name='" . $post['name'] . "', " .
    "tipo='" . $post['tipo'] . "', " .
    "shop='" . $post['shop'] . "', " .
    "notas='" . $post['notas'] . "', " .
    "power='" . json_encode($power) . "', " .
    "version=FROM_UNIXTIME('" . (new \Datetime())->getTimestamp() . "') " .
    "WHERE id=" . $post['id'];

    return $mysql->consulta($sql, false);
}

 /**
 * Añade el equipo a la base de datos
 * * @return bool true si la consulta fue exitosa
 */
function add (&$post) {
    global $mysql;

    $power = fill_power_array($post);

    $sql = "INSERT INTO devices (name, tipo, shop, notas, power) VALUES('" .
            $post['name'] . "','" .
            $post['tipo'] . "','" .
            $post['shop'] . "','" .
            $post['notas'] . "','" .
            json_encode($power) . "')";
    return $mysql->consulta($sql, false);
}

/**
 * Borra el equipo de la base de datos
 * * @param int $id
 * * @return bool true si la consulta fue exitosa
 */
function delete ( $id ) {
    global $mysql;
    $sql = 'DELETE FROM devices WHERE id=' . $id;
    return $mysql->consulta($sql, false);
    //TODO: Borrar el resto de informacion de el equipo en contenidos, usuarios, listas, etc
}

/* -------------------------------------------------------------------------- */
/*                                  CONSULTA                                  */
/* -------------------------------------------------------------------------- */

/**
 * Devuelve la información de todos los equipos
 * * @param array $IDs el id de los equipos que se necesita consultar
 * * @param array $IDs el id de las tiendas cuyos equipos que se necesita consultar
 * * @return array la información de los equipos
 */
function listado($IDs = false, $shops = false) {
    global $mysql;
    global $_PREFS;
    $sql = "SELECT devices.*, UNIX_TIMESTAMP(devices.version) AS version, CAST(devices.tipo AS UNSIGNED) AS tipo, shops.name AS shopName, DATE_FORMAT(lastConnect, '%d-%m-%y %H:%i') AS lastConnect 
            FROM devices INNER JOIN shops ON devices.shop = shops.id";
    if ($IDs) { 
        $sql .= ' AND devices.id IN (' . implode(',', $IDs) . ')';
    }
    
    if ($shops) { 
        $sql .= ' AND shops.id IN (' . implode(',', $shops) . ')';
    }
    $dbData = $mysql->consulta( $sql );

    $devices = array();
    foreach ($dbData as $dbRow) {
        $type = $dbRow['tipo'] - 1;
        if (array_key_exists($type, $_PREFS['devices']['types'])) {
            $dbRow['nameTipo'] = $_PREFS['devices']['types'][$type]['name'];
            $dbRow['color'] = $_PREFS['devices']['types'][$type]['color'];
            $dbRow['icon'] = $_PREFS['devices']['types'][$type]['icon'];
        } else {
            $dbRow['color'] = 'ffffff'; $dbRow['icon'] = 'void';
        }
        
        $dbRow['power'] = json_decode($dbRow['power'] );

        $devices[$dbRow['id']]= $dbRow;
    }
    return $devices;
}


function details ($id) {
    global $mysql;
    global $_PREFS;

    $row = $mysql->consulta("SELECT * FROM devices WHERE id=".$id)[0];
    $data['id'] = $row['id'];

    return $data;
}

?>