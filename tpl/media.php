<?php 
 global $mysql;

/** ******************************************************************
 ******************************* Baul *******************************
****************************************************************** */
?>

<script type="text/template" id="rowMedia">   
    {{#progress}}<div class="percent"></div>{{/progress}}
    <div class="tag" style="background-color:{{color}}">{{nameCategoria}}</div>
    <div class="previewMedia" data-type="{{type}}" ><img src="{{thumb}}"></div>
    <div class="content">
        <h3>{{name}}{{#audio}} <i class="icon-audio small noHeight"></i>{{/audio}}</h3>
        <p><i class="icon-calendar noHeight"></i> {{dateFromF}} | {{dateToF}} &nbsp;&nbsp;&nbsp; <i class="icon-clock noHeight"></i> {{duration}} s</p>
        {{#noLists}}<p class="warningText"><em>No está en listas o eventos. <b>¡No se emitirá!</b></em></p>{{/noLists}}
    </div>
    <div class="actions">
        <button class="trigger-actions flat nohover">&nbsp;<i class="icon-"></i>&nbsp;</button>
    <?php 
    if ($_PREFS['media']['pendientes']) {
        if ($_['mStat'] == 'pendientes') { ?>
            <button class="cEnable tooltip" data-tt_pos="right" data-tt_text="Activar">&nbsp;<i class="icon-enable"></i>&nbsp;</button>
        <?php } else { ?>
            <button class="cDisable tooltip" data-tt_pos="right" data-tt_text="Desactivar">&nbsp;<i class="icon-disable"></i>&nbsp;</button>
        <?php } 
    } ?>
        <button class="cDetails tooltip" data-tt_pos="right" data-tt_text="Detalles">&nbsp;<i class="icon-details"></i>&nbsp;</button>
    <?php if ($login->can['editMedia']) { ?>
        <hr>
        <button class="cEdit tooltip" data-tt_pos="left" data-tt_text="Editar">&nbsp;<i class="icon-edit"></i>&nbsp;</button>
        <button class="cDelete tooltip" data-tt_pos="left" data-tt_text="Borrar">&nbsp;<i class="icon-delete"></i>&nbsp;</button>
    <?php } ?>
    </div>
</script>

<script type="text/template" id="modalMediaDetails">
    <div class="grid">
        <div class="g6">
            <fieldset>
                <legend>Contenido</legend>
                <div class="grid">
                    <div class="g6">
                        <p><b>Id:</b> {{id}}</p>
                        <p><b>Nombre:</b> {{name}}</p>
                        <p><b>Archivo:</b> {{file}}</p>
                        <p><b>Desde:</b> {{dateFromF}}</p>
                        <p><b>Hasta:</b> {{dateToF}}</p>
                    </div>
                    <div class="g6">
                        <p><b>Volumen:</b> {{volume}}</p>
                        <p><b>Etiquetas:</b> {{tags}}</p>
                        <p><b>Categoría:</b> {{categoria}}</p>
                        <p><b>Estado:</b> {{status}}</p>
                        <p><b>Versión:</b> {{version}}</p>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="g6">
            <fieldset>
                <legend>Información técnica</legend>
                <div class="grid">
                    <div class="g6">
                        <p><b>Tipo:</b> {{mediainfo.type}}</p>
                        <p><b>Codec:</b> {{mediainfo.codec}} ({{mediainfo.profile}} profile)</p>
                        <p><b>Dimensiones:</b> {{mediainfo.width}} x {{mediainfo.height}}</p>
                        <p><b>Ratio:</b> {{mediainfo.ratio}} ({{mediainfo.orientation}})</p>
                    </div>
                    <div class="g6">
                        <p><b>Duracion:</b> {{mediainfo.duration}} s</p>
                        <p><b>Bitrate:</b> {{mediainfo.bitrate}}</p>
                        <p><b>Framerate:</b> {{mediainfo.framerate}}</p>
                        <p><b>Tamaño:</b> {{mediainfo.filesize}}</p>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</script>

<script type="text/template" id="modalEditMedia">
    <form id="datosContenido">
        <fieldset>
            <legend>Contenido</legend>
            <div class="grid form">
                <div class="g6">
                    <div class="input">
                        <input id="name" name="name" type="text" placeholder=" " value="{{name}}">
                        <label for="name">Nombre contenido</label>
                    </div>
                </div>

                <div class="g1">
                    <div class="input">
                        <input id="duration" name="duration" type="number" placeholder=" " value="{{duration}}">
                        <label for="duration">Duración</label>
                    </div>
                </div>

                <div class="g3">
                    <div class="input">
                        <select name="categoria" id="categoria">
                        <?php
                            foreach ($_PREFS['media']['types'] as $id => $cat) {
                                ?><option value="<?=$id?>"><?=$cat['name']?></option><?php
                            }
                        ?>
                        </select>
                        <label>Categoria</label>
                    </div>
                </div>

                <div class="g2">
                    <div class="input">
                        <input type="range" id="volume" name="volume" min="0" max="10" step="1" value="{{volume}}" />
                        <label for="volume">Volumen</label>
                    </div>
                </div>

                <div class="g4">
                    <div class="input">
                        <input type="file" accept=".png, .jpeg, .mp4, .mkv" name="file" id="file">
                        <label for="file">Archivo</label>
                    </div>
                </div>

                <div class="g4">
                    <div class="input">
                        <select name="transition" id="transition">
                        <?php
                            $values = $mysql->getEnumValues('media','transition');
                            foreach ($values as $id => $type) {
                                ?><option value="<?=$type?>"><?=$type?></option><?php
                            }
                        ?>
                        </select>
                        <label>Efecto de transición</label>
                    </div>
                </div>

                <div class="g4">
                    <div class="input">
                        <input id="tags" name="tags" type="text" pattern="[a-zA-Z]+(,[a-zA-Z]+)*" placeholder=" " value="{{tags}}">
                        <label for="tags">Tags</label>
                    </div>
                </div>

            </div>
        </fieldset>

        <fieldset>
            <legend>Programación</legend>
            <div class="grid form">
                <div class="col g12">
                    <div class="input">
                        <select name="devices[]" id="devices" multiple>
                        <?php
                            $devices = devices\listado();
                            foreach ($devices as $equipo) {
                                ?><option id="eq<?=$equipo['id']?>" value="<?=$equipo['id']?>"><?=$equipo['name']?></option><?php
                            }
                        ?>
                        </select>
                    </div>
                </div>

                <div class="col g6">
                    <div class="input">            
                        <input id="fDateFrom" name="fDateFrom" type="date" value="{{dateFrom}}">
                        <label for="fDateFrom">Fecha desde</label>
                    </div>
                </div>

                <div class="col g6">
                    <div class="input">            
                        <input id="fDateTo" name="fDateTo" type="date" value="{{dateTo}}">
                        <label for="fDateTo">Fecha hasta</label>
                    </div>
                </div>

                {{^id}}
                <div class="col g12">
                    <div class="input">            
                        <select name="addToPlaylist" id="addToPlaylist">
                            <option value="-1" selected>Ninguna</option>
                            <?php
                                $playlists = \media\playlist\listado();
                                foreach ($playlists as $pl) {
                                    ?><option id="pl<?=$pl['id']?>" value="<?=$pl['id']?>"><?=$pl['name']?></option><?php
                                }
                            ?>
                        </select>
                        <label>Añadir a lista</label>
                    </div>
                </div>
                {{/id}}
            </div>
        </fieldset>
        


        <input type="hidden" name="id" value="{{id}}">
    </form>
</script>

<script type="text/template" id="rowParrilla">
    {{#categoria}}<span class="category">{{name}}</span>{{/categoria}}
    {{#thumb}}<img src="{{thumb}}" class="thumb">{{/thumb}}
    <div id="segs">{{duration}}s</div>
    <div>
        <h2 class="title">{{name}}{{#audio}} <i class="icon-audio small noHeight"></i>{{/audio}}</h2>
        <p><b>Desde:</b> {{dateFromF}} &nbsp;&nbsp;&nbsp; <b>Hasta:</b> {{dateToF}} &nbsp;&nbsp;&nbsp; <b>Lista:</b> {{playlist}}</p>
    </div>
</script>


<?php
/** ******************************************************************
 ***************************** Playlists *****************************
****************************************************************** */
?>

<script type="text/template" id="rowMediaPlaylist">
    <div class="content">
        <h3>{{name}} (<?php if ($login->isAdmin) { ?><small>{{id}}</small><?php } ?>)</h3>
        <p>Asignada a <b>{{ndevices}}</b> equipos</p>
    </div>
    <div class="actions">
        <button class="trigger-actions flat nohover">&nbsp;<i class="icon-"></i>&nbsp;</button>
        <button class="bDetails tooltip" data-tt_pos="right" data-tt_text="Detalles">&nbsp;<i class="icon-details"></i>&nbsp;</button>
        <?php if ($login->can['editDevices']) { ?>
        <hr>
        <button class="bEdit tooltip" data-tt_pos="left" data-tt_text="Editar">&nbsp;<i class="icon-edit"></i>&nbsp;</button>
        <button class="bDelete tooltip" data-tt_pos="left" data-tt_text="Borrar">&nbsp;<i class="icon-delete"></i>&nbsp;</button>
        <?php } ?>
    </div>
</script>

<script type="text/template" id="modalEditMediaPlaylist">
    <form id="mediaPlaylistData" class="playlistEditor">
        <fieldset>
            <legend>Asignación</legend>
            <div class="grid form">
                <div class="g6">
                    <div class="input icon-prefix icon-equipo">
                        <input name="name" type="text" minlength="3" maxlength="30" value="{{name}}" required placeholder=" ">
                        <label for="name">Nombre lista</label>
                    </div>
                </div>
                <div class="g6">
                    <div class="input">
                        <select name="devices[]" id="devices" multiple>
                        <?php
                            $devices = devices\listado();
                            foreach ($devices as $equipo) {
                                ?><option id="eq<?=$equipo['id']?>" value="<?=$equipo['id']?>"><?=$equipo['name']?></option><?php
                            }
                        ?>
                        </select>
                        <label for="devices">Equipos</label>
                    </div>
                </div>
            </div>
        </fieldset>

        <div class="editor">
            <fieldset class="editList">
                <legend>Contenidos</legend>
                <ol class="sortableList" id="mediaList"></ol>
            </fieldset>
    
            <hr class="transfer-left">
    
            <fieldset class="catalog">
                <legend>Catálogo disponible</legend>
                <ol class="sortableListCatalog" id="mediaCatalog"></ol>
            </fieldset>
        </div>

        <input type="hidden" name="id" value="{{id}}">
    </form>
</script>