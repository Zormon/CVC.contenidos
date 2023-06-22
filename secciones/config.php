<?php
global $_PREFS;
if (!$login->isAdmin) { header('Location: /'); die; }

include(ROOT_DIR.'/_header.php');
include(ROOT_DIR.'/_menu.php');
?>

<header class="actionBar">
    <span></span>
    <div class="tools">
        <button class="flat" id="save"><i class="icon-save"></i></button>
    </div>
</header>

<main class="seccion" id="main_config">
    <form id="appConfig">
        <fieldset class="grid form">
            <legend>Plataforma</legend>
                <div class="g6">
                    <div class="input">
                        <input name="name" type="text" value="<?=$_PREFS['app']['name']?>" required placeholder=" ">
                        <label for="name">Nombre de aplicación</label>
                    </div>
                </div>
                <div class="g6">
                    <div class="input">
                        <input name="domain" type="text" value="<?=$_PREFS['app']['domain']?>" required placeholder=" ">
                        <label for="domain">Dominio</label>
                    </div>
                </div>
                <div class="g4">
                    <div class="input">
                        <input name="mediaURL" type="text" value="<?=$_PREFS['path']['media']?>" required placeholder=" ">
                        <label for="mediaURL">URL de contenidos</label>
                    </div>
                </div>
                <div class="g4">
                    <div class="input">
                        <input name="musicURL" type="text" value="<?=$_PREFS['path']['music']?>" required placeholder=" ">
                        <label for="musicURL">URL de música</label>
                    </div>
                </div>
                <div class="g4">
                    <div class="input">
                        <input type="checkbox" name="offline" <?=$_PREFS['app']['offline']?'checked':''?>>
                        <label for="offline">Deshabilitada</label>
                    </div>
                </div>
                <div class="g4">
                    <div class="input">
                        <input type="checkbox" name="pendientes" <?=$_PREFS['media']['pendientes']?'checked':''?>>
                        <label for="pendientes">Gestión de pendientes</label>
                    </div>
                </div>
                <div class="g4">
                    <div class="input">
                        <select name="clientType" id="clientType">
                            <option value="single">Cliente único</option>
                            <option value="multiple">Multicliente</option>
                            <option value="multilist">Multilista (no disponible)</option>
                        </select>
                        <label for="clientType">Gestión de clientes</label>
                    </div>
                </div>
        </fieldset>

        <fieldset class="grid form">
            <legend>Apariencia</legend>

            <div class="g2"><img src="/img/mainLogo.webp" style="max-width:100%;max-height: 50px;"></div>
            <div class="g4">
                <div class="input">
                    <input type="file" name="logo" accept=".png,.jpg,.gif,.webp">
                    <label for="logo">Logo <small>(max 800w)</small></label>
                </div>
            </div>

            <div class="g1"><img src="/img/favicon.png" style="width:32px"></div>
            <div class="g5">
                <div class="input">
                    <input type="file" name="favicon" accept=".png,.jpg,.gif,.webp">
                    <label for="favicon">Favicon <small>(32x32)</small></label>
                </div>
            </div>

            <div class="g3 input">
                <input type="color" name="mainColor" value="<?=$_PREFS['color']['main']?>">
                <label for="mainColor">Color principal</label>
            </div>
            <div class="g3 input">
                <input type="color" name="secondaryColor" value="<?=$_PREFS['color']['secondary']?>">
                <label for="secondaryColor">Color secundario</label>
            </div>
            <div class="g3 input">
                <input type="color" name="emphasisColor" value="<?=$_PREFS['color']['emphasis']?>">
                <label for="emphasisColor">Color de énfasis</label>
            </div>
            <div class="g3 input">
                <input type="color" name="neutralColor" value="<?=$_PREFS['color']['neutral']?>">
                <label for="neutralColor">Color neutro</label>
            </div>
            <div class="g4 input">
                <input type="color" name="infoColor" value="<?=$_PREFS['color']['info']?>">
                <label for="infoColor">Color de información</label>
            </div>
            <div class="g4 input">
                <input type="color" name="warnColor" value="<?=$_PREFS['color']['warn']?>">
                <label for="warnColor">Color de aviso</label>
            </div>
            <div class="g4 input">
                <input type="color" name="errorColor" value="<?=$_PREFS['color']['error']?>">
                <label for="errorColor">Color de error</label>
            </div>
        </fieldset>

        <fieldset class="grid form">
            <legend>Contenidos</legend>
                <div class="g4 input">
                    <input name="defaultDuration" type="number" min="0" max="200" value="<?=$_PREFS['media']['defaults']['duration']?>" required placeholder=" ">
                    <label for="defaultDuration">Duracion de imagenes (segundos)</label>
                </div>
                <div class="g4 input">
                    <input name="defaultVolume" type="number" min="0" max="10" value="<?=$_PREFS['media']['defaults']['volume']?>" required placeholder=" ">
                    <label for="defaultVolume">Volumen predeterminado</label>
                </div>
                <div class="g4 input">
                    <input name="daysToEndDate" type="number" min="-1" max="2000" value="<?=$_PREFS['media']['defaults']['daysToEndDate']?>" required placeholder="-1">
                    <label for="daysToEndDate">Dias de caducidad de contenido</label>
                </div>
                <fieldset class="g12">
                    <legend>Categorías</legend>
                    <div id="cats"></div>
                </fieldset>
        </fieldset>

        <fieldset class="g12">
            <legend>Dispositivos</legend>
            <div id="devTypes"></div>
        </fieldset>

    </form>
</main>

<script type="module">
    import {$,$$,$$$,editableList,selectModal} from '/js/exports.js?3'

    $$$('button.removeCat').forEach(but => {
        but.onclick = (e)=> { e.currentTarget.parentElement.remove() }
    })

    // Categorias
    var catListJson = {
        fields: [{type:'text',name:'Categoría',width:2},{type:'color',name:'Color', width:1}],
        items: [
        <?php foreach ($_PREFS['media']['types'] as $key => $type) { ?>
            ['<?=$type['name']?>','<?=$type['color']?>'],
        <?php } ?>
        ]}
    var catList = new editableList('cats', catListJson)

    // Tipos de dispositivos
    var devTypesJson = {
        fields: [{type:'text',name:'Tipo',width:2},{type:'color',name:'color', width:1},{type:'text',name:'icon', width:1}],
        items: [
        <?php foreach ($_PREFS['devices']['types'] as $key => $type) { ?>
            ['<?=$type['name']?>','<?=$type['color']?>', '<?=$type['icon']?>'],
        <?php } ?>
        ]}
    var devTypeList = new editableList('devTypes', devTypesJson)

    $('clientType').value = '<?=$_PREFS['app']['clientType']?>'
    $('clientType').selectModal = new selectModal( 'clientType', 'Gestion de clientes', 'list' )


    $('save').onclick = ()=> { 
        if ( $('appConfig').reportValidity() ) {
            var formData = new FormData($('appConfig'))
            formData.append('categories', JSON.stringify(catList.getData()))
            formData.append('deviceTypes', JSON.stringify(devTypeList.getData()))
            formData.append('mode','save')
            
            fetch('/api/config', { method: 'POST', body: formData}).then(resp => resp.json()).then( (data)=> { 
                if ( data.status == 'ok' )  { location.reload() }
                else                        { alert(`ERROR: ${data.error}`) }
            })
        }


    }
</script>

<?php include(ROOT_DIR.'/_footer.php'); ?>