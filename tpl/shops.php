<script type="text/template" id="rowShop">
    <div class="content">
        <h3>{{name}}</h3>
        <p>
            <?php if ($login->isAdmin) { ?><small class="tag" style="background-color: #222">{{id}}</small><?php } ?>
            {{ndevices}} equipo{{#pdevices}}s{{/pdevices}}
        </p>
    </div>
    <div class="actions">
        <button class="trigger-actions flat nohover">&nbsp;<i class="icon-"></i>&nbsp;</button>
        {{#telefono}}<button class="tCall tooltip" data-tt_pos="right" data-tt_text="Llamar">&nbsp;<i class="icon-telefono"></i>&nbsp;</button>{{/telefono}}
        {{#email}}<button class="tMail tooltip" data-tt_pos="right" data-tt_text="Enviar correo">&nbsp;<i class="icon-mail"></i>&nbsp;</button>{{/email}}
        <?php if ( $login->can['editShops'] ) { ?>
        <hr>
        <button class="tEdit tooltip" data-tt_pos="left" data-tt_text="Editar">&nbsp;<i class="icon-edit"></i>&nbsp;</button>
        <button class="tDelete tooltip" data-tt_pos="left" data-tt_text="Borrar">&nbsp;<i class="icon-delete"></i>&nbsp;</button>
        <?php } ?>
    </div>
</script>

<script type="text/template" id="modalShop">
    <form id="shopData">
        <fieldset>
            <legend>Tienda</legend>
            <div class="grid form">
                <div class="g12">
                    <div class="input icon-prefix icon-equipo">
                        <input name="name" type="text" pattern=".{3,}" value="{{name}}" required placeholder=" ">
                        <label for="name">Nombre tienda</label>
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
                <div class="g8">
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