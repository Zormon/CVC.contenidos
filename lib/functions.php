<?php
function rgb2hsl($r,$g,$b){$r/=255;$g/=255;$b/=255;$max=max($r,$g,$b);$min=min($r,$g,$b);$h;$s;$l=($max+$min)/2;$d=$max-$min;if($d==0){$h=$s=0;}else{$s=$d/(1-abs(2*$l-1));switch($max){case $r:$h=60*fmod((($g-$b)/$d),6);if($b>$g){$h+=360;}break;case $g:$h=60*(($b-$r)/$d+2);break;case $b:$h=60*(($r-$g)/$d+4);break;}}return[round($h,0),round($s*100,0),round($l*100,0)];}
function hsl2rgb($h,$s,$l){$c=(1-abs(2*($l/100)-1))*$s/100;$x=$c*(1-abs(fmod(($h/60),2)-1));$m=($l/100)-($c/2);if($h<60){$r=$c;$g=$x;$b=0;}elseif($h<120){$r=$x;$g=$c;$b=0;}elseif($h<180){$r=0;$g=$c;$b=$x;}elseif($h<240){$r=0;$g=$x;$b=$c;}elseif($h<300){$r=$x;$g=0;$b=$c;}else{$r=$c;$g=0;$b=$x;}return[floor(($r+$m)*255),floor(($g+$m)*255),floor(($b+$m)*255)];} 

function getRatio($num1, $num2){
    for($i = $num2; $i > 1; $i--) {
        if(($num1 % $i) == 0 && ($num2 % $i) == 0) {
            $num1 = $num1 / $i;
            $num2 = $num2 / $i;
        }
    }
    return "$num1:$num2";
}

/**
 * Redimensiona una imagen. Si solo se especifica una coordenada, escala proporcionalmente.
 * $mode especifica si la imagen resultante estará estirada o cortada desde el centro o el origen
 * De momento, si la imagen resultante es mayor, funcionara siempre como RESIZE_MODE_FILL
 */
const RESIZE_MODE_FILL = 0;
const RESIZE_MODE_CROP_CENTER = 1;
const RESIZE_MODE_CROP_ORIGIN = 2;
function image_resize(&$srcImg, $targetW, $targetH=false, $mode=RESIZE_MODE_FILL) {
    $x1 = $x2 = $y1 = $y2 = 0;
    $srcW = $cropW = imagesx($srcImg);
    $srcH = $cropH = imagesy($srcImg);
    $srcR = $srcW / $srcH;
    $destW = $targetW;
    $destH = $targetH;

    if (!$targetH) { // Width mode
        $destH = $targetW / $srcR;
    } else if (!$targetW) { // Height mode
        $destW = $targetH * $srcR;
    } else { // Both axis
        if ($mode != RESIZE_MODE_FILL) {
            $targetR = $targetW / $targetH;
            if ( $srcR >= 1 ) { // Source Horizontal
                $cropW = min($srcH * $targetR, $srcW);
                if ($mode == RESIZE_MODE_CROP_CENTER) { $x2 = $srcW / 2 - $cropW /2; }
            } else { // Source Vertical
                $cropH = min($srcW / $targetR, $srcH);
                if ($mode == RESIZE_MODE_CROP_CENTER) { $y2 = $srcH / 2 - $cropH /2; }
            }
        }
    }

    $destImg = imagecreatetruecolor($destW, $destH); 
    imagealphablending($destImg, false); imagesavealpha($destImg, true);
    imagecopyresampled($destImg, $srcImg, $x1, $y1, $x2, $y2, $destW, $destH, $cropW, $cropH);

    imagedestroy($srcImg);
    return $destImg;
}

/** 
 * Redimensiona una imagen proporcionalmente a un ancho máximo si lo supera
 * Si width es falso, toma en cuenta el alto como valor maximo
 * Devuelve un Image resource
 */
function image_max_size(&$origImg, $maxSize, $W=true) { 
    $origW = imagesx($origImg);
    $origH = imagesy($origImg);
    $ratio = $origW / $origH;
    
    if ($W && $origW > $maxSize) {
        $targetW = $maxSize;
        $targetH = $maxSize / $ratio;
    } else if (!$W && $origH > $maxSize) {
        $targetW = $maxSize * $ratio;
        $targetH = $maxSize;
    } else {
        $targetW = $origW;
        $targetH = $origH;
    }

    $targetImg = imagecreatetruecolor($targetW, $targetH); 
    imagealphablending($targetImg, false); imagesavealpha($targetImg, true);
    imagecopyresampled($targetImg, $origImg, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);

    imagedestroy($origImg);
    return $targetImg;
}


function makeImgFromFile (&$file) {
    switch( strtoupper( pathinfo($file['name'], PATHINFO_EXTENSION ) ) ) {
        case 'JPG':
            return imagecreatefromjpeg($file['tmp_name']);
            break;
        case 'PNG':
            return imagecreatefrompng($file['tmp_name']);
            break;
        case 'GIF':
            return imagecreatefromgif($file['tmp_name']);
            break;
        case 'WEBP':
            return imagecreatefromwebp($file['tmp_name']);
        break;
        default:
            return false;
            break;
    }
}


/** 
 * Imprime variables CSS basadas en la configuracion de la plataforma
 */
function print_css_vars() {
    global $_PREFS;
    $css = ':root { ';
        foreach ($_PREFS['color'] as $color => $base) {
                $css .= '--rgb-' . $color . ':' . implode(',', lightness($base)) . ';';
                if ( !in_array( $color, ['info','warn','error'] ) ) {
                    for ($i = 10; $i < 100; $i+=10) {
                        $css .= '--rgb-' . $color . "-light-$i:" . implode(',', lightness($base, $i)). ';';
                        $css .= '--rgb-' . $color . "-dark-$i:" . implode(',', lightness($base, $i*-1)). ';';
                    }
                }
        }
    $css .= '}';
    return $css;
}


/** 
 * Recibe un color en hexadecimal y un porcentaje y lo devuelve ajustado con ese porcentaje de iluminacion en un array rgb
 * Si no recibe porcentaje, funciona como un conversor a rgb
 */
function lightness($hex, $percent=0) {
    $percent = max(-100, min(100, $percent)); # clamp to range -100 - 100

    list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");

    if ($percent != 0) { 
        list($h, $s, $l) = rgb2hsl($r,$g,$b);
        $range = $percent>0? (100 - $l) : $l;
        $l = $l + ($range * $percent / 100);
        list($r, $g, $b) = hsl2rgb($h, $s, $l);
    }

    return [$r,$g,$b];
}

function time_elapsed($datetime, $full = false) {
    if ($datetime == NULL) { return 'Nunca'; }
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'año',
        'm' => 'mes',
        'w' => 'semana',
        'd' => 'día',
        'h' => 'hora',
        'i' => 'minuto',
        's' => 'segundo',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? 'hace ' . implode(', ', $string) : 'justo ahora';
}



require(ROOT_DIR.'/lib/functions.cache.php');
require(ROOT_DIR.'/lib/functions.devices.php');
require(ROOT_DIR.'/lib/functions.events.php');
require(ROOT_DIR.'/lib/functions.groups.php');
require(ROOT_DIR.'/lib/functions.home.php');
require(ROOT_DIR.'/lib/functions.media.php');
require(ROOT_DIR.'/lib/functions.music.php');
require(ROOT_DIR.'/lib/functions.shops.php');
require(ROOT_DIR.'/lib/functions.users.php');
require(ROOT_DIR.'/lib/functions.deploy.php');
?>