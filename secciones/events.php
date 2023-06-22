<?php 
global $login;
if (!$login->can['seeEvents']) { header('Location: /'); die; }

include(ROOT_DIR.'/_header.php');
include(ROOT_DIR.'/_menu.php');

include(ROOT_DIR.'/tpl/events.php'); 
?>

<header class="actionBar">
    <div class="input search">
        <input id="search" type="search" placeholder=" ">
        <label for="search">Buscar evento</label>
    </div>
    <div class="input">
        <select id="order">
            <option value="name" selected>Nombre</option>
            <option value="type">Tipo</option>
            <?php if ($login->isAdmin) { ?>
                <option value="id">Identificador</option>
            <?php } ?>
        </select>
        <label for="order">Ordenar por</label>
    </div>
    <div class="tools">
        <button class="flat tooltip" id="refresh" data-tt_pos="down" data-tt_text="Recargar listado"><i class="icon-recargar"></i></button>
        <?php  if ( $login->can['editEvents'] ) { ?>
            <button class="flat tooltip" id="add" data-tt_pos="down" data-tt_text="Nuevo evento"><i class="icon-add"></i></button>
        <?php } ?>
    </div>
</header>

<main class="seccion" id="main_events">
    <ol class="rowList" id="ul_events"></ol>
</main>

<script type="module">
    import EVENTS from '/js/events.js?7'
    import {may, debounce,  selectModal, $} from '/js/exports.js?5'

    const jsonEvents = <?=json_encode(events\query(false, array_column($login->devices, 'id')))?>;
    const jsonMedia = <?=json_encode(media\listado(ACTUALES, ['name', 'volume']))?>;
    var events = new EVENTS(jsonEvents, $('search'), $('order'))

    $('search').onkeyup = (e)=> {
        sessionStorage.eventsSearch = e.currentTarget.value
        events.printList()
    }
    $('order').onchange = (e)=> {
        localStorage.eventsOrder = e.currentTarget.value
        events.printList()
    }
    $('refresh').onclick = ()=> { events.refresh() }
    may ( ()=>{ $('add').onclick = ()=> { events.modal() } } )

    if ( !!sessionStorage.eventsSearch ) { $('search').value = sessionStorage.eventsSearch }
    if ( !!localStorage.eventsOrder ) { $('order').value = localStorage.eventsOrder }
    new selectModal( 'order', 'Ordenar por', 'list' )

    events.printList()
</script>

<?php include(ROOT_DIR.'/_footer.php'); ?>