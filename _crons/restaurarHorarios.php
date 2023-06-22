<?php
    require __DIR__.'/../boot.php';
    global $mysql;


	$sql = file_get_contents(__DIR__.'/horariosSpar221213.sql');
    $mysql->consulta($sql,false);

    cache\clearAll(\cache\type::deploy);
?>