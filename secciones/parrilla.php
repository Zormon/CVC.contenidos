<?php
global $login;

include(ROOT_DIR.'/_header.php');
include(ROOT_DIR.'/_menu.php');

include(ROOT_DIR.'/tpl/media.php');
?>

<header class="actionBar">
    <div class="input">
        <select id="device">
            <?php
            $devices = devices\listado( array_column($login->devices, 'id') );
            foreach ( $devices as $equipo ) {
                ?><option value="<?=$equipo['id']?>"><?=$equipo['name']?></option><?php
            }
            ?>
        </select>
        <label for="device">Equipo</label>
    </div>
    <div class="tools">
        <button class="flat tooltip" id="refresh" data-tt_pos="down" data-tt_text="Recargar listado"><i class="icon-recargar"></i></button>
        <button class="flat tooltip" id="cropView" data-tt_pos="down" data-tt_text="Vista proporcional"><i class="icon-expandView"></i></button>
    </div>
</header>

<main class="seccion" id="parrilla">
    <h3 id="resumen"></h3>
    <ol id="listadoParrilla"></ol>
</main>

<script type="module">
    import {PARRILLA} from '/js/media.js?6'
    import {debounce,  selectModal, $, $$$} from '/js/exports.js?5'

    var expanded = localStorage.expandView == 'true'
    var parrilla = new PARRILLA()

    function expandView(expand) {
        let icon = $('cropView').firstChild
        if (expand) { // Expandir
            $('listadoParrilla').classList.add('expanded')
            icon.className = 'icon-reduceView'
        } else { // Reducir
            $('listadoParrilla').classList.remove('expanded')
            icon.className = 'icon-expandView'
        }

        localStorage.expandView = expand
    }

    $('device').onchange = $('refresh').onclick = (e)=> { 
        localStorage.parrillaDevice = $('device').value
        parrilla.refresh($('device').value).then(()=> {
            expandView(expanded)
        })
    }
    
    $('cropView').onclick = (e)=> { 
        expanded = !expanded
        expandView(expanded)
     }
     
    if ( !!localStorage.parrillaDevice ) { $('device').value = localStorage.parrillaDevice }
    new selectModal( 'device', 'Equipo', 'grid', true )

    parrilla.refresh($('device').value)
    expandView(expanded)
</script>

<?php include(ROOT_DIR.'/_footer.php'); ?>