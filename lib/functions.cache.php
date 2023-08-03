<?php
namespace cache {
    abstract class type {
        const all = -1;
        const deploy = 1;
    }

    function clear( $ids, $type=type::all ) {
        switch ($type) {
            case type::all:
            case type::deploy:
                foreach($ids as $id) { @unlink(ROOT_DIR."/cache/deploy/deploy_$id.json"); }
            break;
        }
    }

    function clearAll( $type=type::all ) {
        switch ($type) {
            case type::all:
            case type::deploy:
                array_map( 'unlink', glob(ROOT_DIR."/cache/deploy/deploy_*.json") );
            break;
        }
    }

    function exists($type, $id) {
        switch ($type) {
            case type::deploy: 
                return file_exists(ROOT_DIR."/cache/deploy/deploy_$id.json");
            break;
        }
    }

    function get($type, $id) {
        switch ($type) {
            case type::deploy: 
                if ( exists(type::deploy, $id) ) { return file_get_contents(ROOT_DIR."/cache/deploy/deploy_$id.json"); }
                else                             { return false; }
            break;
        }
    }
};
?>