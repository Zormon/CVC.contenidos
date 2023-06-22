<script type="text/template" id="shopCard">
    <div class="content">
        <div class="title">
            <h2>{{name}}</h2>
            <h3>{{ndevices}} equipo{{#pdevices}}s{{/pdevices}}</h3>
        </div>

        <div class="actions">
            {{#telefono}}<a class="button flat" href="tel:+34{{telefono}}">Llamar</a>{{/telefono}}
            {{#email}}<a class="button flat" href="mailto:{{email}}">Email</a>{{/email}}
            <?php if ( $login->can['editShops'] ) { ?>
            <hr>
            <button class="flat tEdit">Editar</button>
            <button class="flat tDelete">Borrar</button>
            <?php } ?>
        </div>

        <ul class="info">
            {{#telefono}}<li><i class="icon-telefono"></i> {{telefono}}</li>{{/telefono}}
            {{#email}}<li><i class="icon-mail"></i> {{email}}</li> {{/email}}
            {{#direccion}}<li><i class="icon-gps"></i> {{direccion}}</li>{{/direccion}}
            {{#notas}}<li><i class="icon-nota"></i> {{notas}}</li>{{/notas}}
            <li><i class="icon-equipo"></i> Equipos:</li>
            <li>
                <ul class="devices">
                    {{#devices}}<li><?php if ( $login->isAdmin ) { ?>[{{id}}]&nbsp;&nbsp; <?php } ?>{{name}} ({{tipo}})</li>{{/devices}}
                </ul>
            </li>
    </div>
</script>

<script type="text/template" id="modalShop">
    <form id="shopData">
        <fieldset>
            <legend>Tienda</legend>
            <div class="grid form">
                <div class="g8">
                    <div class="input icon-prefix icon-equipo">
                        <input name="name" type="text" pattern=".{3,}" value="{{name}}" required placeholder=" ">
                        <label for="name">Nombre tienda</label>
                    </div>
                </div>

                <div class="g4">
                    <div class="input icon-prefix icon-equipo">
                        <input name="imagen" type="file" accept=".jpg,.png,.gif,.webp" id="imagen" <?php if ( !$login->isAdmin ) { ?>disabled<?php } ?>>
                        <label for="imagen">Foto de tienda</label>
                    </div>
                </div>
                
                <div class="g3">
                    <div class="input icon-prefix icon-telefono">
                        <input name="telefono" type="tel" pattern="[0-9]{9}" value="{{telefono}}" placeholder=" ">
                        <label for="telefono">Teléfono</label>
                    </div>
                </div>

                <div class="g4">
                    <div class="input icon-prefix icon-mail">
                        <input name="email" type="email" value="{{email}}" placeholder=" ">
                        <label for="email">Email</label>
                    </div>
                </div>

                <div class="g5">
                    <div class="input icon-prefix icon-gps">
                        <input name="direccion" type="text" value="{{direccion}}" placeholder=" ">
                        <label for="direccion">Dirección</label>
                    </div>
                </div>

                <div class="g12">
                    <div class="input icon-prefix icon-nota">
                        <textarea rows="4" id="notas" name="notas" placeholder=" ">{{notas}}</textarea>
                        <label for="notas">Notas</label>
                    </div>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Configuración</legend>
            <div class="grid form">
                <div class="g4">
                    <div class="input icon-prefix icon-canales">
                        <select name="canal" id="canal" required>
                        <?php
                            $canales = music\playlist\listado();
                            foreach ($canales as $canal) {
                                ?><option id="pl<?=$canal['id']?>" value="<?=$canal['id']?>"><?=$canal['name']?></option><?php
                            }
                        ?>
                        </select>
                        <label for="canal">Hilo musical</label>
                    </div>
                </div>
            </div>
        </fieldset>

        <input type="hidden" name="id" value="{{id}}">
    </form>
</script>