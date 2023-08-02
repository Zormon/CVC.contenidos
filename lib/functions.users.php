<?php
namespace users;

/* -------------------------------------------------------------------------- */
/*                                  ACCIONES                                  */
/* -------------------------------------------------------------------------- */

function calculatePermissions(&$perms) {
    $calc = 0;
    if ( isset($perms['isAdmin']) )            { $calc += AUTH_ADMIN; }

    if ( isset($perms['seeSupport']) )         { $calc += AUTH_SEE_SUPPORT; }

    if ( isset($perms['seeUsers']) )           { $calc += AUTH_SEE_USERS; }
    if ( isset($perms['seeBaul']) )            { $calc += AUTH_SEE_BAUL; }
    if ( isset($perms['seeLists']) )           { $calc += AUTH_SEE_LISTS; }
    if ( isset($perms['seeIssues']) )          { $calc += AUTH_SEE_ISSUES; }
    if ( isset($perms['seeGroups']) )          { $calc += AUTH_SEE_GROUPS; }
    if ( isset($perms['seeMusic']) )           { $calc += AUTH_SEE_MUSIC; }
    if ( isset($perms['seeDevices']) )         { $calc += AUTH_SEE_DEVICES; }
    if ( isset($perms['seeEvents']) )          { $calc += AUTH_SEE_EVENTS; }

    if ( isset($perms['editMedia']) )          { $calc += AUTH_EDIT_MEDIA; }
    if ( isset($perms['editShops']) )        { $calc += AUTH_EDIT_SHOPS; }
    if ( isset($perms['editUsers']) )          { $calc += AUTH_EDIT_USERS; }
    if ( isset($perms['editLists']) )          { $calc += AUTH_EDIT_LISTS; }
    if ( isset($perms['editMusic']) )          { $calc += AUTH_EDIT_MUSIC; }
    if ( isset($perms['editGroups']) )         { $calc += AUTH_EDIT_GROUPS; }
    if ( isset($perms['editDevices']) )        { $calc += AUTH_EDIT_DEVICES; }
    if ( isset($perms['editEvents']) )         { $calc += AUTH_EDIT_EVENTS; }

    return $calc;
}

function fillPermissions(&$row) {
    $perms = array();
    $perms['isAdmin']       = ($row & AUTH_ADMIN);
  
    $perms['seeSupport']    = ($row & AUTH_SEE_SUPPORT);

    $perms['seeUsers']      = ($row & AUTH_SEE_USERS);
    $perms['seeBaul']       = ($row & AUTH_SEE_BAUL);
    $perms['seeLists']      = ($row & AUTH_SEE_LISTS);
    $perms['seeGroups']     = ($row & AUTH_SEE_GROUPS);
    $perms['seeMusic']      = ($row & AUTH_SEE_MUSIC);
    $perms['seeDevices']    = ($row & AUTH_SEE_DEVICES);
    $perms['seeEvents']     = ($row & AUTH_SEE_EVENTS);

    $perms['editMedia']     = ($row & AUTH_EDIT_MEDIA);
    $perms['editShops']     = ($row & AUTH_EDIT_SHOPS);
    $perms['editUsers']     = ($row & AUTH_EDIT_USERS);
    $perms['editLists']     = ($row & AUTH_EDIT_LISTS);
    $perms['editMusic']     = ($row & AUTH_EDIT_MUSIC);
    $perms['editGroups']    = ($row & AUTH_EDIT_GROUPS);
    $perms['editDevices']   = ($row & AUTH_EDIT_DEVICES);
    $perms['editEvents']    = ($row & AUTH_EDIT_EVENTS);

    return $perms;
}

/**
 * Edita el usuario enviado
 * * @return bool true si la consulta fue exitosa
 */
function edit (&$post) {
    global $mysql;

    if (!isset($post['shops'])) { $post['shops'] = []; }
    $sql = "UPDATE users SET " .
    "name='" . $post['name'] . "', " .
    "login='" . $post['login'] . "', " .
    "email='" . $post['email'] . "', " .
    "permissions='" . calculatePermissions($post) . "', " .
    "shops='" . implode(',', $post['shops']) . "' " .
    "WHERE id=" . $post['id'];

    $mysql->consulta($sql, false);

    return true;
}

 /**
 * Añade un usuario a la base de datos
 * * @return pass devuelve el password temporal generado
 */
function add (&$post) {
    $pass = bin2hex(openssl_random_pseudo_bytes(4));

    if (!isset($post['shops'])) { $post['shops'] = []; }
    $sql = "INSERT INTO users (name, login, password, email, permissions, shops) VALUES('" .
        $post['name'] . "','" .
        $post['login'] . "','" .
        sha1($pass) . "','" .
        $post['email'] . "','" .
        calculatePermissions($post) . "','" .
        implode(',', $post['shops']) . "')";

    global $mysql;
    $mysql->consulta($sql, false);

    //TODO: Comprobar mejor todo
    return $pass;
}

/**
 * Borra el usuario
 * * @param int $id
 * * @return bool true si la consulta fue exitosa
 */
function delete ( $id ) {
    global $mysql;

    $sql = 'DELETE FROM users WHERE id=' . $id;
    return $mysql->consulta($sql, false);
    //TODO: Borrar el resto de informacion del usuario
}

/* -------------------------------------------------------------------------- */
/*                                  CONSULTA                                  */
/* -------------------------------------------------------------------------- */

/**
 * Devuelve la información de todos los usuarios
 * * @param array $IDs el id de los usuarios que se necesita consultar
 * * @return array la información de los usuarios
 */
function listado($IDs = false) {
    global $mysql;
    $sql = "SELECT *, DATE_FORMAT(lastLogin, '%d-%m-%y %H:%i') AS lastLogin FROM users";
    if ($IDs) { $sql .= ' WHERE id IN (' . implode(',', $IDs) . ')'; }
    $dbData = $mysql->consulta( $sql );
    foreach($dbData as $dbRow) {
        $dbRow['can'] = fillPermissions($dbRow['permissions']);

        $dbRow['shops'] = array();
        if ( ! empty($dbRow['shops']) ) { $dbRow['shops'] = $mysql->consulta( 'SELECT id,name FROM shops WHERE id IN (' . $dbRow['shops'] . ')' ); }
        $dbRow['ndevices'] = count($dbRow['shops']);

        $users[$dbRow['id']]= $dbRow;
    }


    return $users;
}

?>