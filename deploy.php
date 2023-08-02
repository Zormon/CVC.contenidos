<?php
global $mysql;
define('NO_LOGIN', true);
define('NOCACHE', isset($_GET['nocache']));
define('NOREFRESH', isset($_GET['norefresh']));

header('Content-type:application/json;charset=utf-8');
if ( !isset($_['id']) || $_['id'] == '' ) { die(' {"status":"ko", "error":"ID no definido"} '); }

$output = \cache\get(\cache\type::deploy, $_['id']);

if ($output && !NOCACHE) { 
    echo $output;
} else { // No hay cache, regenera JSON
    $device = \devices\listado([$_['id']]);
    if ( count($device) == 0 ) { die('{"status":"ko", "error":"No existe el equipo"}'); }
    $device = $device[array_key_first($device)];
    
    global $mysql;
    $canal = \shops\find(fields:'canal', ids:[$device['shop']])[0]['canal'];

    $parrilla = \media\parrilla($device['id'], true);
    $musica = \music\playlist\songList($canal);
    
    $json['info'] = \deploy\info($device);
    $json['media'] = \deploy\media($parrilla);
    $json['music'] = \deploy\music($canal);
    $json['events'] = \deploy\events($device['id']);
    
    $json['catalog']['media'] = \deploy\mediaCatalog($parrilla, array_column($json['events'], 'media'));
    $json['catalog']['music'] = \deploy\musicCatalog($musica);

    $json['power'] = \deploy\power($device['power']);

    if (!NOCACHE) { $json['cached'] = date(DATE_RFC2822); }

    $output = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    echo $output;

    // Genera cache
    if (!NOCACHE) { file_put_contents(ROOT_DIR.'/cache/deploy/deploy_'.$_['id'].'.json', $output); }
}

// Registro de actividad de los equipos
if ( !NOREFRESH ) {
    $mysql->consulta("UPDATE devices SET lastConnect = NOW(), lastIp = '" . $_SERVER['REMOTE_ADDR'] . "' WHERE id = " . $_['id'], false);
}



?>