<script type="module">
    import {$, $$, $$$, toolTip, logout} from '/js/exports.js?3'

    let subUrl = window.location.pathname
    if (subUrl == '/') { subUrl = '/home'; }
    $('mainMenu').querySelector(`[href="${subUrl}"]`).className = 'current'
    const secName = subUrl.split('/').pop()
    document.title += ` (${secName}) `

    $('cerrarSesion').onclick = () => { 
        logout() 
    }

    $$$('.actionBar .tools button').forEach(el => { el.tooltip = new toolTip(el) })
    

    // Atajos de teclado
    const clickEvt = new Event( 'click', {'bubbles':true})
    window.onkeydown = (e)=> { 
        if (document.body.contains($('search'))) { // Cuadro busqueda (shift + f)
            if (e.keyCode == 27) { $('search').value = ''; $('search').dispatchEvent( new Event( 'keyup', {'bubbles':true}) ); $('search').blur() }
            else if (e.keyCode == 70 && e.altKey) { e.preventDefault(); $('search').focus() }
        }
        
        if (document.body.contains($('refresh'))) { // Boton recargar (shift + r)
            if (e.keyCode == 82 && e.altKey) { e.preventDefault(); $('refresh').dispatchEvent( clickEvt ) }
        }

        if (document.body.contains($('add'))) { // Boton añadir (shift + +)
            if (e.keyCode == 107 && e.altKey) { e.preventDefault(); $('add').dispatchEvent( clickEvt ) }
        }
    }
</script>

</body>
</html>