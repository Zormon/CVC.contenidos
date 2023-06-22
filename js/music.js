import {fetchPost, selectableList, sortableList, sortableListCatalog, selectModal, modalBox, contentBox, $, $$, $$$, toolTip, toggleRowActions, uploadQueue, modalConfirm, sortJson} from '/js/exports.js?3'

var audio = new Audio()
audio.oncanplay = () => { audio.play() }
async function playMusic(song) {
    if (song) {
        audio.pause()
        audio.src = `/storage/music/${song}`
    } else {
        audio.pause()
    }
}

/*=============================================
=            Canciones            =
=============================================*/

class SONGS {
    constructor (json, searchElement, orderElement, channelElement) {
        this.json = json
        this.search = searchElement
        this.order = orderElement
        this.channel = channelElement

        this.selectableList = null
        // Eventos
        this.onList = ()=>{}; this.onRefresh = ()=>{}
        this.onAdd = ()=>{}; this.onModal = ()=>{}
    }

    printList () {
        playMusic(false)
        // Limpia la lista
        const elList = $('songList')
        while (elList.firstChild) { elList.removeChild(elList.lastChild) }
        
        let textFilter = this.search.value.toUpperCase()
        let filtered = []
        const channel = parseInt( this.channel.value )
        Object.values(this.json).forEach(el => {
            if ((textFilter == '' || el.name.toUpperCase().search( textFilter ) != -1) && 
                (channel == 0 || el.canales.includes(channel))) {
                    filtered.push(el)
            }
        })

        sortJson(filtered, this.order.value)

        if (filtered.length == 0) {
            contentBox( $('main_music'), 'info', 'No hay canciones' )
        } else {
            contentBox( $('main_music'), 'info', false )
            for (let cancion of filtered) {
                let el = document.createElement('li')
                el.dataset.id = cancion.id;
                el.dataset.file = cancion.file;
                el.innerHTML = Mustache.render( $('rowCancion').innerHTML, cancion )
                if (cancion.canales.length == 0 ) { el.classList.add('noLists') } //No esta asignada a ninguno canal
                $('songList').appendChild(el)
            }
            
            $$$('button.play').forEach(btn => { btn.onclick = (e)=> {
                if ( e.currentTarget.childNodes[0].textContent == 'play_arrow' ) {
                    playMusic(e.target.parentElement.parentElement.dataset.file) 
                    $$$('button.play i').forEach( i => { i.textContent = 'play_arrow' } )
                    e.currentTarget.childNodes[0].textContent = 'pause'
                } else {
                    playMusic(false)
                    e.currentTarget.childNodes[0].textContent = 'play_arrow'
                }
            }})
        
            let boxes = $$$('#songList input[type="checkbox"]')
            boxes.forEach(el => {
                el.onclick = (e)=> {
                    $$('#selectAll i').textContent = 'select_all'
                    SONGS.select(e.currentTarget.checked)
                }
            })
        }

        this.selectableList = new selectableList('songList')
        this.onList()
    }

    modal () {
        let html = Mustache.render( $('modalAddCanciones').innerHTML, {} )
        modalBox (html, [ {text:'Cancelar'}, {text:'Añadir', action:()=> { return this.upload() }} ])

        $('addToChannel').selectModal = new selectModal( 'addToChannel', 'Añadir a canal', 'list' )

        this.onModal()
    }

    upload () {
        let uploadData = [];

        [...$('canciones').files].forEach((file, i) => {
            let data = new FormData()
            data.set('mode', 'addSong')
            data.set('addToChannel', $('addToChannel').value)
            uploadData.push( {appendData:data, file:file} ) // A job
        })

        uploadQueue(uploadData, '/api/music', {maxUploads:3})
            .then(()=> { this.refresh() })
            .catch((err)=> { alert(`ERROR: ${err}`) })
        this.onAdd()
        return true
    }

    delete (ids) {
        modalConfirm(`¿Borrar las ${ids.length} canciones seleccionadas?`, ()=> {
            fetchPost('/api/music', {mode:'deleteSongs', ids:ids}).then(r=>r.json()).then( (data)=> {
                if (data.status == 'ok') { this.refresh() }
                else { alert(`ERROR: ${data.error}`) }
            })
        })
    }

    refresh () {
        return new Promise((resolve)=> {
            SONGS.selected = 0
            fetchPost('/api/music', {mode:'listSong'}).then(r=>r.json()).then( (data)=> {
                this.json = data
                this.printList()
                this.onRefresh()
                resolve()
            })
        })
        
    }
}


/*=============================================
=            Listas            =
=============================================*/
class MUSICPLAYLISTS {
    constructor (json, catalog, searchElement) {
        this.json = json
        this.catalog = catalog
        this.search = searchElement

        this.printList
    }

    details (id) {
        let pl = this.json[id]
        let songs = []

        pl.songs.forEach(id => {
            const sg = this.catalog[id]
            if (!!sg) { songs.push( {name:sg.name, file:sg.file, timeFrom: sg.timeFrom, timeTo:sg.timeTo} ) }
        })

        let html = Mustache.render( $('modalMusicPlaylistDetails').innerHTML, {name:pl.name, songs:songs} )
        modalBox (html, [ {text:'Cerrar'} ])
    }

    send (add=false) {
        if ( !$('musicPlaylistData').reportValidity() ) {
            return false
        } else {
            const playlistForm = new FormData($('musicPlaylistData'))
            playlistForm.append('songs', this.printList.getData('id'))
            playlistForm.append('mode', 'savePlaylist') 
            
            fetch('/api/music', {method: 'POST', body: playlistForm}).then(resp => resp.json()).then( (data)=> {
                if ( data.status == 'ok' )    { this.refresh() }
                else                          { alert(`ERROR: ${data.error}`) }
            })
    
            return true
        }
    }

    delete (id) {
        modalConfirm(`¿Borrar la lista ${this.json[id].name}?`, ()=> {
            fetchPost('/api/music', {mode:'deletePlaylist', id:id}).then(resp => resp.json()).then( (data)=> {
                if (data.status == 'ok') { delete this.json[id]; this.printList() }
                else { console.error(data) }
            })
        })
    }

    modal (id) {
        var musicList = []

        if (!id) {
            let html = Mustache.render( $('modalMusicPlaylist').innerHTML, {} )
            modalBox (html, [ {text:'Cancelar'}, {text:'Añadir', action:()=> { return this.send(true) }} ], 90)
        } else {
            let playlist = this.json[id]
            let html = Mustache.render( $('modalMusicPlaylist').innerHTML, playlist )
            modalBox (html, [ {text:'Cancelar'}, {text:'Editar', action:()=> { return this.send() }} ], 90)
            
            playlist.songs.forEach(el => {
                let song = this.catalog[el]
                if (!!song) { musicList.push( {content: song.name, id: song.id} ) }
            })

        }
        this.printList = new sortableList( 'musicList', musicList)

        
        var musicCatalog = []
        Object.values(this.catalog).forEach(song => {
            musicCatalog.push({content: song.name, id: song.id})
        })

        var catalog = new sortableListCatalog( 'musicCatalog', musicCatalog)
    }

    printList () {
        const textFilter = this.search.value.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
        // Limpia la lista
        const elList = $('ul_musicPlaylists')
        while (elList.firstChild) { elList.removeChild(elList.lastChild) }
    
        let filtered = []
        Object.values(this.json).forEach(el => {
            const name = el.name.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            if ( textFilter == '' || name.search( textFilter ) != -1 ) {
                filtered.push(el)
            }
        })
    
        if (filtered.length == 0) {
            contentBox( $('main_musicPlaylists'), 'info', 'No hay listas' )
        } else {
            contentBox( $('main_musicPlaylists'), 'info', false )
            for (let playlist of filtered) {
                let li = document.createElement('li'); li.id = `e${playlist.id}`
                li.dataset.id = playlist.id
                li.innerHTML = Mustache.render( $('rowMusicPlaylist').innerHTML, playlist )
                li.querySelectorAll('.tooltip').forEach(el => { el.tooltip = new toolTip(el) })
                li.querySelector('.trigger-actions').onmouseenter = ev => { toggleRowActions(ev) }

                /*---------- Botones de acciones  ----------*/
                li.querySelector('.bDetails').onclick = ()=>    { this.details(playlist.id) }
                if (LOGIN.can.edit.media) {
                    li.querySelector('.bEdit').onclick = ()=>   { this.modal(playlist.id) }
                    li.querySelector('.bDelete').onclick = ()=> { this.delete(playlist.id) }
                }

                elList.appendChild(li)
            }
        }
    }

    refresh () {
        return new Promise((resolve)=> {
            fetchPost('/api/music', {mode:'listPlaylists'}).then(resp => resp.json()).then( (data)=> {
                this.json = data
                this.printList()
                resolve()
            })
        })
    }

}

/*=====  End of Funciones  ======*/

/*=============================================
=            Drag and Drop Music            =
=============================================*/
/*
$('main_music').ondrop = (e)=> {
    e.preventDefault()
    
    e.currentTarget.classList.remove('dragHover')
    e.currentTarget.classList.remove('dragInvalid')

    SONGS.modal(false)

    $('canciones').files = e.dataTransfer.files
    $('canciones').dispatchEvent( new Event( 'change', {'bubbles':true} ) )
}

$('main_music').ondragover = (e)=> { 
    e.preventDefault()
    if (e.currentTarget.classList.contains('dragInvalid')) {
        e.dataTransfer.dropEffect = 'none'
    } else {
        e.dataTransfer.dropEffect = 'copy'
    }
}

$('main_music').ondragenter = (e)=>{ 
    if ( e.dataTransfer.items[0].type == 'audio/ogg' ) { //TODO: Comprobar toda la lista
        e.currentTarget.classList.add('dragHover')
    } else {
        e.currentTarget.classList.add('dragInvalid')
    }
 }

$('main_music').ondragleave = (e)=> {
    e.currentTarget.classList.remove('dragHover')
    e.currentTarget.classList.remove('dragInvalid')
}
*/
/*=====  End of Drag and Drop Music  ======*/


export { SONGS, MUSICPLAYLISTS }