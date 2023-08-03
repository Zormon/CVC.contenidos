<?php

class login {
    public $id = '';
    public $fullname = '';
    public $name = '';
    public $email = '';
    public $isAdmin = false;
    public $permissions = array();
    public $shops = array();
    public $devices = array();
    public $can = array();

    function fill_user_data($id) {
        global $mysql;
        $row = $mysql->consulta('SELECT * FROM users WHERE id=' . $id)[0];
        $this->id = $row['id'];
        $this->fullname = $row['name'];
        $this->name = $row['login'];
        $this->email = $row['email'];
        $this->isAdmin = ($row['permissions'] & AUTH_ADMIN);

        $this->can['seeSupport']    = $this->isAdmin || ($row['permissions'] & AUTH_SEE_SUPPORT);

        $this->can['seeUsers']      = $this->isAdmin || ($row['permissions'] & AUTH_SEE_USERS);
        $this->can['seeBaul']       = $this->isAdmin || ($row['permissions'] & AUTH_SEE_BAUL);
        $this->can['seeLists']      = $this->isAdmin || ($row['permissions'] & AUTH_SEE_LISTS);
        $this->can['seeGroups']     = $this->isAdmin || ($row['permissions'] & AUTH_SEE_GROUPS);
        $this->can['seeMusic']      = $this->isAdmin || ($row['permissions'] & AUTH_SEE_MUSIC);
        $this->can['seeDevices']    = $this->isAdmin || ($row['permissions'] & AUTH_SEE_DEVICES);
        $this->can['seeEvents']     = $this->isAdmin || ($row['permissions'] & AUTH_SEE_EVENTS);

        $this->can['editMedia']     = $this->isAdmin || ($row['permissions'] & AUTH_EDIT_MEDIA);
        $this->can['editShops']     = $this->isAdmin || ($row['permissions'] & AUTH_EDIT_SHOPS);
        $this->can['editUsers']     = $this->isAdmin || ($row['permissions'] & AUTH_EDIT_USERS);
        $this->can['editLists']     = $this->isAdmin || ($row['permissions'] & AUTH_EDIT_LISTS);
        $this->can['editMusic']     = $this->isAdmin || ($row['permissions'] & AUTH_EDIT_MUSIC);
        $this->can['editGroups']    = $this->isAdmin || ($row['permissions'] & AUTH_EDIT_GROUPS);
        $this->can['editDevices']   = $this->isAdmin || ($row['permissions'] & AUTH_EDIT_DEVICES);
        $this->can['editEvents']    = $this->isAdmin || ($row['permissions'] & AUTH_EDIT_EVENTS);

        $sql = "SELECT id, name FROM shops";
        if (!$this->isAdmin) { $sql .=  " WHERE id IN (" . $row['shops'] . ")"; }
        $res = $mysql->consulta($sql);
        foreach ($res as $row2) {
            $this->shops[] = $row2;
        }

        $sql = "SELECT id, name, tipo FROM devices";
        if (!$this->isAdmin) { $sql .=  " WHERE shop IN (" . $row['shops'] . ")"; }
        $res = $mysql->consulta($sql);
        foreach ($res as $row2) {
            $this->devices[] = $row2;
        }
    }

    function loginPass($name, $pass) {
        global $mysql;
        $result = $mysql->consulta('SELECT id FROM users WHERE login="' . $name . '" AND password="' . sha1($pass) . '"');
        
        if ( count($result)>0 ) {
            $this->fill_user_data( $result[0]['id'] );
            $mysql->consulta("UPDATE users SET lastLogin=NOW(), lastIp = '" . $_SERVER['REMOTE_ADDR'] . "' WHERE id = " . $this->id, false);

            return true;
        } else { return false; } 
    }
    
    function loginToken($name, $token) {
        global $mysql;
        $result = $mysql->consulta('SELECT id FROM users WHERE login="' . $name . '" AND loginToken="' . $token . '"');
        
        if ( count($result)>0 ) {
            $this->fill_user_data( $result[0]['id'] );
            return true;
        } else { return false; } 
    }

    function addShop($shop) {
        global $mysql;
        $mysql->consulta("UPDATE users SET shops = CONCAT(shops, '," . $shop . "') WHERE id=" . $this->id, false);
    }

    function newToken() {
        global $mysql;
        $token = bin2hex(random_bytes(4));
        $mysql->consulta("UPDATE users SET loginToken = '" . $token . "' WHERE id=" . $this->id, false);

        return $token;
    }
    
    static function existsLogin($login) {
        global $mysql;
        $mysql->consulta("SELECT id FROM users WHERE login=" . $login);
    }

}

?>