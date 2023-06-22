<?php
global $login;
if (!$login->can['seeDevices']) { header('Location: /'); die; }

include(ROOT_DIR.'/_header.php');
include(ROOT_DIR.'/_menu.php');

include(ROOT_DIR.'/tpl/devices.php'); 
?>

<header class="actionBar">
    <div class="input search">
        <input id="search" type="search" placeholder=" ">
        <label for="search">Buscar equipo</label>
    </div>
    <div class="input">
        <select id="order">
            <option value="name" selected>Nombre</option>
            <option value="tipo">Tipo</option>
            <option value="lastConnect">Última conexión</option>
            <option value="shop">Tienda</option>
            <?php if ($login->isAdmin) { ?>
                <option value="id">Identificador</option>
            <?php } ?>
        </select>
        <label for="order">Ordenar por</label>
    </div>
    <div class="tools">
        <button class="flat tooltip" id="refresh" data-tt_pos="down" data-tt_text="Recargar listado"><i class="icon-recargar"></i></button>
        <?php  if ( $login->can['editDevices'] ) { ?>
            <button class="flat tooltip" id="add" data-tt_pos="down" data-tt_text="Nuevo equipo"><i class="icon-add"></i></button>
        <?php } ?>
    </div>
</header>

<main class="seccion" id="main_devices">
    <ol class="rowList" id="ul_devices"></ol>
</main>


<script type="module">
    import DEVICES from '/js/devices.js?5'
    import {may, debounce,  selectModal, $} from '/js/exports.js?5'

    var jsonDevices = <?=json_encode(devices\listado( false, array_column($login->shops, 'id') ), JSON_HEX_QUOT)?>;
    var devices = new DEVICES(jsonDevices, $('search'), $('order'));
    
    $('search').onkeyup = (e)=> {
        sessionStorage.devicesSearch = e.currentTarget.value
        devices.printList()
    }
    $('order').onchange = (e)=> {
        localStorage.devicesOrder = e.currentTarget.value
        devices.printList()
    }
    $('refresh').onclick = ()=> { devices.refresh() }
    may( ()=>{ $('add').onclick = ()=> { devices.modal() } } )
    
    if ( !!sessionStorage.devicesSearch ) { $('search').value = sessionStorage.devicesSearch }
    if ( !!localStorage.devicesOrder ) { $('order').value = localStorage.devicesOrder }
    new selectModal( 'order', 'Ordenar por', 'list', false, false, true )
    
    devices.printList()
</script>

<?php include(ROOT_DIR.'/_footer.php'); ?>