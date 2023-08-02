<?php
global $login;
if (!$login->can['seeUsers']) { header('Location: /'); die; }

include(ROOT_DIR.'/_header.php');
include(ROOT_DIR.'/_menu.php');

include(ROOT_DIR.'/tpl/users.php'); 
?>

<header class="actionBar">
    <div class="input search">
        <input id="search" type="search" placeholder=" ">
        <label for="search">Buscar usuario</label>
    </div>
    <div class="input">
        <select id="order">
            <option value="login">Login</option>
            <option value="name" selected>Nombre</option>
            <option value="lastLogin">Última conexión</option>
        </select>
        <label for="order">Ordenar por</label>
    </div>
    <div class="tools">
        <button class="flat tooltip" id="refresh" data-tt_pos="down" data-tt_text="Recargar listado"><i class="icon-recargar"></i></button>
        <?php  if ( $login->can['editUsers'] ) { ?>
            <button class="flat tooltip" id="add" data-tt_pos="down" data-tt_text="Nuevo usuario"><i class="icon-add"></i></button>
        <?php } ?>
    </div>
</header>

<main class="seccion" id="main_users">
    <ol class="rowList" id="ul_users"></ol>
</main>

<script type="module">
    import USERS from '/js/users.js?4'
    import {may, debounce, sortJson,  selectModal, $} from '/js/exports.js?3'

    const jsonUsers = <?=json_encode(users\listado(), JSON_HEX_QUOT)?>;
    var users = new USERS(jsonUsers, $('search'), $('order'))

    $('search').onkeyup = (e)=> {
        sessionStorage.usersSearch = e.currentTarget.value
        users.printList()
    }
    $('order').onchange = (e)=> {
        localStorage.usersOrder = e.currentTarget.value
        users.printList()
    }

    $('refresh').onclick = ()=> { users.refresh(jsonUsers) }
    may ( ()=>{ $('add').onclick = ()=> { users.modal() } } )

    if ( !!sessionStorage.usersSearch ) { $('search').value = sessionStorage.usersSearch }
    if ( !!localStorage.usersOrder ) { $('order').value = localStorage.usersOrder }
    new selectModal( 'order', 'Ordenar por', 'list' )

    users.printList()
</script>

<?php include(ROOT_DIR.'/_footer.php'); ?>