<?php

// Permisos de usuario
const AUTH_ADMIN        = 1<<0;

const AUTH_SEE_SUPPORT  = 1<<1;

const AUTH_SEE_USERS    = 1<<2;
const AUTH_SEE_BAUL     = 1<<3;
const AUTH_SEE_LISTS    = 1<<4;
const AUTH_SEE_GROUPS   = 1<<5;
const AUTH_SEE_MUSIC    = 1<<6;
const AUTH_SEE_DEVICES  = 1<<7;
const AUTH_SEE_EVENTS   = 1<<8;

const AUTH_EDIT_MEDIA   = 1<<9;
const AUTH_EDIT_SHOPS = 1<<10;
const AUTH_EDIT_USERS   = 1<<11;
const AUTH_EDIT_LISTS   = 1<<12;
const AUTH_EDIT_MUSIC   = 1<<13;
const AUTH_EDIT_GROUPS  = 1<<14;
const AUTH_EDIT_DEVICES = 1<<15;
const AUTH_EDIT_EVENTS  = 1<<16;


// Preferencias de usuario


// Estados de contenidos
const PENDIENTES    = 1;
const ACTUALES      = 2;
const FUTUROS       = 3;
const CADUCADOS     = 4;
const ACTIVOS       = 5;
const OCULTOS       = 6;
const TODOS         = -1;


// Estilos de consultas
const QUERY_STD         = 0;
const QUERY_DEPLOY     = 1;


// Variables hardcoded
const MAINLOGO_MAXWIDTH = 800;
const CARDGRID_IMG_MAXWIDTH = 500;
const ROW_IMG_MAXWIDTH = 256;


?>