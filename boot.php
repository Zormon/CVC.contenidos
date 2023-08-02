<?php
error_reporting(E_ALL); ini_set('display_errors', '1');

const ROOT_DIR = __DIR__;
// Convierte a variables post el contenido json recibido para gestión más fácil
$_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
$_CONFIG = parse_ini_file(ROOT_DIR.'/config.ini', true);
$_PREFS = yaml_parse_file(ROOT_DIR.'/prefs.yml');

require(ROOT_DIR.'/lib/constants.php');
require(ROOT_DIR.'/lib/functions.php');
require(ROOT_DIR.'/lib/class.db.php');
require(ROOT_DIR.'/lib/class.login.php');


$mysql = new DB(
    $_CONFIG['driver'], $_CONFIG['server'], 
    $_CONFIG['base'], $_CONFIG['user'],  $_CONFIG['pass'] 
);


$login = new login();

/* ===================================================
======================= Routes =======================
=================================================== */
$loginInit = 'loginInit.php';

require(ROOT_DIR.'/lib/router.php');
route('/', [$loginInit, 'secciones/home.php']);

route('/deploy/$id', ['deploy.php']);

route('/login', ['secciones/login.php']);

// API
route('/api/login', ['api/login.php']);
route('/api/shops', [$loginInit, 'api/shops.php']);
route('/api/devices', [$loginInit, 'api/devices.php']);
route('/api/groups', [$loginInit, 'api/groups.php']);
route('/api/users', [$loginInit, 'api/users.php']);
route('/api/media', [$loginInit, 'api/media.php']);
route('/api/music', [$loginInit, 'api/music.php']);
route('/api/events', [$loginInit, 'api/events.php']);

// Secciones
route('/home', [$loginInit, 'secciones/home.php']);
route('/tiendas', [$loginInit, 'secciones/shops.php']);
route('/equipos', [$loginInit, 'secciones/devices.php']);
route('/grupos', [$loginInit, 'secciones/groups.php']);
route('/usuarios', [$loginInit, 'secciones/users.php']);

route('/parrilla', [$loginInit, 'secciones/parrilla.php']);
route('/listas', [$loginInit, 'secciones/mediaPlaylists.php']);
route('/eventos', [$loginInit, 'secciones/events.php']);

route('/contenidos/$mStat', [$loginInit, 'secciones/media.php']);

route('/soporte', [$loginInit, 'secciones/support.php']);

route('/canales', [$loginInit, 'secciones/musicPlaylists.php']);
route('/canciones', [$loginInit, 'secciones/music.php']);

route('/ayuda', [$loginInit, 'secciones/help.php']);
route('/perfil', [$loginInit, 'secciones/profile.php']);

route('/config', [$loginInit, 'secciones/config.php']);
route('/registros', [$loginInit, 'secciones/logs.php']);



?>