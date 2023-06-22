import {$, selectModal, modalBox, contentBox, fetchPost, sortJson} from '/js/exports.js?3'

class GROUPS {
    constructor (json, searchElement, orderElement) {
        this.json = json
        this.search = searchElement
        this.order = orderElement
    }

    modal (id=false)  {
        if (!id) {
            let html = Mustache.render( $('modalGroup').innerHTML, {} )
            modalBox (html, [ {text:'Cancelar'}, {text:'Añadir', action:()=> { return this.send(true) } } ])
        } else {
            let group = this.json[id]
            let html = Mustache.render( $('modalGroup').innerHTML, group )
            modalBox (html, [ {text:'Cancelar'}, {text:'Editar', action:()=> { return this.send() }} ])
            group.devices.forEach(equipo => { $(`eq${equipo.id}`).selected = true })
        }

        $('devices').selectModal = new selectModal( 'devices', 'Equipos', 'grid', true, GLOBAL.groups )
    }

    send (add=false) {
        if ( !$('groupData').checkValidity() ) {
            $('groupData').reportValidity()
            return false
        } else {
            const groupForm = new FormData($('groupData'))
            if (add)    { groupForm.append('mode', 'add') }
            else        { groupForm.append('mode', 'edit') }
            
            fetch('/api/groups', {method: 'POST', body: groupForm}).then(resp => resp.json()).then( (data)=> {
                if ( data.status == 'ok' ) { this.refresh() }
                else { alert(`ERROR: ${data.error}`) }
            })

            return true
        }
    }

    delete (id) {
        if ( confirm(`¿Borrar grupo ${id}?`) ) {
            fetchPost('/api/groups', {mode:'delete', id:id}).then(resp => resp.json()).then( (data)=> {
                if (data.status == 'ok')   { $(`g${id}`).remove() }
                else                { alert(`ERROR: ${data.error}`) }
            })
        }
    }

    printList () {
        const textFilter = this.search.value.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
        // Limpia la lista
        const elList = $('groupsList')
        while (elList.firstChild) { elList.removeChild(elList.lastChild) }

        let filtered = []
        for ( let el of Object.values(this.json) ) {
            const name = el.name.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            if ( textFilter == '' || name.search( textFilter ) != -1 ) {
                filtered.push(el)
            }
        }
        sortJson(filtered, this.order.value)
        
        if (filtered.length == 0) {
            contentBox( $('groupsList'), 'info', 'No hay grupos' )
        } else {
            contentBox( $('groupsList'), 'info', false )
            for (let group of filtered) {
                let el = document.createElement('div'); el.id = `g${group.id}`
                el.dataset.id = group.id; el.className = 'card'
                el.style.backgroundImage = `url(/img/groups/${group.id}.webp)`
                if (group.ndevices != 1) { group.pdevices = true }
                el.innerHTML = Mustache.render( $('cardGroup').innerHTML, group )
                el.querySelector('.title').onclick = (e)=> { e.currentTarget.parentElement.parentElement.classList.toggle('expanded') }

                el.querySelector('.gDetails').onclick = ()=>   { this.details(group.id) }
                if (LOGIN.can.edit.groups) {
                    el.querySelector('.gEdit').onclick = ()=>   { this.modal(group.id) }
                    el.querySelector('.gDelete').onclick = ()=> { this.delete(group.id) }
                }
                $('groupsList').appendChild(el)
            }
        }
    }

    refresh () {
        return new Promise((resolve)=> {
            fetchPost('/api/groups', {mode:'list'}).then(resp => resp.json()).then( (data)=> {
                this.json = data
                this.printList()
                resolve()
            })
        })
    }
}

export default GROUPS