<?php
die;
error_reporting(E_ALL); ini_set('display_errors', '1');
if (!isset($_GET['seccion'])) { $_GET['seccion'] = ''; } // Por alguna razon, con navegadores webkit esta variable no se define a veces
$URL = explode('/', strtoupper($_GET['seccion']));
define('SECTION', $URL[0]);
define('SUBSECTION', isset($URL[1])? $URL[1] : '' );

require 'boot.php';
// Si no está logeado el usuario, incluye el login y ya está
if ( !isset( $_SESSION['user_id']) )  { include('login.php'); die; }


switch (SECTION) {
    case 'HOME':            $mainFile = 'home'; break;
    case 'TIENDAS':         $mainFile = 'shops'; break;
    case 'EQUIPOS':         $mainFile = 'devices'; break;
    case 'GRUPOS':          $mainFile = 'groups'; break;
    case 'USUARIOS':        $mainFile = 'users'; break;

    case 'PARRILLA':        $mainFile = 'parrilla'; break;
    case 'LISTAS':          $mainFile = 'mediaPlaylists'; break;
    case 'EVENTOS':         $mainFile = 'events'; break;

    case 'CONTENIDOS':      $mainFile = 'media'; break;

    case 'SOPORTE':         $mainFile = 'support'; break;
    case 'CANALES':         $mainFile = 'musicPlaylists'; break;
    case 'CANCIONES':       $mainFile = 'music'; break;

    case 'AYUDA':           $mainFile = 'help'; break;
    case 'PERFIL':          $mainFile = 'profile'; break;

    case 'CONFIG':          $mainFile = 'config'; break;
    case 'REGISTROS':       $mainFile = 'logs'; break;

    default:            header('Location: /home'); die; break;
}


include('_header.php');
include('_menu.php');
include(__DIR__.'/secciones/'.$mainFile.'.php');
include('_footer.php');


?>