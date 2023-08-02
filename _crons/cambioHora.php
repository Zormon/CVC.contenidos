<?php
if ( isset($_GET['sum']) ) {
    require __DIR__.'/../boot.php';
    global $mysql;
    $devices = $mysql->consulta('SELECT id, power FROM devices');
    
    
    foreach ($devices as $k => $dev) {
        $power = json_decode($dev['power']);
        $domingo = $power->D->on;
        $lunes = $power->L->on;

        if ($domingo == '') { // Si no enciende domingo, cambia lunes
            $parts = explode(':',$lunes);
            $parts[0] += $_GET['sum'];
            $newLunes = str_pad($parts[0],2,'0',STR_PAD_LEFT).':'.$parts[1];
            $power->L->on = $newLunes;
        } else { // Si enciende domingo, cambia domingo
            $parts = explode(':',$domingo);
            $parts[0] += $_GET['sum'];
            $newDomingo = str_pad($parts[0],2,'0',STR_PAD_LEFT).':'.$parts[1];;
            $power->D->on = $newDomingo;
        }

        $sql = "UPDATE devices SET power='".json_encode($power)."' WHERE id=".$dev['id'];
        //echo $sql . '<br><br>';
        $mysql->consulta($sql,false);
    }

    \cache\clearAll(\cache\type::deploy);
}
?>