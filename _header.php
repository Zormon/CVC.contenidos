<?php 
global $_PREFS;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$_PREFS['app']['name']?></title>
    <link rel="icon" type="image/png" href="/img/favicon.png">
    <script src="/js/mustache.min.js"></script>
    <style><?=print_css_vars()?></style>
    <link type="text/css" rel="stylesheet" href="/css/main.css?4"  media="screen,projection"/>
</head>
<body class="panel">
<script>
    const MEDIASTATUS = { PENDIENTES: 1, ACTUALES: 2, FUTUROS: 3, CADUCADOS: 4, ACTIVOS: 5 }
    const LOGIN = {
        id:'<?=$login->id?>',
        name:'<?=$login->name?>',
        email:'<?=$login->email?>',
        isAdmin: '<?=$login->isAdmin?>',
        can: {
            see: {
                support:    <?=var_export($login->can['seeSupport'])?>,
                users:      <?=var_export($login->can['seeUsers'])?>,
                baul:       <?=var_export($login->can['seeBaul'])?>,
                lists:      <?=var_export($login->can['seeLists'])?>,
                groups:     <?=var_export($login->can['seeGroups'])?>,
                music:      <?=var_export($login->can['seeMusic'])?>,
                devices:    <?=var_export($login->can['seeDevices'])?>,
                events:     <?=var_export($login->can['seeEvents'])?>
            }, 
            edit: {
                media:      <?=var_export($login->can['editMedia'])?>,
                shops:      <?=var_export($login->can['editShops'])?>,
                users:      <?=var_export($login->can['editUsers'])?>,
                lists:      <?=var_export($login->can['editLists'])?>,
                music:      <?=var_export($login->can['editMusic'])?>,
                groups:     <?=var_export($login->can['editGroups'])?>,
                devices:    <?=var_export($login->can['editDevices'])?>,
                events:     <?=var_export($login->can['editEvents'])?>
            }
        }
    }

    const GLOBAL = {
        groups: <?=json_encode(groups\listado(true))?>,
        config: {
            media: <?=json_encode($_PREFS['media'])?>,
            path: <?=json_encode($_PREFS['path'])?>
        }
    }
</script>