<?php
if (!$login->can['seeBaul']) { header('Location: /'); die; }
if ( !isset($_POST['shop']) ) { $_POST['shop'] = ''; }

include(ROOT_DIR.'/_header.php');
include(ROOT_DIR.'/_menu.php');

require(ROOT_DIR.'/tpl/media.php');

?>

<header class="actionBar">
    <div class="input search">
        <input id="search" type="search" placeholder=" ">
        <label for="search">Buscar contenido</label>
    </div>
    <div class="input">
        <select id="order">
            <option value="name" selected>Nombre</option>
            <option value="duration" data-numeric>Duración</option>
            <option value="categoria">Categoría</option>
            <option value="dateFrom">Desde</option>
            <option value="dateTo">Hasta</option>
            <?php if ($login->isAdmin) { ?>
                <option value="id" data-numeric>Identificador</option>
            <?php } ?>
        </select>
        <label for="order">Ordenar por</label>
    </div>
    <div class="input">
        <select id="device">
            <option value="0">Todos los equipos</option>
            <?php
            $devices = devices\listado( array_column($login->devices, 'id') );
            foreach ( $devices as $equipo ) {
                ?><option value="<?=$equipo['id']?>"><?=$equipo['name']?></option><?php
            }
            ?>
        </select>
    </div>
    <div class="toggle naranja">
        <input type="checkbox" id="filterAudio">
        <label for="filterAudio">Con audio</label>
    </div>
    <div class="tools">
        <button class="flat tooltip" id="refresh" data-tt_pos="down" data-tt_text="Recargar listado"><i class="icon-recargar"></i></button>
        <?php  if ( $login->can['editDevices'] ) { ?>
            <button class="flat tooltip" id="add" data-tt_pos="down" data-tt_text="Nuevo contenido"><i class="icon-add"></i></button>
        <?php } ?>
    </div>
</header>

<main class="seccion" id="main_media">
    <ol class="rowList" id="listaUploads"></ol>
    <ol class="rowList" id="mediaList"></ol>
</main>

<script type="module">
    import {MEDIA} from '/js/media.js?5'
    import {may, debounce, selectModal, $} from '/js/exports.js?5'

    const jsonMedia = <?=json_encode( media\listado(constant(strtoupper($_['mStat']))) )?>;
    var media = new MEDIA(jsonMedia, <?=constant(strtoupper($_['mStat']))?>, $('search'), $('order'), $('device'), $('filterAudio') )
    
    //media.dragContainer( $('main_media') )

    $('search').onkeyup = (e)=> {
        sessionStorage.mediaSearch = e.currentTarget.value
        media.printList()
    }
    $('order').onchange = (e)=> {
        localStorage.mediaOrder = e.currentTarget.value
        media.printList()
    }
    $('device').onchange = (e)=> {
        sessionStorage.mediaDevice = e.currentTarget.value
        media.printList()
    }
    $('filterAudio').onchange = (e)=> {
        localStorage.mediaFilterAudio = e.currentTarget.checked
        media.printList()
    }

    $('refresh').onclick = ()=> { media.refresh() }
    may( ()=>{ $('add').onclick = ()=> { media.modal(false) } } )

    if ( !!sessionStorage.mediaSearch ) { $('search').value = sessionStorage.mediaSearch }
    if ( !!localStorage.mediaOrder ) { $('order').value = localStorage.mediaOrder }
    if ( !!sessionStorage.mediaDevice ) { $('device').value = sessionStorage.mediaDevice }
    if ( !!localStorage.mediaFilterAudio ) { $('filterAudio').checked = localStorage.mediaFilterAudio === 'true' }
    new selectModal( 'device', 'Equipo', 'grid', true )
    new selectModal( 'order', 'Ordenar por', 'list' )

    media.printList()
</script>

<?php include(ROOT_DIR.'/_footer.php'); ?>