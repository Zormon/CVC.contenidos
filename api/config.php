<?php

if ( isset($_POST['mode']) ) {
    switch ($_POST['mode']) {
        case 'save':
            $config['app']['name'] = $_POST['name'];
            $config['app']['domain'] = $_POST['domain'];
            $config['app']['clientType'] = $_POST['clientType'];
            $config['app']['offline'] = isset($_POST['offline']);
            
            $config['path']['media'] = $_POST['mediaURL'];
            $config['path']['music'] = $_POST['musicURL'];

            $config['color']['main'] = $_POST['mainColor'];
            $config['color']['secondary'] = $_POST['secondaryColor'];
            $config['color']['emphasis'] = $_POST['emphasisColor'];
            $config['color']['neutral'] = $_POST['neutralColor'];

            $config['color']['info'] = $_POST['infoColor'];
            $config['color']['warn'] = $_POST['warnColor'];
            $config['color']['error'] = $_POST['errorColor'];
            
            $config['media']['pendientes'] = isset($_POST['pendientes']);
            $config['media']['defaults']['duration'] = $_POST['defaultDuration'];
            $config['media']['defaults']['volume'] = $_POST['defaultVolume'];
            $config['media']['defaults']['daysToEndDate'] = $_POST['daysToEndDate'];

            $categories = array_map( function($cat) {
                return ["name" => $cat[0], "color" => $cat[1]];
            } , json_decode($_POST['categories']));
            $config['media']['types'] = $categories;

            $deviceTypes = array_map( function($type) {
                return ["name" => $type[0], "color" => $type[1], "icon" => $type[2]];
            } , json_decode($_POST['deviceTypes']));
            $config['devices']['types'] = $deviceTypes;
            
            // Actualizar enum en DB
            global $mysql;
            $types = "'" . implode("','", array_column($deviceTypes, 'name')) . "'";
            $mysql->consulta( 'ALTER TABLE devices MODIFY COLUMN tipo enum('.$types.')' ,false);


            if ( yaml_emit_file(ROOT_DIR.'/prefs.yml', $config)) {
                echo json_encode(["status" => 'ok' ]);
            } else {
                echo json_encode(["status" => 'ko', "error" => 'No se pudo guardar la configuración' ]);
            }

            // Logo
            if ( is_uploaded_file( $_FILES['logo']['tmp_name'] ) ) {
                switch( pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION ) ) {
                    case 'jpg':
                        $origImg = imagecreatefromjpeg($_FILES['logo']['tmp_name']);
                    break;
                    case 'png':
                        $origImg = imagecreatefrompng($_FILES['logo']['tmp_name']);
                    break;
                    case 'gif':
                        $origImg = imagecreatefromgif($_FILES['logo']['tmp_name']);
                        break;
                    case 'webp':
                        $origImg = imagecreatefromwebp($_FILES['logo']['tmp_name']);
                    break;
                    default:
                        goto endlogo;
                    break;
                }

                imagewebp( image_max_size($origImg, MAINLOGO_MAXWIDTH), __DIR__ . '/../img/mainLogo.webp');
                endlogo:
            }

            // Favicon
            if ( is_uploaded_file( $_FILES['favicon']['tmp_name'] ) ) {
                switch( pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION ) ) {
                    case 'jpg':
                        $origImg = imagecreatefromjpeg($_FILES['favicon']['tmp_name']);
                    break;
                    case 'png':
                        $origImg = imagecreatefrompng($_FILES['favicon']['tmp_name']);
                    break;
                    case 'gif':
                        $origImg = imagecreatefromgif($_FILES['favicon']['tmp_name']);
                        break;
                    case 'webp':
                        $origImg = imagecreatefromwebp($_FILES['favicon']['tmp_name']);
                    break;
                    default:
                        goto endfav;
                    break;
                }

                imagepng( image_max_size($origImg, 32), __DIR__ . '/../img/favicon.png');
                endfav:
            }
        break;
    }
}

?>