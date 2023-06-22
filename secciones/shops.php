<?php
global $login;
include(ROOT_DIR.'/_header.php');
include(ROOT_DIR.'/_menu.php');

include(ROOT_DIR.'/tpl/shops.php'); 
?>

<header class="actionBar">
    <div class="input search">
        <input id="search" type="search" placeholder=" ">
        <label for="search">Buscar tienda</label>
    </div>
    <div class="input">
        <select id="order">
            <option value="name" selected>Nombre</option>
            <option value="ndevices">Número de equipos</option>
            <?php if ($login->can['editDevices']) { ?>
                <option value="id">Identificador</option>
            <?php } ?>
        </select>
        <label for="order">Ordenar por</label>
    </div>
    <div class="tools">
        <button class="flat tooltip" id="refresh" data-tt_pos="down" data-tt_text="Recargar listado"><i class="icon-recargar"></i></button>
        <?php  if ( $login->can['editShops'] ) { ?>
            <button class="flat tooltip" id="add" data-tt_pos="down" data-tt_text="Nueva tienda"><i class="icon-add"></i></button>
        <?php } ?>
    </div>
</header>

<main class="seccion" id="main_shops">
    <ol class="rowList" id="ul_shops"></ol>
</main>

<script type="module">
    import SHOPS from '/js/shops.js?2'
    import {may, selectModal, $} from '/js/exports.js?2'

    var jsonShops = <?=json_encode(shops\listado( array_column($login->shops, 'id') ))?>;
    var shops = new SHOPS(jsonShops, $('search'), $('order'))
    
    $('search').onkeyup = (e)=> {
        sessionStorage.shopSearch = e.currentTarget.value
        shops.printList()
    }
    $('order').onchange = (e)=> {
        localStorage.shopOrder = e.currentTarget.value
        shops.printList()
    }
    $('refresh').onclick = ()=> { shops.refresh() }
    may ( ()=>{ $('add').onclick = ()=> { shops.modal() } } )

    if ( !!sessionStorage.shopSearch ) { $('search').value = sessionStorage.shopSearch }
    if ( !!localStorage.shopOrder ) { $('order').value = localStorage.shopOrder }
    new selectModal( 'order', 'Ordenar por', 'list' )

    shops.printList()
</script>


<?php include(ROOT_DIR.'/_footer.php'); ?>