<script type="text/template" id="modalm3u8">
    <form id="datosListam3u8">
        <input type="hidden" name="channel" value="{{channel}}">
        <h5>Subir lista</h5>

        <p>El archivo m3u8 deberá tener los nombres de los archivos iguales a los que se subieron en la plataforma.</p>

        <div class="row">
            <div class="file-field input-field">
                <input type="file" accept=".m3u8, .txt" name="m3u8File" id="m3u8File" required>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text" placeholder="Archivo">
                </div>
            </div>
        </div>

        <p><strong>¡ATENCIÓN! Se sustituirá la lista actual.</strong></p>
    </form>
</script>

<script type="text/template" id="cardCanal">
    <div class="content">
        <div class="title">
            <h2>{{name}}</h2>
            <h3>{{ncanciones}} cancion{{#pcanciones}}es{{/pcanciones}}</h3>
            <i class="material-icons moreInfo">arrow_drop_up</i>
        </div>

        <a class="waves-effect waves-teal btn-flat" href="/canciones/{{id}}">Canciones</a>
        <?php if ( $login->can['editMusic'] ) { ?>
        <a class="waves-effect waves-teal btn-flat editShop">Editar</a>
        <a class="waves-effect waves-teal btn-flat delShop">Borrar</a>
        <?php } ?>
        <hr>

        <ul class="info">
            {{#descripcion}}{{descripcion}}</li>{{/descripcion}}
            <li><?php if ( $login->isAdmin ) { ?><li><i class="material-icons small">fingerprint</i> ID {{id}}</li><?php } ?>
            <li><i class="material-icons small">queue_music</i> Canciones:</li>
            <li>
                <ul class="canciones">
                    {{#canciones}}<li><?php if ( $login->isAdmin ) { ?>[{{id}}]&nbsp;&nbsp; <?php } ?>{{titulo}}</li>{{/canciones}}
                </ul>
            </li>
    </div>
</script>

<script type="text/template" id="rowCancion">
    <label class="sel hidden"><input type="checkbox" data-id="{{id}}"><span></span></label>
    <span>{{name}}</span>
    <button class="play"><i class="material-icons">play_arrow</i></button>
</script>


<script type="text/template" id="modalAddCanciones">
    <form id="addCanciones">
        <fieldset>
            <legend>Subir canciones</legend>
            <div class="grid form">
                <div class="g12">
                    <div class="input icon-prefix icon-canciones">
                        <input type="file" accept=".opus" id="canciones" multiple>
                        <label for="canciones">Canciones</label>
                    </div>
                </div>
            </div>

            <div class="g12">
                <div class="input icon-prefix icon-canales">
                    <select name="addToChannel" id="addToChannel">
                    <?php
                        $canales = music\playlist\listado();
                        foreach ($canales as $canal) {
                            ?><option id="cn<?=$canal['id']?>" value="<?=$canal['id']?>"><?=$canal['name']?></option><?php
                        }
                    ?>
                    </select>
                    <label for="addToChannel">Añadir al canal</label>
                </div>
            </div>
        </fieldset>
    </form>
</script>

<script type="text/template" id="modalMusicPlaylistDetails">
    <div class="grid">
        <div class="g12">
            {{name}}
        </div>
        <div class="g12">
            <fieldset>
                <legend>Canciones</legend>
                <table>
                    <tr>
                        <th>Título</th>
                        <th>Horario desde</th>
                        <th>Horario hasta</th>
                        <th>Archivo</th>

                    </tr>
                {{#songs}}
                    <tr>
                        <td>{{name}}</td>
                        <td>{{timeFrom}}</td>
                        <td>{{timeTo}}</td>
                        <td>{{file}}</td>
                    </tr>
                {{/songs}}
                </table>
            </fieldset>
        </div>
    </div>
</script>


<?php
/** ******************************************************************
 ***************************** Playlists *****************************
****************************************************************** */
?>

<script type="text/template" id="modalMusicPlaylist">
    <form id="musicPlaylistData" class="playlistEditor">
        <fieldset>
            <legend>Canal</legend>
            <div class="grid form">
                <div class="g4">
                    <div class="input icon-prefix icon-equipo">
                        <input name="name" type="text" minlength="3" maxlength="30" value="{{name}}" required placeholder=" ">
                        <label for="name">Nombre lista</label>
                    </div>
                </div>
                <div class="g8">
                    <div class="input icon-prefix icon-equipo">
                        <input name="notes" type="text" maxlength="100" value="{{notes}}" placeholder=" ">
                        <label for="notes">Descripción corta</label>
                    </div>
                </div>
            </div>
        </fieldset>

        <div class="editor">
            <fieldset class="editList">
                <legend>Canciones</legend>
                <ol class="sortableList" id="musicList"></ol>
            </fieldset>

            <hr class="transfer-left">

            <fieldset class="catalog">
                <legend>Catálogo disponible</legend>
                <ol class="sortableListCatalog" id="musicCatalog"></ol>
            </fieldset>
        </div>

        <input type="hidden" name="id" value="{{id}}">
    </form>
</script>

<script type="text/template" id="rowMusicPlaylist">
    <div class="content">
        <h3>{{name}}<?php if ($login->isAdmin) { ?><small> ({{id}})</small><?php } ?></h3>
        <p><b>{{nshops}}</b> tiendas suscritas</p>
    </div>
    <div class="actions">
        <button class="trigger-actions flat nohover">&nbsp;<i class="icon-"></i>&nbsp;</button>
        <button class="bDetails tooltip" data-tt_pos="right" data-tt_text="Detalles">&nbsp;<i class="icon-details"></i>&nbsp;</button>
        <?php if ($login->can['editMusic']) { ?>
        <hr>
        <button class="bEdit tooltip" data-tt_pos="left" data-tt_text="Editar">&nbsp;<i class="icon-edit"></i>&nbsp;</button>
        <button class="bDelete tooltip" data-tt_pos="left" data-tt_text="Borrar">&nbsp;<i class="icon-delete"></i>&nbsp;</button>
        <?php } ?>
    </div>
</script>