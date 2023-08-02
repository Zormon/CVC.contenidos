<?php
global $login;
if (!$login->can['seeMusic']) { header('Location: /'); die; }
if ( !isset($_POST['shop']) ) { $_POST['shop'] = ''; }

include(ROOT_DIR.'/_header.php');
include(ROOT_DIR.'/_menu.php');

include(ROOT_DIR.'/tpl/music.php');
?>

<header class="actionBar">
    <div class="input search">
        <input id="search" type="search" placeholder=" ">
        <label for="search">Buscar canciones</label>
    </div>
    <div class="input">
        <select id="order">
            <option value="name" selected>Nombre</option>
            <option value="id" data-numeric>Identificador</option>
        </select>
        <label for="order">Ordenar por</label>
    </div>
    <div class="input">
        <select id="channel">
            <option value="0">Todos los canales</option>
            <?php
            $canales = music\playlist\listado();
            foreach ( $canales as $canal ) {
                ?><option value="<?=$canal['id']?>"><?=$canal['name']?></option><?php
            }
            ?>
        </select>
        <label for="channel">Canal</label>
    </div>
    <div class="tools">
        <button class="flat tooltip" id="refresh" data-tt_pos="down" data-tt_text="Recargar listado"><i class="icon-recargar"></i></button>
        <?php  if ( $login->can['editDevices'] ) { ?>
            <button class="flat tooltip" id="add" data-tt_pos="down" data-tt_text="Añadir canciones"><i class="icon-add"></i></button>
            <button class="flat tooltip" id="del" data-tt_pos="down" data-tt_text="Borrar canciones"><i class="icon-delete"></i></button>
        <?php } ?>
    </div>
</header>

<main class="seccion" id="main_music">
    <ul id="songList"></ul>
</main>

<script type="module">
    import {SONGS} from '/js/music.js?5'
    import {selectModal, $, may} from '/js/exports.js?5'

    var jsonCanciones = <?=json_encode( music\song\listado(false, true) )?>;
    var canciones = new SONGS(jsonCanciones, $('search'), $('order'), $('channel'))

    may( ()=>{
        $('del').onclick = ()=> { 
            const selected = canciones.selectableList.getSelectedNodes()
            if (selected.length > 0) {
                let ids = []
                selected.forEach(sel => { ids.push( sel.dataset.id ) })
                canciones.delete(ids)
            }
        }
    } )
    $('search').onkeyup = (e)=> {
        sessionStorage.musicSearch = e.currentTarget.value
        canciones.printList()
    }
    $('order').onchange = (e)=> {
        localStorage.musicOrder = e.currentTarget.value
        canciones.printList()
    }
    $('channel').onchange = (e)=> {
        localStorage.musicChannel = e.currentTarget.value
        canciones.printList()
    }
    $('refresh').onclick = ()=> { canciones.refresh() }
    may( ()=>{ $('add').onclick = ()=> { canciones.modal() } } )

    if ( !!sessionStorage.musicSearch ) { $('search').value = sessionStorage.musicSearch }
    if ( !!localStorage.musicOrder ) { $('order').value = localStorage.musicOrder }
    if ( !!localStorage.musicChannel ) { $('channel').value = localStorage.musicChannel }
    new selectModal( 'channel', 'Canal', 'list' )
    new selectModal( 'order', 'Ordenar por', 'list' )

    canciones.printList()
</script>

<?php include(ROOT_DIR.'/_footer.php'); ?>