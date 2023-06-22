<script type="text/template" id="rowGroup">
    <div class="content">
        <h3>{{name}}</h3>
        <p>
            <?php if ($login->isAdmin) { ?><small class="tag" style="background-color: #222">{{id}}</small><?php } ?>
            {{ndevices}} equipo{{#pdevices}}s{{/pdevices}}
        </p>
    </div>
    <div class="actions">
        <button class="trigger-actions flat nohover">&nbsp;<i class="icon-"></i>&nbsp;</button>
        <button class="gDetails tooltip" data-tt_pos="left" data-tt_text="Detalles">&nbsp;<i class="icon-details"></i>&nbsp;</button>
        <?php if ( $login->can['editGroups'] ) { ?>
            <hr>
            <button class="gEdit tooltip" data-tt_pos="left" data-tt_text="Editar">&nbsp;<i class="icon-edit"></i>&nbsp;</button>
            <button class="gDelete tooltip" data-tt_pos="left" data-tt_text="Borrar">&nbsp;<i class="icon-delete"></i>&nbsp;</button>
        <?php } ?>
    </div>
</script>

<script type="text/template" id="modalGroupDetails">
    <fieldset>
        <legend>Datos del grupo</legend>
        <div class="grid">
            <div class="g12">
                <p><b>Id:</b> {{id}}</p>
                <p><b>Nombre:</b> {{name}}</p>
                <p><b>Notas:</b> {{notes}}</p>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>Equipos ({{ndevices}})</legend>
        <div class="g12">
            {{#devices}}
                <p>{{name}}</p>
            {{/devices}}
        </div>
    </fieldset>
</script>


<script type="text/template" id="modalGroup">
    <form id="groupData">
        <fieldset>
            <legend>Grupo</legend>
            <div class="grid form">
                <div class="g6">
                    <div class="input icon-prefix icon-equipo">
                        <input name="name" type="text" pattern=".{3,}" value="{{name}}" required placeholder=" ">
                        <label for="name">Nombre tienda</label>
                    </div>
                </div>

                <div class="g12">
                    <div class="input icon-prefix icon-canales">
                        <select name="devices[]" id="devices" multiple>
                        <?php
                            $devices = devices\listado();
                            foreach ($devices as $equipo) {
                                ?><option id="eq<?=$equipo['id']?>" value="<?=$equipo['id']?>"><?=$equipo['name']?></option><?php
                            }
                        ?>
                        </select>
                        <label for="devices">devices</label>
                    </div>
                </div>

                <div class="g12">
                    <div class="input icon-prefix icon-nota">
                        <textarea rows="4" id="notes" name="notes" placeholder=" ">{{notes}}</textarea>
                        <label for="notes">Descripción</label>
                    </div>
                </div>
            </div>
        </fieldset>

        <input type="hidden" name="id" value="{{id}}">
    </form>
</script>