<?php
namespace music\playlist {

    function find($fields='*',$ids=null,$name=null,$song=null) {
        global $mysql;
        $q = array_fill(0,2,'');
    
        if ($ids)       { $q[0] = " AND id IN (".implode(',',$ids).")"; }
        if ($name)      { $q[1] = " AND name LIKE '$name'"; }
        if ($song)      { $q[2] = " AND find_in_set($song,'songs')"; }
    
        $sql = "SELECT $fields FROM musicPlaylists WHERE 1".implode('',$q);
        return $mysql->consulta($sql);
    }

    /**
     * Guarda el canal en la base de datos
     * * @return bool true si la consulta fue exitosa
     */
    function save (&$post) {
        global $mysql;

        if ( $post['id'] != '' ) { // Editar
            $sql = "UPDATE musicPlaylists SET " .
            "name='" . $post['name'] . "', " .
            "notes='" . $post['notes'] . "', " .
            "songs='" . $post['songs'] . "' " .
            "WHERE id=" . $post['id'];
        } else {                    // Añadir
            $sql = "INSERT INTO musicPlaylists(name, notes, songs) VALUES(" .
            "'" . $post['name'] . "'," .
            "'" . $post['notes'] . "',".
            "'" . $post['songs'] . "')";
        }

        $mysql->consulta($sql);
        
        // Borrar la cache
        \cache\clearAll(\cache\type::deploy); //TODO: Borrar solo cache de equipos afectados
        return true;
    }

    
    function m3u8Upload( $channel, $m3u8 ) {
        global $mysql;
        $songs = array(); $ids = array();
        
        $dbData = $mysql->consulta('SELECT id, srcFile FROM music WHERE srcFile IS NOT NULL');
        foreach ($dbData as $cancion) {
            $songs[$cancion['id']] = $cancion['srcFile'];
        }
		
		preg_match_all( '/#EXTINF:.*\n.*[\\\\\/](?<titles>.*)\.mp3/', $m3u8, $preg);
        foreach ($preg['titles'] as $name) {
            $song = array_search( $name, $songs );

            if ($song) { $ids[]= $song; }
        }
	
        $ids = implode(',', $ids);
        $mysql->consulta("UPDATE canales SET songs='" . $ids . "' WHERE id = " . $channel);
    }

    /**
     * Borra el canal de la base de datos
     * * @param int $id
     * * @return bool true si la consulta fue exitosa
     */
    function delete ( $id ) {
        global $mysql;
        $mysql->consulta('DELETE FROM musicPlaylists WHERE id=' . $id);
        \cache\clearAll(\cache\type::deploy);

        return true; //TODO: Devolver segun el estado
    }

    /**
     * Devuelve la información de todos los canales
     * * @param array $IDs el id de los canales que se necesita consultar
     * * @return array la información de los canales
     */
    function listado($IDs = false) {
        global $mysql;
        $sql = "SELECT * FROM musicPlaylists";
        if ($IDs) { $sql .= ' WHERE id IN (' . implode(',', $IDs) . ')'; }
        $res = $mysql->consulta( $sql );

        $canales = array();
        foreach ($res as $row) {
            $row['songs'] = array_map('intval', explode(',', $row['songs']));
            $row['nshops'] =  $mysql->consulta( 'SELECT count(*) AS n FROM shops WHERE canal='.$row['id'])[0]['n'];
            
            $canales[$row['id']]= $row;
        }

        return $canales;
    }

    function songList($channel) {
        global $mysql;
        $sql =  "SET @songs = (SELECT songs FROM musicPlaylists WHERE id=$channel);
                SELECT id, name, file FROM music WHERE FIND_IN_SET(id, @songs) ORDER BY FIND_IN_SET(id, @songs);";
 
        $mysql->consulta($sql, false);
        $songs = $mysql->nextRowset();

        foreach ($songs as $i => $v) { $songs[$i]['id'] = (int)$songs[$i]['id'];}

        return $songs;
    }
}

namespace music\song {
    const E_INVALID_EXTENSION = 'Archivo no admitido. Solo se permite: opus, mp3';

    /**
     * Edita la cancion enviada
     * * @return bool true si la consulta fue exitosa
     */
    function edit (&$post) {
        global $mysql;
        if ( $files['file']['name']!= '' ) {
            $ext = pathinfo($files["file"]["name"], PATHINFO_EXTENSION);
            if ( !in_array($ext, ['mp3','opus']) ) { throw new \Exception( E_INVALID_EXTENSION ); };

            $oldFilename = $mysql->consulta( 'SELECT file FROM music WHERE id = ' . $post['id'] )[0]['file'];
            $filename  = time() . "." . $ext;
            move_uploaded_file($files["file"]["tmp_name"], ROOT_DIR.'/storage/music/' . $filename);
            unlink(ROOT_DIR.'/storage/music/' . $oldFilename);
            $sqlFile = ", `file` = '" . $filename . "', ";
        } else { $sqlFile = ''; }

        $sql = "UPDATE music SET " .
        "name='" . $post['titulo'] .
        $sqlFile .
        "WHERE id=" . $post['id'];

        return $mysql->consulta($sql);
    }


    /**
     * Añade una canción a la base de datos
     * * @return bool true si la consulta fue exitosa
     */
    function add (&$post, &$files) {
        global $mysql;

        $ext = pathinfo($files["file"]["name"], PATHINFO_EXTENSION);
        if ( !in_array($ext, ['mp3', 'opus']) ) { throw new \Exception( E_INVALID_EXTENSION ); }

        $filename = uniqid() . '.' . $ext;
        move_uploaded_file($files['file']['tmp_name'], ROOT_DIR . '/storage/music/' . $filename);
        $name = $mysql->quote( pathinfo($files['file']['name'], PATHINFO_FILENAME) );

        $sql = "INSERT INTO music (name, file, srcFile) VALUES(".$name.", '".$filename."',".$name.")";
        $mysql->consulta($sql);
        
        if (isset($post['addToChannel'])) {
            $sql = "UPDATE musicPlaylists SET songs=TRIM(BOTH ',' FROM CONCAT(songs, ',".$mysql->lastInsertId()."')) WHERE id=".$post['addToChannel'].";";
        }

        $mysql->consulta($sql);
        return true;
    }

    /**
     * Borra las cancions
     * * @param array $ids
     * * @return bool true si la consulta fue exitosa
     */
    function delete ( $ids ) {
        global $mysql;

        foreach ($ids as $id) {
            $sql = 'SELECT file FROM music WHERE id='.$id.'; DELETE FROM music WHERE id='.$id;
            $oldFile = $mysql->consulta( $sql )[0]['file']; 
            unlink(ROOT_DIR . '/storage/music/' . $oldFile);
        }

        return true;
    }

    /**
     * Devuelve la información de todas las canciones
     * * @param array $IDs el id de las canciones que se necesita consultar
     * * @return array la información de las canciones
     */
    function listado($IDs = false, $addChannels = false) {
        global $mysql;
        $sql = "SELECT *";
        if ($addChannels)    { $sql .= ", (SELECT GROUP_CONCAT(id) AS canales FROM musicPlaylists WHERE FIND_IN_SET(music.id, songs)) AS canales"; }
        $sql .= ' FROM music';
        if ($IDs)            { $sql .= ' WHERE id IN (' . implode(',', $IDs) . ')'; }
        $res = $mysql->consulta( $sql );

        $songs = array();
        foreach ($res as $row) {
            if ($addChannels) {
                $row['canales'] = $row['canales'] == null? array() : array_map('intval', explode(',', $row['canales']) );
            }

            $songs[$row['id']]= $row;
        }

        return $songs;
    }
}


?>