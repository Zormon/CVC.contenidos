<?php

namespace home {
    function overview() {
        global $mysql;
        global $_PREFS;

        $dbData = $mysql->consulta(
            'SELECT tipo, COUNT(*) AS num FROM devices GROUP BY tipo;' . 
            'SELECT media_cats.name AS cat, COUNT(*) AS num FROM media INNER JOIN media_cats ON media.categoria = media_cats.id  GROUP BY categoria;' .
            'SELECT media.id, media.name, media.version AS version, categoria, color, ' . 
                "DATE_FORMAT(dateFrom, '%d-%m-%Y') AS dateFrom,  DATE_FORMAT(dateTo, '%d-%m-%Y') AS dateTo, " . 
                'duration, volume, estado FROM media WHERE estado = 1 ORDER BY media.version DESC LIMIT 5'
        );

        // ########  CATEGORIAS  ########
        $homeData['cats'] = $_PREFS['media']['types'];

        // ########  EQUIPOS  ########
        $homeData['devices'] = array();
        foreach($dbData as $e) { $homeData['devices'][$e['tipo']] = $e['num']; }
        
        // ########  CONTENIDOS  ########
        $dbData = $mysql->nextRowset();
        $homeData['media'] = array();
        foreach($dbData as $e) { $homeData['media'][$e['cat']] = $e['num']; }

        // ########  ULTIMOS CONTENIDOS  ########
        $dbData = $mysql->nextRowset();
        foreach($dbData as &$e) { $e['elapsed'] = time_elapsed($e['version']); }
        $homeData['lastMedia'] = $dbData;

        return $homeData;
    }
}




?>