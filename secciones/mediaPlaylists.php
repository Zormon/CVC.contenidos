<?php
global $login;
if (!$login->can['seeLists']) { header('Location: /'); die; }

include(ROOT_DIR.'/_header.php');
include(ROOT_DIR.'/_menu.php');

include(ROOT_DIR.'/tpl/media.php');
?>

<header class="actionBar">
    <div class="input search">
        <input id="search" type="search" placeholder=" ">
        <label for="search">Buscar lista</label>
    </div>
    <div class="tools">
        <button class="flat tooltip" id="refresh" data-tt_pos="down" data-tt_text="Recargar listado"><i class="icon-recargar"></i></button>
        <?php  if ( $login->can['editLists'] ) { ?>
            <button class="flat tooltip" id="add" data-tt_pos="down" data-tt_text="Nueva lista"><i class="icon-add"></i></button>
        <?php } ?>
    </div>
</header>

<main class="seccion" id="main_mediaPlaylists">
    <ol class="rowList" id="ul_mediaPlaylists"></ol>
</main>

<script type="module">
    import {MEDIAPLAYLISTS} from '/js/media.js?5'
    import {debounce, selectModal, sortableList, sortableListCatalog, may, $, $$$} from '/js/exports.js?5'

    const jsonPlaylists = <?=json_encode(media\playlist\listado())?>;
    const jsonMedia = <?=json_encode(media\listado(TODOS))?>;
    var playlists = new MEDIAPLAYLISTS(jsonPlaylists, jsonMedia, $('search'))
    
    $('search').onkeyup = (e)=> {
        sessionStorage.mediaPlaylistsSearch = e.currentTarget.value
        playlists.printList()
    }
    $('refresh').onclick = ()=> { playlists.refresh() }
    may( ()=>{ $('add').onclick = ()=> { playlists.modal() } } )

    if ( !!sessionStorage.mediaPlaylistsSearch ) { $('search').value = sessionStorage.mediaPlaylistsSearch }

    playlists.printList()
</script>

<?php include(ROOT_DIR.'/_footer.php'); ?>