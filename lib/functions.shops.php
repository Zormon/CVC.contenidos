<?php
namespace shops;

/* -------------------------------------------------------------------------- */
/*                                  ACCIONES                                  */
/* -------------------------------------------------------------------------- */

/**
 * Edita la tienda en la base de datos
 * * @return bool true si la consulta fue exitosa
 */
function edit (&$post) {
    global $mysql;

    $sql = "UPDATE `shops` SET " .
    "name='" . $post['name'] . "', " .
    "telefono='" . $post['telefono'] . "', " .
    "email='" . $post['email'] . "', " .
    "direccion='" . $post['direccion'] . "', " .
    "notas='" . $post['notas'] . "', " .
    "canal='" . $post['canal'] . "', " .
    "version=NOW()" .
    "WHERE id=" . $post['id'];


    $mysql->consulta($sql, false);

    // Borrar la cache de hilo musical de equipos
    $devices = $mysql->consulta('SELECT id FROM devices WHERE shop=' . $post['id']);
    \cache\clear( array_column($devices, 'id'), \cache\type::deploy );

    return true;
}

 /**
 * Añade la shop a la base de datos
 * * @return bool true si la consulta fue exitosa
 */
function add (&$post) {
    global $mysql;

    $sql = "INSERT INTO `shops` (name, telefono, email, direccion, canal, notas) VALUES('" .
            $post['name'] . "','" .
            $post['telefono'] . "','" .
            $post['email'] . "','" .
            $post['direccion'] . "','" .
            $post['canal'] . "','" .
            $post['notas'] . "')";
    $mysql->consulta($sql, false);

    return true;
}

/**
 * Borra la tienda de la base de datos
 * * @param int $id
 * * @return bool true si la consulta fue exitosa
 */
function delete ( $id ) { //TODO: Borrar el resto de informacion de la tienda en contenidos, usuarios, listas, etc
    global $mysql;

    // Borrar devices asociados
    $devices = $mysql->consulta('SELECT id FROM devices WHERE shop=' . $id);
    foreach( $devices as $equipo) {
        \devices\delete($equipo['id']);
    }
    \cache\clear( $devices, \cache\type::all );

    $mysql->consulta('DELETE FROM `shops` WHERE id=' . $id, false);

    return true; //TODO: Devolver segun el estado
}

/* -------------------------------------------------------------------------- */
/*                                  CONSULTA                                  */
/* -------------------------------------------------------------------------- */

function find($fields='*',$ids=null,$name=null,$telefono=null,$email=null,$direccion=null,$canal=null) {
    global $mysql;
    $q = array_fill(0,4,'');

    if ($ids)       { $q[0] = " AND id IN (".implode(',',$ids).")"; }
    if ($name)      { $q[1] = " AND name LIKE '$name'"; }
    if ($telefono)  { $q[2] = " AND telefono LIKE '$telefono'"; }
    if ($direccion) { $q[3] = " AND direccion LIKE '$direccion'"; }
    if ($canal)     { $q[4] = " AND canal = $canal"; }

    $sql = "SELECT $fields FROM shops WHERE 1".implode('',$q);
    return $mysql->consulta($sql);
}


/**
 * Devuelve la información de todas las tiendas
 * * @param array $IDs el id de las tiendas que se necesita consultar
 * * @return array la información de las tiendas
 */
function listado($IDs = false) {
    global $mysql;
    $sql = "SELECT *, UNIX_TIMESTAMP(version) AS version FROM shops";
    if ($IDs) { $sql .= ' WHERE id IN (' . implode(',', $IDs) . ')'; }
    $dbData = $mysql->consulta( $sql );

    $shops = array();
    foreach ($dbData as $dbRow) { // Añadir todos los devices a cada shop
        $dbRow['devices'] = $mysql->consulta('SELECT id,name,tipo FROM devices WHERE shop=' . $dbRow['id']);
        $dbRow['ndevices'] = count($dbRow['devices']);

        $shops[$dbRow['id']]= $dbRow;
    }

    return $shops;
}

?>