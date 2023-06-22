<?php
?>

<header>
    <!--<a data-target="slide-out" class="sidenav-trigger"><i class="material-icons">menu</i></a>-->
    <nav>
        <div class="nav-wrapper">
        <ul>
            <li class="right"><a id="refresh"><i class="material-icons">refresh</i></a></li>
        </ul>
            
        </div>
    </nav>
</header>

<main id="perfil">
    <section id="perfilUsuario">
    <h1>Perfil de usuario</h1>
        <span><?=$login->name?></span>
    </section>
</main>

<script src="/js/perfil.js" defer></script>