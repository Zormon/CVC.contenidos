<?php

if ( !isset($_POST['mode'])) { die; }

session_start();
global $login;
global $_PREFS;

switch ( $_POST['mode'] ) {
    case 'login':
        if ( isset($_POST['user']) && $_POST['user']!='' && isset($_POST['pass']) ) {
            if ( $login->loginPass($_POST['user'], $_POST['pass']) ) {
                $_SESSION['user_id'] = $login->id;
                
                if (isset($_POST['remember'])) {
                    setcookie('user', $_POST['user'], time()+2592000, '/', $_PREFS['app']['domain']); // 2592000 = 30 dias
                    setcookie('token', $login->newToken(), time()+2592000, '/', $_PREFS['app']['domain']);
                }

                echo json_encode(["status" => 'ok']);
            } else {
                echo json_encode(["status" => 'ko', "error" => 'invalid' ]);
            }
        }
    break;

    case 'logout':
        session_destroy();
        setcookie('user', '', 0, '/');
        setcookie('token', '', 0, '/');
        unset($_COOKIE["user"]);
        unset($_COOKIE["token"]);
    break;
}

?>