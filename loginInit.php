<?php

global $login;

session_start();
// Login process
$login = new login();
if ( isset($_SESSION['user_id']) && $_SESSION['user_id'] != '' ) {
    $login->fill_user_data($_SESSION['user_id']);
} else if (isset($_COOKIE["user"]) && isset($_COOKIE["token"])) {
    if ( $login->loginToken($_COOKIE["user"], $_COOKIE["token"]) ) {
        $_SESSION['user_id'] = $login->id;
    }
}

if ( !isset( $_SESSION['user_id']) )  { header('Location:/login'); die; }

?>