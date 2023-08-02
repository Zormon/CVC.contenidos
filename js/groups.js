import {$, selectModal, modalBox, contentBox, fetchPost, sortJson, toolTip, toggleRowActions, modalConfirm} from '/js/exports.js?3'

class GROUPS {
    constructor (json, searchElement, orderElement) {
        this.json = json
        this.search = searchElement
        this.order = orderElement
    }

    details(id) {
        let group = this.json[id]
        let html = Mustache.render( $('modalGroupDetails').innerHTML, group )
        modalBox (html, [ {text:'Cerrar'} ])
    }

    modal (id=false)  {
        if (!id) {
            let html = Mustache.render( $('modalGroup').innerHTML, {} )
            modalBox (html, [ {text:'Cancelar'}, {text:'Añadir', action:()=> { return this.send(true) } } ])
        } else {
            let group = this.json[id]
            let html = Mustache.render( $('modalGroup').innerHTML, group )
            modalBox (html, [ {text:'Cancelar'}, {text:'Editar', action:()=> { return this.send() }} ])
            $('color').value = group.color
            group.devices.forEach(equipo => { $(`eq${equipo.id}`).selected = true })
        }

        $('color').selectModal = new selectModal( 'color', 'Color', 'list', false )
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
        modalConfirm(`¿Borrar grupo <em>${this.json[id].name}</em> ?`, ()=> {
            fetchPost('/api/groups', {mode:'delete', id:id}).then(resp => resp.json()).then( (data)=> {
                if (data.status == 'ok')   { delete this.json[id]; this.printList() }
                else                { alert(`ERROR: ${data.error}`) }
            })
        })
    }

    printList () {
        const textFilter = this.search.value.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
        // Limpia la lista
        const elList = $('ul_groups')
        while (elList.firstChild) { elList.removeChild(elList.lastChild) }

        let filtered = []
        for ( let el of Object.values(this.json) ) {
            const name = el.name.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            if ( textFilter == '' || name.search( textFilter ) != -1 ) {
                filtered.push(el)
            }
        }
        const isNumeric = this.order.options[this.order.selectedIndex].dataset.numeric !== undefined
        sortJson(filtered, this.order.value, isNumeric)
        
        if (filtered.length == 0) {
            contentBox( elList, 'info', 'No hay grupos' )
        } else {
            contentBox( elList, 'info', false )
            for (let group of filtered) {
                let li = document.createElement('li'); li.id = `g${group.id}`
                li.dataset.id = group.id;
                if (group.ndevices != 1) { group.pdevices = true }
                li.innerHTML = Mustache.render( $('rowGroup').innerHTML, group )
                li.querySelectorAll('.tooltip').forEach(el => { el.tooltip = new toolTip(el) })
                li.querySelector('.trigger-actions').onmouseenter = ev => { toggleRowActions(ev) }

                li.querySelector('.gDetails').onclick = ()=>   { this.details(group.id) }

                if (LOGIN.can.edit.groups) {
                    li.querySelector('.gEdit').onclick = ()=>   { this.modal(group.id) }
                    li.querySelector('.gDelete').onclick = ()=> { this.delete(group.id) }
                }
                elList.appendChild(li)
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