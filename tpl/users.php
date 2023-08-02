<script type="text/template" id="rowUser">
    <div class="content">
        <h3>{{name}}</h3>
        <p>
            <b>Login: </b>{{login}}{{#can.isAdmin}}<em> (Administrador)</em>{{/can.isAdmin}}
        </p>
    </div>
    <div class="actions">
        <button class="trigger-actions flat nohover">&nbsp;<i class="icon-"></i>&nbsp;</button>
        <button class="uDetails tooltip" data-tt_pos="right" data-tt_text="Detalles">&nbsp;<i class="icon-details"></i>&nbsp;</button>
        <?php if ($login->can['editDevices']) { ?>
        <hr>
        <button class="uEdit tooltip" data-tt_pos="left" data-tt_text="Editar">&nbsp;<i class="icon-edit"></i>&nbsp;</button>
        <button class="uDelete tooltip" data-tt_pos="left" data-tt_text="Borrar">&nbsp;<i class="icon-delete"></i>&nbsp;</button>
        <?php } ?>
    </div>
</script>

<script type="text/template" id="detailsUser">
    <div class="content">
        <ul class="info">
            {{#email}}<li><i class="material-icons small">email</i> {{email}}</li> {{/email}}
            <li>
                <ul class="equipos">
                        {{#can.seeUsers}}<li>Ver soporte</li>{{/can.seeUsers}}
                        {{#can.seeBaul}}<li>Ver el baúl</li>{{/can.seeBaul}}
                        {{#can.seeLists}}<li>Ver listas</li>{{/can.seeLists}}
                        {{#can.seeGroups}}<li>Ver grupos</li>{{/can.seeGroups}}
                        {{#can.seeMusic}}<li>Ver música</li>{{/can.seeMusic}}
                        {{#can.seeDevices}}<li>Ver equipos</li>{{/can.seeDevices}}
                        {{#can.seeOrders}}<li>Ver encargos</li>{{/can.seeOrders}}
                        {{#can.seeIssues}}<li>Ver incidencias</li>{{/can.seeIssues}}
                        {{#can.seeNotif}}<li>Ver notificaciones</li>{{/can.seeNotif}}
                        {{#can.seeEvents}}<li>Ver eventos</li>{{/can.seeEvents}}

                        {{#can.editMedia}}<li>Editar el baúl</li>{{/can.editMedia}}
                        {{#can.editShops}}<li>Editar sus tiendas</li>{{/can.editShops}}
                        {{#can.editUsers}}<li>Editar usuarios</li>{{/can.editUsers}}
                        {{#can.editLists}}<li>Editar listas</li>{{/can.editLists}}
                        {{#can.editMusic}}<li>Gestionar música</li>{{/can.editMusic}}
                        {{#can.editGroups}}<li>Editar grupos</li>{{/can.editGroups}}
                        {{#can.editDevices}}<li>Editar equipos</li>{{/can.editDevices}}
                        {{#can.editOrders}}<li>Editar equipos</li>{{/can.editOrders}}
                        {{#can.editIssues}}<li>Editar equipos</li>{{/can.editIssues}}
                        {{#can.editNotif}}<li>Editar equipos</li>{{/can.editNotif}}
                        {{#can.editEvents}}<li>Editar equipos</li>{{/can.editEvents}}
                </ul>
            </li>
        </ul>
    </div>
</script>

<script type="text/template" id="modalUser">
    <form id="userData">
        <input type="hidden" name="id" value="{{id}}">
        <fieldset>
            <legend>Usuario</legend>
            <div class="grid form">
                <div class="g4">
                    <div class="input icon-prefix icon-usuario">
                        <input id="login" name="login" type="text" pattern=".{3,}" placeholder=" " value="{{login}}" <?=($login->isAdmin)?'required':'disabled'?>>
                        <label for="login">Login</label>
                    </div>
                </div>

                <div class="g4">
                    <div class="input">
                        <input id="name" name="name" type="text" pattern=".{5,}" placeholder=" " required value="{{name}}">
                        <label for="name">Nombre completo</label>
                    </div>
                </div>

                <div class="g4">
                    <div class="input icon-prefix icon-mail">
                        <input id="email" name="email" type="email" placeholder=" " value="{{email}}">
                        <label for="email">Email</label>
                    </div>
                </div>
            </div>
        </fieldset>


        <fieldset>
            <legend>Permisos</legend>
            <div class="grid form">
                <div class="g12">
                    <div class="input icon-prefix icon-tienda">
                        <select name="shops[]" id="shops" multiple>
                        <?php
                            $shops = shops\listado();
                            foreach ($shops as $shop) {
                                ?><option id="t<?=$shop['id']?>" value="<?=$shop['id']?>"><?=$shop['name']?></option><?php
                            }
                        ?>
                        </select>
                        <label for="shops">Tiendas</label>
                    </div>
                </div>

                <div class="g12">
                    <label><input type="checkbox" name="isAdmin" id="isAdmin" {{#can.isAdmin}}checked{{/can.isAdmin}} /><span>Admin</span></label>
                </div>

                <div class="g6">
                    <fieldset>
                        <legend>Ver</legend>
                        <ul>
                            <li><label><input type="checkbox" name="seeSupport" {{#can.seeSupport}}checked{{/can.seeSupport}} /><span>Soporte</span></label></li>
                            <li><label><input type="checkbox" name="seeUsers" {{#can.seeUsers}}checked{{/can.seeUsers}} /><span>Usuarios</span></label></li>
                            <li><label><input type="checkbox" name="seeBaul" {{#can.seeBaul}}checked{{/can.seeBaul}} /><span>Baul</span></label></li>
                            <li><label><input type="checkbox" name="seeLists" {{#can.seeLists}}checked{{/can.seeLists}} /><span>Listas</span></label></li>
                            <li><label><input type="checkbox" name="seeGroups" {{#can.seeGroups}}checked{{/can.seeGroups}} /><span>Grupos</span></label></li>
                            <li><label><input type="checkbox" name="seeMusic" {{#can.seeMusic}}checked{{/can.seeMusic}} /><span>Musica</span></label></li>
                            <li><label><input type="checkbox" name="seeDevices" {{#can.seeDevices}}checked{{/can.seeDevices}} /><span>Equipos</span></label></li>
                            <li><label><input type="checkbox" name="seeEvents" {{#can.seeEvents}}checked{{/can.seeEvents}} /><span>Eventos</span></label></li>
                        </ul>
                    </fieldset>
                </div>

                <div class="g6">
                    <fieldset>
                        <legend>Editar</legend>
                        <ul>
                            <li><label><input type="checkbox" name="editMedia" {{#can.editMedia}}checked{{/can.editMedia}} /><span>Media</span></label></li>
                            <li><label><input type="checkbox" name="editShops" {{#can.editShops}}checked{{/can.editShops}} /><span>Tiendas</span></label></li>
                            <li><label><input type="checkbox" name="editUsers" {{#can.editUsers}}checked{{/can.editUsers}} /><span>Usuarios</span></label></li>
                            <li><label><input type="checkbox" name="editLists" {{#can.editLists}}checked{{/can.editLists}} /><span>Listas</span></label></li>
                            <li><label><input type="checkbox" name="editMusic" {{#can.editMusic}}checked{{/can.editMusic}} /><span>Música</span></label></li>
                            <li><label><input type="checkbox" name="editGroups" {{#can.editGroups}}checked{{/can.editGroups}} /><span>Grupos</span></label></li>
                            <li><label><input type="checkbox" name="editDevices" {{#can.editDevices}}checked{{/can.editDevices}} /><span>Equipos</span></label></li>
                            <li><label><input type="checkbox" name="editEvents" {{#can.editEvents}}checked{{/can.editEvents}} /><span>Eventos</span></label></li>
                        </ul>
                    </fieldset>
                </div>
            </div>
        </fieldset>
    </form>
</script>