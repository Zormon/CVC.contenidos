<?php
global $_PREFS;
?>

<nav id="mainMenu">
    <section>
        <header>Plataforma</header>
        <a id="sec_home" href="/home"><i class="icon-general"></i>General</a>
        <a id="sec_tiendas" href="/tiendas"><i class="icon-tienda"></i>Tiendas</a> 
    <?php if ($login->can['seeDevices']) { ?>
        <a id="sec_equipos" href="/equipos"><i class="icon-equipo"></i>Equipos</a>
    <?php } ?>
    <?php if ($login->can['seeGroups']) { ?>
        <a id="sec_grupos" href="/grupos"><i class="icon-grupos"></i>Grupos</a>
    <?php } ?>
    <?php if ($login->can['seeUsers']) { ?>
        <a id="sec_usuarios" href="/usuarios"><i class="icon-usuarios"></i>Usuarios</a>
    <?php } ?>
    </section>

    <section>
        <header>Programación</header>
        <a id="sec_parrilla" href="/parrilla"><i class="icon-parrilla"></i>Parrilla</a>
    <?php if ($login->can['seeLists']) { ?>
        <a id="sec_listas" href="/listas"><i class="icon-listas"></i>Listas</a>
    <?php } ?>
    <?php if ($login->can['seeEvents']) { ?>
        <a id="sec_eventos" href="/eventos"><i class="icon-calendar"></i>Eventos</a>
    <?php } ?>
    </section>

<?php if ($login->can['seeBaul']) { ?>
    <section>
        <header>Baúl de contenidos</header>
        <?php if($_PREFS['media']['pendientes']) { ?>
        <a id="sec_pendientes" href="/contenidos/pendientes"><i class="icon-pendientes"></i>Pendientes</a>
        <?php } ?>
        <a id="sec_actuales" href="/contenidos/actuales"><i class="icon-actuales"></i>Actuales</a>
        <a id="sec_futuros" href="/contenidos/futuros"><i class="icon-clock"></i>Futuros</a>
        <a id="sec_caducados" href="/contenidos/caducados"><i class="icon-noCalendar"></i>Caducados</a>
    </section>
<?php } ?>

    <?php if ($login->can['seeSupport']) { ?>
	<section>
        <header>Comunicación</header>
        <a id="sec_soporte" href="/soporte"><i class="icon-encargos"></i>Portal de soporte</a>
    </section>
	<?php } ?>

	<?php if ($login->can['seeMusic']) { ?>
    <section>
        <header>Hilo musical</header>
        <a id="sec_canales" href="/canales"><i class="icon-canales"></i>Canales</a>
        <a id="sec_canciones" href="/canciones"><i class="icon-canciones"></i>Canciones</a>
    </section>
	<?php } ?>

    <section>
        <header>Sesión</header>
        <a id="sec_ayuda" href="/ayuda"><i class="icon-ayuda"></i>Ayuda</a>
        <a id="sec_perfil" href="/perfil"><i class="icon-perfil"></i>Mi perfil</a>
        <a id="cerrarSesion"><i class="icon-salir"></i>Cerrar sesión</a>
    </section>

    <?php if ($login->isAdmin) { ?>
    <section>
        <header>Administración</header>
        <a id="sec_config" href="/config"><i class="icon-config"></i>Configuración</a>
        <a id="sec_registros" href="/registros"><i class="icon-config"></i>Registros</a>
    </section>
    <?php } ?>
</nav>