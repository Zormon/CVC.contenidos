<?php

/**
 * Enruta basado en reglas
 * @param string $route URL declarada Ej: '/seccion/$1'
 * @param array $paths Archivos php a incluir
 * @param array $vars Variables adicionales a definir, en $_. Ej: ["accion"=>"modificar"]
 * @return void
 */
function route(string $route, array $paths, array $vars=[]) {
  $route_parts = explode('/', $route);
  array_shift($route_parts);

  $request_url = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
  $request_url = strtok( rtrim($request_url, '/'), '?');
  $request_url_parts = explode('/', $request_url);
  array_shift($request_url_parts);

  // Variables a definir del tercer parametro
  foreach ($vars as $key => $value) { $_[$key] = $value; }  

  if ( !!!$route_parts[0] && count($request_url_parts) == 0 ) { // Es la Root Url
    // Incluye los archivos especificados y termina
    foreach ($paths as $file) { include($_SERVER['DOCUMENT_ROOT'].'/'.$file); }
    exit(); 
  } else {
    if ( count($route_parts) != count($request_url_parts) ) { return; }  // Si no coincide el numero de partes de la ruta y la peticion, salir

    // Recorrer las partes de la ruta
    foreach ($route_parts as $i => $part) {
      if ( preg_match("/^[$]/", $part) ) { // Es un parametro de variable
        $part = ltrim($part, '$');
        $_[$part] = $request_url_parts[$i];
      } 
      else if ( $part != $request_url_parts[$i] ) { return; } // No es esta ruta, salir
    }

    // Incluye los archivos especificados y termina
    foreach ($paths as $file) { include($_SERVER['DOCUMENT_ROOT'].'/'.$file); }
    exit();
  }
}