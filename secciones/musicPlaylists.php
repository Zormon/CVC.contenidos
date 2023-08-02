<?php
global $login;
if (!$login->can['seeMusic']) { header('Location: /'); die; }

include(ROOT_DIR.'/_header.php');
include(ROOT_DIR.'/_menu.php');

include(ROOT_DIR.'/tpl/music.php');
?>

<header class="actionBar">
    <div class="input search">
        <input id="search" type="search" placeholder=" ">
        <label for="search">Buscar lista</label>
    </div>
    <div class="tools">
        <button class="flat tooltip" id="refresh" data-tt_pos="down" data-tt_text="Recargar listado"><i class="icon-recargar"></i></button>
        <?php  if ( $login->can['editMusic'] ) { ?>
            <button class="flat tooltip" id="add" data-tt_pos="down" data-tt_text="Nueva lista"><i class="icon-add"></i></button>
        <?php } ?>
    </div>
</header>

<main class="seccion" id="main_musicPlaylists">
    <ol class="rowList" id="ul_musicPlaylists"></ol>
</main>


<script type="module">
    import {MUSICPLAYLISTS} from '/js/music.js?3'
    import {may, $} from '/js/exports.js?3'

    const jsonPlaylists = <?=json_encode( music\playlist\listado() )?>;
    const jsonSongs = <?=json_encode( music\song\listado(false, true) )?>;
    var playlists = new MUSICPLAYLISTS(jsonPlaylists, jsonSongs, $('search'))

    $('search').onkeyup = (e)=> {
        sessionStorage.musicPlaylistsSearch = e.currentTarget.value
        playlists.printList()
    }
    $('refresh').onclick = ()=> { playlists.refresh() }
    may ( ()=>{ $('add').onclick = ()=> { playlists.modal() } } )


    if ( !!sessionStorage.musicPlaylistsSearch ) { $('search').value = sessionStorage.musicPlaylistsSearch }

    playlists.printList()
</script>

<?php include(ROOT_DIR.'/_footer.php'); ?>