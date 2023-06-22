<?php
include(ROOT_DIR.'/_header.php');
include(ROOT_DIR.'/_menu.php');

include(ROOT_DIR.'/tpl/groups.php');
?>

<header class="actionBar">
    <div class="input search">
        <input id="search" type="search" placeholder=" ">
        <label for="search">Buscar grupo</label>
    </div>
    <div class="input">
        <select id="order">
            <option value="name" selected>Nombre</option>
            <option value="ndevices">Número de equipos</option>
            <option value="id">Creación</option>
        </select>
        <label for="order">Ordenar por</label>
    </div>
    <div class="tools">
        <button class="flat tooltip" id="refresh" data-tt_pos="down" data-tt_text="Recargar listado"><i class="icon-recargar"></i></button>
        <?php  if ( $login->can['editGroups'] ) { ?>
            <button class="flat tooltip" id="add" data-tt_pos="down" data-tt_text="Nueva tienda"><i class="icon-add"></i></button>
        <?php } ?>
    </div>
</header>

<main class="seccion" id="main_groups">
    <ol class="rowList" id="ul_groups"></ol>
</main>

<script type="module">
    import GROUPS from '/js/groups.js?1'
    import {may, debounce, selectModal, $} from '/js/exports.js?5'

    const jsonGroups = <?=json_encode(groups\listado())?>;
    var groups = new GROUPS(jsonGroups, $('search'), $('order'))

    $('search').onkeyup = (e)=> {
        sessionStorage.groupsSearch = e.currentTarget.value
        groups.printList()
    }
    $('order').onchange = (e)=> {
        localStorage.groupsOrder = e.currentTarget.value
        groups.printList()
    }
    $('refresh').onclick = ()=> { groups.refresh() }
    may ( ()=>{ $('add').onclick = ()=> { groups.modal(false) } } )

    if ( !!sessionStorage.groupsSearch ) { $('search').value = sessionStorage.groupsSearch }
    if ( !!localStorage.groupsOrder ) { $('order').value = localStorage.groupsOrder }
    new selectModal( 'order', 'Ordenar por', 'list' )

    groups.printList()
</script>

<?php include(ROOT_DIR.'/_footer.php'); ?>