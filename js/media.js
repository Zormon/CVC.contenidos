import {toolTip, fetchPost, mediaInfo, modalBox, previewMedia, selectModal, toggleRowActions, contentBox, sortableList, sortableListCatalog, $, $$, $$$, modalConfirm, sortJson} from '/js/exports.js?3'

class MEDIA {
    constructor (json, status, searchElement, orderElement, deviceElement, audioElement) { 
        this.json = json 
        this.status = status 
        this.search = searchElement
        this.order = orderElement
        this.device = deviceElement
        this.audio = audioElement
    }

    details (id) {
        fetchPost('/api/media', {mode: 'details', id:id}).then(resp => resp.json()).then( (data)=> {
            let html = Mustache.render( $('modalMediaDetails').innerHTML, data )
            modalBox (html, [ {text:'Cerrar'} ])    
        })
    }

    enabled (id, enable) {
        fetchPost('/api/media', {mode: enable?'enable':'disable', id:id}).then(resp => resp.json()).then( (data)=> {
            if (data.status == 'ok') { this.refresh() }
            else { console.error(data) }
        })
    }

    send (add=false) {
        let contentData = new FormData( $('datosContenido') )
        let contenido = {
            name: contentData.get('name'),
            dateFrom: contentData.get('fDateFrom'),
            dateTo: contentData.get('fDateTo'),
            volume: contentData.get('volume'),
            duration: contentData.get('duration'),
            transition: contentData.get('transition'),
            progress: true
        }
        let id
        if (add) { 
            id = Date.now()
            contenido.id = id
            contentData.append('mode','add')
         } else {
            id = contentData.get('id')
            $(`c${id}`).remove()
            contentData.append('mode','edit')
         }

        let xhr = new XMLHttpRequest(); xhr.open('POST', '/api/media')
        xhr.upload.onloadstart = () => {
            let li = document.createElement('li'); li.id = `up${id}`
            
            li.innerHTML = Mustache.render( $('rowMedia').innerHTML, contenido )
            $('listaUploads').appendChild(li)
        }
        xhr.upload.onprogress = (e) => {
            if (e.lengthComputable) { 
                let per = parseInt((e.loaded / e.total) * 100 )
                $$(`#up${id} .percent`).style = `width: ${per}%`
             }
        }
        xhr.onreadystatechange = () => {
            if (xhr.readyState === XMLHttpRequest.DONE ) {
                console.log( JSON.parse(xhr.response))
                try {
                    let json = JSON.parse(xhr.response)
                    if (json.status == 'ok') { 
                        $(`up${id}`).remove()
                        this.refresh() 
                    } else { alert( json.error ); location.reload() }
                } catch (e) { alert(xhr.response); location.reload() }
                
            }
        }
        xhr.send(contentData)

        return true
    }

    delete (id) {
        modalConfirm(`¿Borrar el contenido ${this.json[id].name}?`, ()=> {
            fetchPost('/api/media', {mode:'delete', ids:id}).then(resp => resp.json()).then( (data)=> {
                if (data.status == 'ok') { delete this.json[id]; this.printList() }
                else { console.error(data) }
            })
        })
    }

    deleteAll () {
        modalConfirm('¿Borrar todos los contenidos?', ()=> {
            let ids = []
            Object.values(this.json).forEach(el => { ids.push( el.id ) })

            fetchPost('/api/media', {mode:'delete', ids:ids.join(',')}).then(resp => resp.json()).then( (data)=> {
                if (data.status == 'ok') { this.json = []; this.printList() }
                else { console.error(data) }
            })
        })
    }

    modal (id) {
        if (!id) {
            let date = new Date().toISOString()
            let date2 = new Date()
            date2.setDate(date2.getDate() + parseInt(GLOBAL.config.media.defaults.daysToEndDate))
            date2 = date2.toISOString()
            let html = Mustache.render( $('modalEditMedia').innerHTML, {volume: GLOBAL.config.media.defaults.volume, duration: GLOBAL.config.media.defaults.duration,
                dateFrom: `${date.substring(0,4)}-${date.substring(5,7)}-${date.substring(8,10)}`,
                dateTo: `${date2.substring(0,4)}-${date2.substring(5,7)}-${date2.substring(8,10)}` })
            modalBox (html, [ {text:'Cancelar'}, {text:'Añadir', action:()=> { return this.send(true) }} ])
        } else {
            let cont = this.json[id]
            let html = Mustache.render( $('modalEditMedia').innerHTML, cont )
            modalBox (html, [ {text:'Cancelar'}, {text:'Editar', action:()=> { return this.send() }} ])
            cont.devices.forEach( id=> { try {  $('eq'+id).selected = true }catch(e){}} )
            $('categoria').value = cont.categoria
            $('transition').value = cont.transition
        }
        
        $('categoria').selectModal = new selectModal( 'categoria', 'Categoria', 'list' )
        $('transition').selectModal = new selectModal( 'transition', 'Efecto de transición', 'list' )
        $('devices').selectModal = new selectModal( 'devices', 'Equipos', 'grid', true, GLOBAL.groups)
        try { $('addToPlaylist').selectModal = new selectModal( 'addToPlaylist', 'Añadir a lista', 'list' )} catch (e){}

        $('file').onchange = (e)=> {
            mediaInfo(e.currentTarget.files[0]).then(info => { $('duration').value = info.duration })
            $('name').value = $('file').files[0].name.slice(0, -4).replace(/[^a-zA-Z0-9_\-]/g, "").replace('_',' ')
        }
    }
/*
    dragContainer( el ) {
        el.ondrop = (e)=> {
            e.preventDefault()
            
            e.currentTarget.classList.remove('dragHover')
            e.currentTarget.classList.remove('dragInvalid')
        
            this.modal(false)
        
            $('file').files = e.dataTransfer.files
            $('file').dispatchEvent( new Event( 'change', {'bubbles':true} ) )
        }
        
        el.ondragover = (e)=> { 
            e.preventDefault()
            if (e.currentTarget.classList.contains('dragInvalid')) {
                e.dataTransfer.dropEffect = 'none'
            } else {
                e.dataTransfer.dropEffect = 'copy'
            }
        }
        
        el.ondragenter = (e)=>{ 
            if ( e.dataTransfer.items.length == 1 && (e.dataTransfer.items[0].type == 'video/mp4' || e.dataTransfer.items[0].type == 'image/jpeg') ) {
                e.currentTarget.classList.add('dragHover')
            } else {
                e.currentTarget.classList.add('dragInvalid')
            }
         }
        
        el.ondragleave = (e)=> {
            e.currentTarget.classList.remove('dragHover')
            e.currentTarget.classList.remove('dragInvalid')
        }
    }
*/

    printList () {
        const textFilter = this.search.value.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
        const audio = this.audio.checked
        const equipo = parseInt( this.device.value )

        // Limpia la lista
        const elList = $('mediaList')
        while (elList.firstChild) { elList.removeChild(elList.lastChild) }

        let filtered = []
        for ( let el of Object.values(this.json) ) {
            const name = el.name.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            const tags = el.tags.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            if ( (textFilter == '' || name.search( textFilter ) != -1 || tags.split(',').indexOf( textFilter ) != -1 ) && 
            (equipo == 0 || el.devices.includes(equipo)) &&
            (!audio || (el.volume > audio) ) ) {
                filtered.push( el )
            }
        }
        sortJson(filtered, this.order.value)
        
        if (filtered.length == 0) {
            contentBox( $('main_media'), 'info', 'No hay contenidos' )
        } else {
            contentBox( $('main_media'), 'info', false )
            for (let contenido of filtered) {
                const id = contenido.id
                let li = document.createElement('li'); li.id = `c${id}`
                li.style = `border-color: ${contenido.color};`
                if (contenido.volume != 0) { contenido.audio = true }
                if (!contenido.playlists && !contenido.events) { li.classList.add('noLists'); contenido.noLists = true }
                li.innerHTML = Mustache.render( $('rowMedia').innerHTML, contenido )
                li.querySelectorAll('.tooltip').forEach(el => { el.tooltip = new toolTip(el) })
                li.querySelector('.trigger-actions').onmouseenter = ev => { toggleRowActions(ev) }

                /*---------- Botones de acciones  ----------*/
                li.querySelector('.previewMedia').onclick = (e)=> { // click on thumb
                    const mediaInfo = JSON.parse(this.json[id].mediainfo)
                    const ratio = mediaInfo.width / mediaInfo.height
                    previewMedia(GLOBAL.config.path.media + this.json[id].file, (ratio > 1.6 && ratio < 1.9))
                }
                li.querySelector('.cDetails').onclick = (e)=>   { this.details(id) }
                try {li.querySelector('.cEnable').onclick = (e)=>  { this.enabled(id, true) }} catch (e){}
                try {li.querySelector('.cDisable').onclick = (e)=> { this.enabled(id, false) }} catch (e){}
                if (LOGIN.can.edit.media) {
                    li.querySelector('.cEdit').onclick = ()=>   { this.modal(id) }
                    li.querySelector('.cDelete').onclick = ()=> { this.delete(id) }
                }

                $('mediaList').appendChild(li)
            }
        }
    }

    refresh () {
        return new Promise((resolve)=> {
            fetchPost('/api/media', {mode:'list', status: this.status}).then(resp => resp.json()).then( (data)=> {
                this.json = data
                this.printList()
                resolve()
            })
        })
    }
}



class MEDIAPLAYLISTS {
    constructor (json, conts, searchElement,) {
        this.json = json
        this.catalog = conts
        this.search = searchElement

        this.list
    }

    send () {
        if ( !$('mediaPlaylistData').reportValidity() ) {
            return false
        } else {
            const playlistForm = new FormData($('mediaPlaylistData'))
            playlistForm.append('media', this.list.getData('id'))
            playlistForm.append('mode', 'savePlaylist')
            
            fetch('/api/media', {method: 'POST', body: playlistForm}).then(resp => resp.json()).then( (data)=> {
                if ( data.status == 'ok' )    { this.refresh() }
                else                          { alert(`ERROR: ${data.error}`) }
            })
    
            return true
        }
    }

    delete (id) {
        modalConfirm(`¿Borrar la lista ${this.json[id].name}}?`, ()=> {
            fetchPost('/api/media', {mode:'deletePlaylist', id:id}).then(resp => resp.json()).then( (data)=> {
                if (data.status == 'ok') { delete this.json[id]; this.printList() }
                else { console.error(data) }
            })
        })
    }

    modal (id) {
        var mediaList = []

        if (!id) {
            let html = Mustache.render( $('modalEditMediaPlaylist').innerHTML, {} )
            modalBox (html, [ {text:'Cancelar'}, {text:'Añadir', action:()=> { return this.send() }} ], 90)
        } else {
            let playlist = this.json[id]
            let html = Mustache.render( $('modalEditMediaPlaylist').innerHTML, playlist )
            modalBox (html, [ {text:'Cancelar'}, {text:'Editar', action:()=> { return this.send() }} ], 90)
            playlist.devices.forEach(equipo => { try{$(`eq${equipo}`).selected = true}catch(e){} })
            
            playlist.media.forEach(el => {
                let cont = this.catalog[el]
                if (typeof cont !== 'undefined') {
                    mediaList.push( {content: cont.name, color: cont.color, img: cont.thumb, id: cont.id} )
                }
            })

        }
        this.list = new sortableList( 'mediaList', mediaList)


        var mediaCatalog = []
        Object.values(this.catalog).forEach(cont => {
            mediaCatalog.push({content: cont.name, color: cont.color, img: cont.thumb, id: cont.id})
        })

        var catalog = new sortableListCatalog( 'mediaCatalog', mediaCatalog)
    
        $('devices').selectModal = new selectModal( 'devices', 'Equipos', 'grid', true, GLOBAL.groups )
    }

    printList () {
        const textFilter = this.search.value.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
        // Limpia la lista
        const elList = $('ul_mediaPlaylists')
        while (elList.firstChild) { elList.removeChild(elList.lastChild) }
    
        let filtered = []
        Object.values(this.json).forEach(el => {
            const name = el.name.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            if ( textFilter == '' || name.search( textFilter ) != -1 ) {
                filtered.push(el)
            }
        })
    
        if (filtered.length == 0) {
            contentBox( $('main_mediaPlaylists'), 'info', 'No hay listas' )
        } else {
            contentBox( $('main_mediaPlaylists'), 'info', false )
            for (let playlist of filtered) {
                let li = document.createElement('li'); li.id = `e${playlist.id}`
                li.dataset.id = playlist.id
                playlist.ndevices = playlist.devices.length
                li.innerHTML = Mustache.render( $('rowMediaPlaylist').innerHTML, playlist )
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
            fetchPost('/api/media', {mode:'listPlaylists'}).then(resp => resp.json()).then( (data)=> {
                this.json = data
                this.printList()
                resolve()
            })
        })
    }

}



class PARRILLA {
    constructor () {
        this.json
        this.SIZEMULT = 15
    }

    printList () {
        // Limpia la lista
        const elList = $('listadoParrilla')
        while (elList.firstChild) { elList.removeChild(elList.lastChild) }

        let mins = Math.floor(this.json.duration / 60)
        let secs = (this.json.duration - mins * 60).toString().padStart(2,'0')
        $('resumen').innerHTML = `<strong>${mins}:${secs}</strong> (${this.json.media.length} contenidos, ${this.json.conAudio} con audio)`

        if (this.json.media.length == 0) {
            contentBox( $('parrilla'), 'info', 'Parrilla vacía' )
        } else {
            contentBox( $('parrilla'), 'info', false )
            for (let contenido of this.json.media) {
                if (contenido.volume != 0) { contenido.audio = true }
                let li = document.createElement('li')
                li.style.height = `${contenido.duration*this.SIZEMULT}px`
                if (contenido.categoria != '') { li.style.background = `linear-gradient(to left, ${contenido.categoria.color}50 60%, white)` }
                li.id = 'c' + contenido.id
                li.dataset.video = `/storage/media/${contenido.file}`
                li.dataset.duration = contenido.duration;
                li.innerHTML = Mustache.render( $('rowParrilla').innerHTML, contenido )
        
                $('listadoParrilla').appendChild(li)
            }
        
            $$$('#listadoParrilla .thumb').forEach(li => { li.onclick = (e)=> { previewMedia(e.currentTarget.parentElement.dataset.video) } })
        }
    }

    refresh (id) {
        return new Promise((resolve)=> {
            fetchPost('/api/media', {mode:'parrilla', equipo: id}).then(resp => resp.json()).then( (data)=> {
                this.json = data
                this.printList()
                resolve()
            })
        })
    }
}

export { MEDIA, MEDIAPLAYLISTS, PARRILLA }