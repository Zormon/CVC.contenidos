<script type="text/template" id="cardGroup">
    <div class="content">
        <div class="title">
            <h2>{{name}}</h2>
            <h3>{{ndevices}} equipo{{#pdevices}}s{{/pdevices}}</h3>
        </div>

        
        <div class="actions">
        <button class="flat gDetails">Detalles</button>
        <?php if ( $login->can['editGroups'] ) { ?>
            <hr>
            <button class="flat gEdit">Editar</button>
            <button class="flat gDelete">Borrar</button>
            <?php } ?>
        </div>
        

        <ul class="info">
            {{#notes}}<li><i class="icon-nota"></i> {{notes}}</li>{{/notes}}
            <li><i class="icon-equipo"></i> Equipos:</li>
            <li>
                <ul class="devices">
                    {{#devices}}<li><?php if ( $login->isAdmin ) { ?>[{{id}}]&nbsp;&nbsp; <?php } ?>{{name}} ({{tipo}})</li>{{/devices}}
                </ul>
            </li>
        </ul>
    </div>
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

                <div class="g4">
                    <div class="input icon-prefix icon-equipo">
                        <input name="imagen" type="file" id="imagen">
                        <label for="imagen">Foto de tienda</label>
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