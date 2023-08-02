<header class="actionBar"></header>

<main class="seccion">
    <table>
    <tr><th>Version</th><th>Target</th><th>Equipos</th><th>Notas</th></tr>

    <?php
    global $mysql;

    $updates = $mysql->consulta( "SELECT * FROM updates ORDER BY version" );

    foreach ($updates as $upd) {
        echo '<tr><td>'.$upd['version'].'</td><td>'.$upd['target'].'</td><td>'.$upd['devices'].'</td><td>'.$upd['notes'].'</td></tr>';
    }
    ?>
    </table>
</main>

