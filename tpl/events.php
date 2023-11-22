<script type="text/template" id="rowEvent">
    <div class="content">
        <h3>{{name}}</h3>
        <p>Asignado a <b>{{ndevices}}</b> devices</p>
    </div>
    <div class="actions">
        <button class="trigger-actions flat nohover">&nbsp;<i class="icon-"></i>&nbsp;</button>
        <button class="bDetails tooltip" data-tt_pos="right" data-tt_text="Detalles">&nbsp;<i class="icon-details"></i>&nbsp;</button>
        <button class="bLogs tooltip" data-tt_pos="right" data-tt_text="Registros">&nbsp;<i class="icon-logs"></i>&nbsp;</button>
        <?php if ($login->can['editEvents']) { ?>
        <hr>
        <button class="bEdit tooltip" data-tt_pos="left" data-tt_text="Editar">&nbsp;<i class="icon-edit"></i>&nbsp;</button>
        <button class="bDelete tooltip" data-tt_pos="left" data-tt_text="Borrar">&nbsp;<i class="icon-delete"></i>&nbsp;</button>
        <?php } ?>
    </div>
</script>

<script type="text/template" id="modalEvent">
    <form id="datosEvent">
        <fieldset>
            <legend>Evento</legend>
            <div class="grid form">
                <div class="g6">
                    <div class="input icon-prefix icon-equipo">
                        <input name="name" type="text" pattern=".{3,}" value="{{name}}" required placeholder=" ">
                        <label for="name">Nombre evento</label>
                    </div>
                </div>

                <div class="g6">
                    <div class="input">
                        <select name="devices[]" id="devices" multiple required>
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

                <div class="g6">
                <div class="input">
                        <select name="media" id="media" required>
                        <?php
                            $media = media\listado(ACTUALES, ['name', 'volume']);
                            foreach ($media as $m) {
                                ?><option id="med<?=$m['id']?>" value="<?=$m['id']?>"><?=$m['name']?></option><?php
                            }
                        ?>
                        </select>
                        <label for="media">Contenido</label>
                    </div>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Programación</legend>
            <div class="grid form">
                <div class="g4">
                    <div class="input">
                        <input id="dateFrom" name="dateFrom" type="date" value="{{dateFrom}}" required>
                        <label for="dateFrom">Fecha desde</label>
                    </div>
                </div>

                <div class="g4">
                    <div class="input">
                        <input id="dateTo" name="dateTo" type="date" value="{{dateTo}}" required>
                        <label for="dateTo">Fecha hasta</label>
                    </div>
                </div>

                <div class="g4">
                    <div class="input">
                        <input id="time" name="time" type="time" value="{{time}}" required>
                        <label for="time">Hora</label>
                    </div>
                </div>

                <div class="g12">
                    <div class="input">
                        <input name="week1" id="week1" type="checkbox" {{week1}}>
                        <label for="week1">Lunes</label>
                        <input name="week2" id="week2" type="checkbox" {{week2}}>
                        <label for="week2">Martes</label>
                        <input name="week3" id="week3" type="checkbox" {{week3}}>
                        <label for="week3">Miércoles</label>
                        <input name="week4" id="week4" type="checkbox" {{week4}}>
                        <label for="week4">Jueves</label>
                        <input name="week5" id="week5" type="checkbox" {{week5}}>
                        <label for="week5">Viernes</label>
                        <input name="week6" id="week6" type="checkbox" {{week6}}>
                        <label for="week6">Sábado</label>
                        <input name="week7" id="week7" type="checkbox" {{week7}}>
                        <label for="week7">Domingo</label>
                    </div>
                </div>
            </div>
        </fieldset>

        <input type="hidden" name="id" value="{{id}}">
    </form>
</script>