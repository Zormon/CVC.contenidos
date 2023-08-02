<script type="text/template" id="rowEquipo">
    <div class="tag" style="background-color: {{color}}">{{nameTipo}}</div>
    <div class="content">
        <h3>{{name}}</h3>
        <p>
            <?php if ($login->isAdmin) { ?><small class="tag" style="background-color: #222">{{id}}</small><?php } ?>
            <i class="icon-tienda noHeight"></i> {{shopName}}&nbsp;&nbsp;
            <i class="icon-router noHeight"></i> {{lastConnect}} / {{lastIp}}
        </p>
    </div>
    <div class="actions">
        <button class="trigger-actions flat nohover">&nbsp;<i class="icon-"></i>&nbsp;</button>
        <button class="eParrilla tooltip" data-tt_pos="right" data-tt_text="Parrilla">&nbsp;<i class="icon-parrilla"></i>&nbsp;</button>
        <hr>
        <button class="bDetails tooltip" data-tt_pos="right" data-tt_text="Detalles">&nbsp;<i class="icon-details"></i>&nbsp;</button>
        <button class="eLogs tooltip" data-tt_pos="right" data-tt_text="Registros">&nbsp;<i class="icon-logs"></i>&nbsp;</button>
        <?php if ($login->can['editDevices']) { ?>
        <hr>
        <button class="bEdit tooltip" data-tt_pos="left" data-tt_text="Editar">&nbsp;<i class="icon-edit"></i>&nbsp;</button>
        <button class="bDelete tooltip" data-tt_pos="left" data-tt_text="Borrar">&nbsp;<i class="icon-delete"></i>&nbsp;</button>
        <?php } ?>
    </div>
</script>

<script type="text/template" id="modalDeviceDetails">
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

<script type="text/template" id="modalEquipo">
    <form id="datosEquipo">
        <fieldset>
            <legend>Equipo</legend>
            <div class="grid form">
                <div class="g8">
                    <div class="input icon-prefix icon-equipo">
                        <input name="name" type="text" pattern=".{3,}" value="{{name}}" required placeholder=" ">
                        <label for="name">Nombre</label>
                    </div>
                </div>

                <div class="g4">
                    <div class="input">
                        <select name="tipo" id="tipo">
                        <?php
                            foreach ($_PREFS['devices']['types'] as $id => $type) {
                                ?><option id="t<?=$id+1?>" value="<?=$id+1?>"><?=$type['name']?></option><?php
                            }
                        ?>
                        </select>
                        <label for="tipo">Tipo</label>
                    </div>
                </div>

                <div class="g12">
                    <div class="input icon-prefix icon-shop">
                        <select id="shop" name="shop">
                            <?php
                            $shops = shops\listado( array_column($login->shops, 'id') );
                            foreach ( $shops as $shop ) {
                                ?><option data-icon="/img/shops/<?=$shop['id']?>.webp" value="<?=$shop['id']?>"><?=$shop['name']?></option><?php
                            }
                            ?>
                        </select>
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
            <legend>Encendido</legend>
            <div class="grid form">
                <div class="g4">
                    <div class="input icon-prefix icon-disable">
                        <input id="exDays" name="exDays" type="text" pattern="[0-9](,[0-9]{1,})*" value="{{exDays}}" placeholder=" ">
                        <label for="exDays">Excluir días</label>
                    </div>
                </div>

                <div class="input input g4">
                    <select name="hFormato" id="hFormato">
                        <option value="LV">Lunes-Viernes</option>
                        <option value="LV+S">Lunes-Viernes, Sábado</option>
                        <option value="LV+S+D">Lunes-Viernes, Sábado, Domingo</option>
                        <option value="LJ+VD">Lunes-Jueves, Viernes-Domingo</option>
                        <option value="L+M+X+J+V+S+D">Todos los días</option>
                    </select>
                    <label for="hFormato">Formato</label>
                </div>

                <div class="input input g4">
                    <select name="sleepType" id="sleepType">
                        <option value="mem">mem</option>
                        <option value="freeze">freeze</option>
                        <option value="disk">disk</option>
                        <option value="off">off</option>
                    </select>
                    <label for="sleepType">Tipo de apagado</label>
                </div>
            </div>
            
            <div class="grid form horarios">
                <div class="g6 hidden" id="hLV">
                    <h3>Lunes a viernes</h3>
                    <div class="input icon-prefix icon-on">
                        <input name="LVon" type="time" value="{{power.L.on}}" placeholder=" ">
                    </div>
                    <div class="input icon-prefix icon-off">
                        <input name="LVoff" type="time" value="{{power.L.off}}" placeholder=" ">
                    </div>
                </div>

                <div class="g6 hidden" id="hLJ">
                    <h3>Lunes a jueves</h3>
                    <div class="input icon-prefix icon-on">
                        <input name="LJon" type="time" value="{{power.L.on}}" placeholder=" ">
                    </div>
                    <div class="input icon-prefix icon-off">
                        <input name="LJoff" type="time" value="{{power.L.off}}" placeholder=" ">
                    </div>
                </div>

                <div class="g6 hidden" id="hVD">
                    <h3>Viernes a domingo</h3>
                    <div class="input icon-prefix icon-on">
                        <input name="VDon" type="time" value="{{power.V.on}}" placeholder=" ">
                    </div>
                    <div class="input icon-prefix icon-off">
                        <input name="VDoff" type="time" value="{{power.V.off}}" placeholder=" ">
                    </div>
                </div>

                <div class="g3 hidden" id="hL">
                    <h3>Lunes</h3>
                    <div class="input icon-prefix icon-on">
                        <input name="Lon" type="time" value="{{power.L.on}}" placeholder=" ">
                    </div>
                    <div class="input icon-prefix icon-off">
                        <input name="Loff" type="time" value="{{power.L.off}}" placeholder=" ">
                    </div>
                </div>

                <div class="g3 hidden" id="hM">
                    <h3>Martes</h3>
                    <div class="input icon-prefix icon-on">
                        <input name="Mon" type="time" value="{{power.M.on}}" placeholder=" ">
                    </div>
                    <div class="input icon-prefix icon-off">
                        <input name="Moff" type="time" value="{{power.M.off}}" placeholder=" ">
                    </div>
                </div>

                <div class="g3 hidden" id="hX">
                    <h3>Miércoles</h3>
                    <div class="input icon-prefix icon-on">
                        <input name="Xon" type="time" value="{{power.X.on}}" placeholder=" ">
                    </div>
                    <div class="input icon-prefix icon-off">
                        <input name="Xoff" type="time" value="{{power.X.off}}" placeholder=" ">
                    </div>
                </div>

                <div class="g3 hidden" id="hJ">
                    <h3>Jueves</h3>
                    <div class="input icon-prefix icon-on">
                        <input name="Jon" type="time" value="{{power.J.on}}" placeholder=" ">
                    </div>
                    <div class="input icon-prefix icon-off">
                        <input name="Joff" type="time" value="{{power.J.off}}" placeholder=" ">
                    </div>
                </div>

                <div class="g3 hidden" id="hV">
                    <h3>Viernes</h3>
                    <div class="input icon-prefix icon-on">
                        <input name="Von" type="time" value="{{power.V.on}}" placeholder=" ">
                    </div>
                    <div class="input icon-prefix icon-off">
                        <input name="Voff" type="time" value="{{power.V.off}}" placeholder=" ">
                    </div>
                </div>

                <div class="g3 hidden" id="hS">
                    <h3>Sábado</h3>
                    <div class="input icon-prefix icon-on">
                        <input name="Son" type="time" value="{{power.S.on}}" placeholder=" ">
                    </div>
                    <div class="input icon-prefix icon-off">
                        <input name="Soff" type="time" value="{{power.S.off}}" placeholder=" ">
                    </div>
                </div>

                <div class="g3 hidden" id="hD">
                    <h3>Domingo</h3>
                    <div class="input icon-prefix icon-on">
                        <input name="Don" type="time" value="{{power.D.on}}" placeholder=" ">
                    </div>
                    <div class="input icon-prefix icon-off">
                        <input name="Doff" type="time" value="{{power.D.off}}" placeholder=" ">
                    </div>
                </div>
            </div>
        </fieldset>

        <input type="hidden" name="id" value="{{id}}">
    </form>
</script>