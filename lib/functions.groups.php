<?php
namespace groups;

/* -------------------------------------------------------------------------- */
/*                                  ACCIONES                                  */
/* -------------------------------------------------------------------------- */

/**
 * Edita el grupo enviado
 * * @return bool true si la consulta fue exitosa
 */
function edit (&$post, &$img) {
    global $mysql;

    if ( $img && $img['name']!= '' ) {
        $origImg = makeImgFromFile($img);
        if ($origImg) { imagewebp( image_max_size($origImg, CARDGRID_IMG_MAXWIDTH), __DIR__.'/../img/groups/'.$post['id'].'.webp', 60); }
    }

    $sql = "UPDATE groups SET " .
    "name='" . $post['name'] . "', " .
    "devices='" . implode(',', $post['devices']) . "', " .
    "notes='" . $post['notes'] . "' " .
    "WHERE id=" . $post['id'];

    return $mysql->consulta($sql);
}


/**
 * Añade un grupo a la base de datos
 * * @return bool true si la consulta fue exitosa
 */
function add (&$post, &$img) {
    $sql = "INSERT INTO groups (name, notes, devices) VALUES('" .
            $post['name'] . "','" .
            $post['notes'] . "','" .
            implode(',', $post['devices']) . "')";

    global $mysql;
    $mysql->consulta($sql);

    if ( $img && $img['name']!= '' ) {
        $origImg = makeImgFromFile($img);
        if ($origImg) { imagewebp( image_max_size($origImg, ROW_IMG_MAXWIDTH), __DIR__.'/../img/groups/'.$mysql->lastInsertId().'.webp', 60); }
    }

    return true;
}

/**
 * Borra el grupo
 * * @param int $id
 * * @return bool true si la consulta fue exitosa
 */
function delete ( $id ) {
    $sql = 'DELETE FROM groups WHERE id=' . $id;
    global $mysql;
    return $mysql->consulta($sql);
    //TODO: Borrar el resto de informacion del grupo
}


/* -------------------------------------------------------------------------- */
/*                                  CONSULTA                                  */
/* -------------------------------------------------------------------------- */

/**
 * Devuelve la información de todos los grupos
 * * @param bool $simple devuelve los datos en forma minima, para listar los grupos en desplegables
 * * @param array $IDs el id de los grupos que se necesita consultar
 * * @return array la información de los grupos
 */
function listado ($simple=false, $IDs = false) {
    global $mysql;
    if ($simple)    { $sql = "SELECT id,name,devices FROM groups";  }
    else            { $sql = "SELECT * FROM groups"; }
    if ($IDs) { $sql .= ' WHERE id IN (' . implode(',', $IDs) . ')'; }
    $dbData = $mysql->consulta( $sql );
    if (!$simple) {
        for ($i=0;$i<count($dbData);$i++) { // Añadir todos los equipos a cada grupo
            $dbData[$i]['devices'] = $mysql->consulta( 'SELECT id,name,tipo FROM devices WHERE id IN (' . $dbData[$i]['devices'] . ')' );
            $dbData[$i]['ndevices'] = count($dbData[$i]['devices']);
        }
    }

    $groups = array();
    foreach ($dbData as $dbRow) {
        $groups[$dbRow['id']]= $dbRow;
    }

    return $groups;
}

?>