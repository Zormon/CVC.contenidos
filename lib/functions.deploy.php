<?php
namespace deploy {
    function info($device) {
        $return['id'] = (int)$device['id'];
        
        global $_PREFS;
        $return['storage']['media'] = $_PREFS['path']['media'];
        $return['storage']['music'] = $_PREFS['path']['music'];

        $return['device']['name'] = $device['name'];
        $return['device']['type'] = $device['nameTipo'];
        $return['device']['shop'] = (int)$device['shop'];

        $return['version'] = (new \Datetime())->getTimestamp();

        return $return;
    }

    function media($parrilla) {
        $return = array_column($parrilla, 'id');

        return $return;
    }

    function music($canal) {
        $songs = \music\playlist\find(fields:'songs',ids:[$canal])[0]['songs'];
        $return = explode(',', $songs);

        return $return;
    }

    function events($id) {
        $return = \events\find(device:$id);

        return $return;
    }

    function power($power) {
        $return['mode'] = $power->mode;
        // Dias excluidos
        $return['exclude'] = array_map('intval', explode(',', $power->ex) );
    
        // Horas encendido
        $return['on'] = array(
            !!$power->L->on? $power->L->on : null,
            !!$power->M->on? $power->M->on : null,
            !!$power->X->on? $power->X->on : null,
            !!$power->J->on? $power->J->on : null,
            !!$power->V->on? $power->V->on : null,
            !!$power->S->on? $power->S->on : null,
            !!$power->D->on? $power->D->on : null
        );
    
        $return['off'] = array(
            !!$power->L->off? $power->L->off : null,
            !!$power->M->off? $power->M->off : null,
            !!$power->X->off? $power->X->off : null,
            !!$power->J->off? $power->J->off : null,
            !!$power->V->off? $power->V->off : null,
            !!$power->S->off? $power->S->off : null,
            !!$power->D->off? $power->D->off : null
        );

        return $return;
    }

    function mediaCatalog($parrilla, $events) {
        $evMedia = \media\find(fields:'id,name,file,dateFrom,dateTo,duration,volume,transition',ids:$events);
        $parrilla = array_merge($parrilla, $evMedia);
        
        $catalog = [];
        foreach ($parrilla as $m) {
            $i = $m['id'];
            unset($m['playlist']);
            $catalog[$i] = $m;
            unset($catalog[$i]['id']);
        }

        return $catalog;
    }

    function musicCatalog($musica) {
        $catalog = [];
        foreach ($musica as $m) {
            $i = $m['id'];
            $catalog[$i] = $m;
            unset($catalog[$i]['id']);
        }

        return $catalog;
    }

}
?>