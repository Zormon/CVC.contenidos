<?php

namespace media {
    const E_INVALID_EXTENSION = 'Archivo no admitido. Solo se permite: mp4, mkv, png, jpg';
    const DISABLED  = 0;
    const ENABLED   = 1;
    const HIDDEN    = 2;

    enum status {
        case PENDIENTES;
        case ACTUALES;
        case FUTUROS;
        case CADUCADOS;
        case ACTIVOS;
        case OCULTOS;
        case TODOS;
    }

    function create_thumb($id, $filename, $seek = 0) {
        global $_CONFIG;
        $seek = round($seek); $seekString = sprintf('%02d:%02d:%02d', ($seek/ 3600),($seek/ 60 % 60), $seek% 60);
        $ext = pathinfo($filename, PATHINFO_EXTENSION );
        if ($ext == 'mp4' || $ext == 'mkv') { // Video
            $tmp_file = ROOT_DIR.'/tmp/'.uniqid().'.png';
            $cmd = '"'.$_CONFIG['ffmpeg'].'ffmpeg" -ss '.$seekString.' -i "'.ROOT_DIR."/storage/media/$filename".'"'. ' -vf thumbnail=50 -frames:v 1 '.$tmp_file;

            exec($cmd);
            $origImg = imagecreatefrompng($tmp_file);
            unlink($tmp_file);

        } else if ($ext == 'jpg') {
            $origImg = imagecreatefromjpeg(ROOT_DIR."/storage/media/$filename");
        } else if ($ext == 'png') {
            $origImg = imagecreatefrompng(ROOT_DIR."/storage/media/$filename");
        }
        imagewebp( image_resize($origImg, 100, 100, RESIZE_MODE_CROP_CENTER), ROOT_DIR.'/img/media/'.$id.'.webp', 60);
    }

    function media_info($filename) {
        global $_CONFIG;
        
        $ext = pathinfo($filename, PATHINFO_EXTENSION );
        if ($ext == 'mp4' || $ext == 'mkv') {
            $res = array();
            $cmd = '"'.$_CONFIG['ffmpeg'].'ffprobe" -v error'.
                ' -show_entries format=duration,bit_rate,size'.
                ' -show_entries stream=height,width,codec_name,profile,avg_frame_rate,display_aspect_ratio'.
                ' -of json "'.ROOT_DIR."/storage/media/$filename".'"';

            exec($cmd, $res);
            $json = json_decode( implode($res) );

            $info['type'] = 'video';
            $info['codec'] = $json->streams[0]->codec_name;
            $info['profile'] = $json->streams[0]->profile;
            $info['width'] = $json->streams[0]->width;
            $info['height'] = $json->streams[0]->height;
            $info['framerate'] = $json->streams[0]->avg_frame_rate;
            $info['ratio'] = $json->streams[0]->display_aspect_ratio;
            $info['duration'] = $json->format->duration;
            $info['bitrate'] = $json->format->bit_rate;
            $info['filesize'] = $json->format->size;
            
        } else {
            switch ($ext) {
                case 'jpg':
                    $pic = imagecreatefromjpeg(ROOT_DIR."/storage/media/$filename");
                    break;
                case 'png':
                    $pic = imagecreatefrompng(ROOT_DIR."/storage/media/$filename");
                    break;
                case 'webp':
                    $pic = imagecreatefromwebp(ROOT_DIR."/storage/media/$filename");
                    break;
            }
            $info['type'] = 'image';
            $info['width'] = imagesx($pic);
            $info['height'] = imagesy($pic);
        }
        return $info;
    }

    /**
     * Busca elementos en la base de datos
     *
     * @param String $fields Campos a recuperar de la base de datos
     * @param Array $ids Array con los IDs
     * @param String $name Nombre en expresion SQL 'LIKE'
     * @param String $dateFrom Expresión de comparación de fecha desde, e.j. `> '2022-09-01`
     * @param String $dateTo Expresión de comparación de fecha hasta, e.j. `<= '2023-09-01`
     * @param Number $device ID del dispositivos a buscar
     * @return void
     */
    function find($fields='*',$ids=null,$name=null,$dateFrom=null,$dateTo=null,$device=null) {
        global $mysql;
        $q = array_fill(0,4,'');

        if ($ids)       { $q[0] = " AND id IN (".implode(',',$ids).")"; }
        if ($name)      { $q[1] = " AND name LIKE '$name'"; }
        if ($dateFrom)  { $q[2] = " AND dateFrom $dateFrom"; }
        if ($dateTo)    { $q[3] = " AND dateTo $dateTo"; }
        if ($device)    { $q[4] = " AND find_in_set($device, devices)"; }

        $sql = "SELECT $fields FROM media WHERE 1".implode('',$q);
        return $mysql->consulta($sql);
    }
    
    function edit ( &$post, &$files) {
        global $mysql;
        if ( !isset($post['devices']) ) { $post['devices'] = []; }
        
        if ( $files['file']['name']!= '' ) {
            $ext = pathinfo($files["file"]["name"], PATHINFO_EXTENSION);
            if ( !in_array($ext, ['mp4','mkv','jpg','png']) ) { throw new \Exception( E_INVALID_EXTENSION ); }

            $filename  = time() . "." . $ext;
            move_uploaded_file($files["file"]["tmp_name"], ROOT_DIR.'/storage/media/' . $filename);
            $mediaInfo = media_info($filename);

            $sqlFile = "`file` = '" . $filename . "', `mediainfo`= '" . json_encode($mediaInfo) ."', ";
            
            $oldFilename = $mysql->consulta( 'SELECT file FROM media WHERE id = ' . $post['id'] )[0]['file'];
            unlink(ROOT_DIR . '/storage/media/' . $oldFilename);
            create_thumb($post['id'], $filename, $mediaInfo['duration']/3);
            
        } else { $sqlFile = ''; }
    
    
        $sql = "UPDATE media SET " . $sqlFile .
            "`name` = '" . $post['name'] . "'" .
            ", `dateFrom` = '" . date("Y-m-d",strtotime($post['fDateFrom'])) . "'" .
            ", `dateTo` = '" . date("Y-m-d",strtotime($post['fDateTo'])) . "'" .
            ", devices = '" . implode(',', $post['devices']) . "'" .
            ", `volume` = " . $post['volume'] .
            ", `duration` = " . $post['duration'] .
            ", `categoria` = " . $post['categoria'] .
            ", `transition` = '" . $post['transition'] . "'" .
            ", `tags` = '" . $post['tags'] . "'" .
            ", `version` = NOW()" .
            " WHERE id = " . $post['id'];
    
        $mysql->consulta($sql, false);
    
        return true;
    }
    
    function add ( &$post, &$files) {
        global $mysql;
        global $_PREFS;
        if ( !isset($post['devices']) ) { $post['devices'] = []; }

        $ext = pathinfo($files["file"]["name"], PATHINFO_EXTENSION);
        if ( !in_array($ext, ['mp4','mkv','jpg','png']) ) { throw new \Exception( E_INVALID_EXTENSION ); }

        $filename  = time() . "." . $ext;
        move_uploaded_file($files["file"]["tmp_name"], ROOT_DIR."/storage/media/$filename");

        $mediaInfo = media_info($filename);
    
        $sql = "INSERT INTO media(file, name, dateFrom, dateTo, devices, volume, duration, status, tags, categoria, mediainfo) VALUES('" .
        $filename . "','" .
        $post['name'] . "','" .
        date("Y-m-d",strtotime($post['fDateFrom'])) . "','" .
        date("Y-m-d",strtotime($post['fDateTo'])) . "','" .
        implode(',', $post['devices']) . "','" .
        $post['volume'] . "','" .
        $post['duration'] . "','" .
        ($_PREFS['media']['pendientes']?'0':'1') . "','" .
        $post['tags'] . "','" .
        $post['categoria'] . "','" .
        json_encode($mediaInfo) . "')";
    
        $mysql->consulta($sql, false);
        $id = $mysql->lastInsertId();
    
		$seek = $mediaInfo['type']=='video'? $mediaInfo['duration']/3 : 0;
        create_thumb($id, $filename, $seek);
    
        // Añadir a lista
        if ( $post['addToPlaylist'] != -1 ) { \media\playlist\addMedia($id, $post['addToPlaylist']); }
    
        return true;
    }
    
    function delete ( $ids ) {
        global $mysql;
    
        foreach ($ids as $id) {
            $oldFile = $mysql->consulta( 'SELECT file FROM media WHERE id = '.$id)[0]['file'];
            unlink(ROOT_DIR.'/storage/media/'.$oldFile);
            unlink(ROOT_DIR.'/img/media/'.$id.'.webp');
    
            $mysql->consulta( 'DELETE FROM `media` WHERE id='.$id, false);
        }
    }
    
    function status ( $id, $state = ENABLED ) {
        global $mysql;
        $sql = "UPDATE media SET status='" . $state . "' WHERE id=" . $id;
    
        return $mysql->consulta($sql, false);
    }
    
    function details ($id) {
        global $mysql;
        global $_PREFS;
    
        $row = $mysql->consulta("SELECT id,name,file,duration,volume,devices,tags,version,categoria,status,mediainfo, " .
                                "DATE_FORMAT(dateFrom, '%d-%m-%Y') AS dateFromF, DATE_FORMAT(dateTo, '%d-%m-%Y') AS dateToF " .
                                "FROM media WHERE id=".$id)[0];
    
        $cont['id'] = $row['id'];
        $cont['name'] = $row['name'];
        $cont['file'] = $row['file'];
        $cont['duration'] = $row['duration'];
        $cont['volume'] = $row['volume'];
        $cont['tags'] = $row['tags'];
        $cont['version'] = $row['version'];
        $cont['dateFromF'] = $row['dateFromF'];
        $cont['dateToF'] = $row['dateToF'];
        
        // name categoria
        if ( array_key_exists($row['categoria'], $_PREFS['media']['types']) ) {
            $cont['categoria'] = $_PREFS['media']['types'][$row['categoria']]['name'];
        } else {
            $cont['categoria'] = 'Desconocida ('.$row['categoria'].')';
        }
    
    
        switch ($row['status']) {
            case '0':
                $cont['status'] = 'Pendiente';
            break;
            case '1':
                // name estado
                if (strtotime($row['dateToF']) < time()) {
                    $cont['status'] = 'Caducado';
                } else if ( strtotime($row['dateFromF']) > time() ) {
                    $cont['status'] = 'Programado';
                } else {
                    $cont['status'] = 'Activo';
                }
            break;
            case '2':
                $cont['status'] = 'Oculto';
            break;
        }
    
        // Media info
        $cont['mediainfo'] = json_decode($row['mediainfo']);
        $cont['mediainfo']->framerate = explode('/',$cont['mediainfo']->framerate)[0];
        $cont['mediainfo']->duration = round((float)$cont['mediainfo']->duration, 2);
        $cont['mediainfo']->ratio = getRatio($cont['mediainfo']->width, $cont['mediainfo']->height);
        
    
        $numRatio = $cont['mediainfo']->width / $cont['mediainfo']->height;
        if ($numRatio > 1) {
            $cont['mediainfo']->orientation = 'Horizontal' ;
        } else if ( $numRatio < 1 ) {
            $cont['mediainfo']->orientation = 'Vertical' ;
        } else {
            $cont['mediainfo']->orientation = 'Cuadrado' ;
        }
        
    
        if ( $cont['mediainfo']->filesize > 1048576) { // MB
            $cont['mediainfo']->filesize = round($cont['mediainfo']->filesize/1048576, 2) . ' MB';
        } else {
            $cont['mediainfo']->filesize = round($cont['mediainfo']->filesize/1024, 2) . ' kB';
        }
    
    
        return $cont;
    }
    
    function thumb ($id) {
        if (file_exists(__DIR__ . '/../img/media/' . $id . '.webp')) {
            return '/img/media/' . $id . '.webp';
        } else {
            return '/img/media/_void.webp';
        }
    }

    /**
     * Devuelve un array con todos los contenidos existentes
     */
    function listado ($estado=TODOS, $fields=['*']) {
        global $mysql;
        global $_PREFS;
    
        // Colores de categorias
        foreach($_PREFS['media']['types'] as $id => $cat) { $colores[$id] = $cat['color']; }
    
        $media = array();
        $sql = 'SELECT '.implode(',',$fields).',id,UNIX_TIMESTAMP(version) AS version' .
        ',(SELECT GROUP_CONCAT(mediaPlaylists.id) FROM mediaPlaylists WHERE FIND_IN_SET(media.id, mediaPlaylists.media) ) AS playlists' .
        ",(SELECT GROUP_CONCAT(events.id) FROM events WHERE FIND_IN_SET(media.id, events.media) ) AS events";
        if ($fields == ['*'] || array_key_exists('dateFrom', $fields))     { $sql.= ",DATE_FORMAT(dateFrom, '%d-%m-%Y') AS dateFromF"; }
        if ($fields == ['*'] || array_key_exists('dateTo', $fields))     { $sql.= ",DATE_FORMAT(dateTo, '%d-%m-%Y') AS dateToF"; }
        $sql.= ' FROM media ';


        switch ($estado) {
            case ACTIVOS:
                $sql .= 'WHERE status=' . ENABLED . ' AND dateTo >= CURRENT_DATE()';
            break;
            case PENDIENTES:
                $sql .= 'WHERE status=' . DISABLED . ' AND dateTo >= CURRENT_DATE()';
            break;
            case ACTUALES:
                $sql .= 'WHERE status=' . ENABLED . ' AND dateFrom <= CURRENT_DATE() AND dateTo >= CURRENT_DATE()';
            break;
            case FUTUROS:
                $sql .= 'WHERE status=' . ENABLED . ' AND dateFrom > CURRENT_DATE()';
            break;
            case CADUCADOS:
                $sql .= 'WHERE dateTo < CURRENT_DATE()';
            break;
            case TODOS:
            break;
        }
        $res = $mysql->consulta( $sql );
    

        $devicesField = $fields==['*'] || array_key_exists('devices', $fields);
        $categoriaField = $fields==['*'] || array_key_exists('categoria', $fields);
        // Llenar datos en el array
        foreach($res as $row) {
            if ($devicesField) { $row['devices'] = array_map( 'intval', explode(',',$row['devices']) ); }
            if ($categoriaField) {
                $row['color'] = array_key_exists($row['categoria'], $colores) ? $colores[$row['categoria']] : '#ffffff';
                $row['nameCategoria'] = array_key_exists($row['categoria'], $_PREFS['media']['types']) ? $_PREFS['media']['types'][$row['categoria']]['name'] : '-';
            }
    
            $row['thumb'] = thumb($row['id']).'?'.$row['version'];
            $row['tags'] = array_filter(explode(',', $row['tags']));
            
            $media[$row['id']]= $row;
        }
    
        return $media;
    }
    
    /**
     * Devuelve un array con la parrilla de contenidos de una tienda
     * @param id id de la tienda
     */
    function parrilla($id = -1, $simpleOutput=false) {
        global $mysql; 
        global $_PREFS;
        
        foreach($_PREFS['media']['types'] as $i => $cat) { $categorias[$i] = $cat; }
    
        $sql = "SELECT name, media FROM mediaPlaylists";
        if ($id != -1) { $sql.=  " WHERE find_in_set(" . $id . ", devices)"; }
        $playlists = $mysql->consulta($sql);
        $conAudio=0; $duration = 0; $parrilla = array(); $media = array();


        foreach ($playlists as $pl) {
            if (empty($pl['media'])) { continue; }
            $moreFields = !$simpleOutput? ',status,categoria':'';

            $sql = 'DROP TEMPORARY TABLE IF EXISTS idList;';
            $sql .= 'CREATE TEMPORARY TABLE idList ( id INTEGER, playlist VARCHAR(100) );';
            $sql .= "INSERT INTO idList(id, playlist) VALUES (" . implode(",'".$pl['name']."'),(", explode(',', $pl['media']) ) . ",'" . $pl['name'] . "');";
            $mysql->exec($sql);
            // Contenidos en emision
            $sql = "SELECT media.id,idList.playlist,name,file,dateFrom,dateTo,duration,volume" . ($simpleOutput? '':',status,categoria') . ",transition";
            if (!$simpleOutput) {
                $sql .= ",DATE_FORMAT(dateFrom, '%d-%m-%Y') AS dateFromF, DATE_FORMAT(dateTo, '%d-%m-%Y') AS dateToF";
            }
            $sql .= " FROM idList INNER JOIN media ON idList.id = media.id WHERE status=1 AND dateFrom <= CURRENT_DATE() AND dateTo >= CURRENT_DATE()";
            if ($id != -1) { $sql.= " AND find_in_set( '" . $id . "', devices )"; }
            
            $dataDB = $mysql->query($sql);
            $conts = $dataDB->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($conts as $i => $v) {
                $conts[$i]['id'] = (int)$conts[$i]['id'];
                $conts[$i]['duration'] = (int)$conts[$i]['duration'];
                $conts[$i]['volume'] = (int)$conts[$i]['volume']; }
    
            $parrilla = array_merge($parrilla, $conts);
        }
    
        if (!$simpleOutput) {
            foreach ($parrilla as $row) {
                $row['categoria'] = array_key_exists($row['categoria'], $categorias) ? $categorias[$row['categoria']] : '';
                $row['thumb'] = thumb($row['id']);
                $media[]= $row;
                
                $duration += $row['duration'];
                if ($row['volume']>0) { $conAudio++; }
            }
        
            $parrilla = array();
            $parrilla['media'] = $media;
            $parrilla['conAudio'] = $conAudio;
            $parrilla['duration'] = $duration;
        }
    
        return $parrilla;
    }
}

namespace media\playlist {

    /**
     * Busca elementos en la base de datos
     *
     * @param String $fields Campos a recuperar de la base de datos
     * @param Number $id
     * @param String $name Nombre en expresion SQL 'LIKE'
     * @param Number $media ID de contenido a buscar
     * @param Number $device ID de dispositivo a buscar
     * @return void
     */
    function find($fields='*',$id=null,$name=null,$media=null,$device=null) {
        global $mysql;
        $q = array_fill(0,3,'');

        if ($id)        { $q[0] = " AND id = $id"; }
        if ($name)      { $q[1] = " AND name LIKE '$name'"; }
        if ($media)     { $q[2] = " AND find_in_set($media, media)"; }
        if ($device)    { $q[3] = " AND find_in_set($device, devices)"; }

        $sql = "SELECT $fields FROM mediaPlaylists WHERE 1".implode('',$q);
        return $mysql->consulta($sql);
    }

    function save($data) {
        global $mysql;

        if ($data['id'] == '') {
            $sql = "INSERT INTO mediaPlaylists(name, devices, media) VALUES(" .
            "'" . $data['name'] . "', " . 
            "'" .implode(',', $data['devices']) . "', " .  
            "'" . $data['media'] . "')";
        } else {
            $sql = "UPDATE mediaPlaylists SET " .
            "name='" . $data['name'] . "', " . 
            "devices='" .implode(',', $data['devices']) . "', " .  
            "media='" . $data['media'] . "' " . 
            "WHERE id=" . $data['id'] . ';';
        }

        $mysql->consulta($sql);
    
        // Borrar la cache
        \cache\clearAll(\cache\type::deploy); //TODO: Borrar solo cache de devices afectados
        return true;
    }
    
    function addMedia($media, $playlist) {
        global $mysql;
        $sql = "UPDATE mediaPlaylists SET media = TRIM(BOTH ',' FROM CONCAT(media,',$media') ) WHERE id = " . $playlist;
        $mysql->consulta($sql);
    
        return true;
    }
    
    function delete ( $id ) {
        global $mysql;
        $mysql->consulta('DELETE FROM mediaPlaylists WHERE id=' . $id);
        \cache\clearAll(\cache\type::deploy); //TODO: Borrar solo cache de devices afectados

        return true;
        //TODO: Borrar el resto de informacion
        
    }
    /**
     * Devuelve un array con todas las playlists existentes
     */
    function listado(&$fields=['*']) {
        global $mysql;
        $res = $mysql->consulta( "SELECT ".implode(',', $fields).",id FROM mediaPlaylists" );
        $playlists = [];
    
        $mediaField = $fields==['*'] || array_key_exists('media', $fields);
        $devicesField = $fields==['*'] || array_key_exists('devices', $fields);

        foreach ($res as $row) {
            if ($mediaField) { $row['media'] = array_map('intval', explode(',', $row['media'])); }
            if ($devicesField) { $row['devices'] = array_map('intval', explode(',', $row['devices'])); }
    
            $playlists[$row['id']]= $row;
        }
    
        return $playlists;
    }
}

?>