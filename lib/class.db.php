<?php

class DB extends PDO {
    private $prep;

    function __construct( $driver, $server, $name, $user, $pass ) {
        try {
            parent::__construct( $driver.':host='.$server.';dbname='.$name.';charset=utf8', $user, $pass );
        } catch (Exception $e) { die("¡Error al conectar a DB!: " . $e->getMessage() . "<br/>"); }
    }

    function consulta( $sql, $fetch=true ) {
        try {
            $this->prep = $this->prepare($sql);
            $this->prep->execute();
            if ($fetch) { return $this->prep->fetchAll(PDO::FETCH_ASSOC); }
        } catch (PDOException $e) { die("¡Error en consulta!: " . $e->getMessage() . "<br/> Consulta: " . $sql); }
    }

    function nextRowset() {
        $this->prep->nextRowset();
        return $this->prep->fetchAll(PDO::FETCH_ASSOC);
    }

    function getEnumValues( $table, $col ) {
        $res = $this->consulta("SHOW COLUMNS FROM `" . $table . "` LIKE '" . $col . "'")[0];
        return explode("','", preg_replace("/(enum|set)\('(.+?)'\)/","\\2", $res['Type']) );
    }
}

?>