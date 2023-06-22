import {toolTip, fetchPost, sortJson, modalBox, selectModal, modalConfirm, toggleRowActions, contentBox, $, $$, $$$} from '/js/exports.js?3'

class DEVICES {
    constructor (json, searchElement, orderElement) { 
        this.json = json 
        this.search = searchElement
        this.order = orderElement
    }

    logs(id) {
        alert(`Historial de ${id}`)
    }

    details (id) {
        fetchPost('/api/devices', {mode: 'details', id:id}).then(resp => resp.json()).then( (data)=> {
            let html = Mustache.render( $('modalDeviceDetails').innerHTML, data )
            modalBox (html, [ {text:'Cerrar'} ])    
        })
    }

    modal (id=false) {
        if (!id) {
            let html = Mustache.render( $('modalEquipo').innerHTML, {} )
            modalBox (html, [ {text:'Cancelar'}, {text:'Añadir', action:()=> { return this.send(true) }} ])
        } else {
            let equipo = this.json[id]
            let html = Mustache.render( $('modalEquipo').innerHTML, equipo )
            modalBox (html, [ {text:'Cancelar'}, {text:'Editar', action:()=> { return this.send() }} ])
            $('shop').value = equipo.shop
            $('tipo').value = equipo.tipo
            $('hFormato').value = equipo.power.format
            $('sleepType').value = equipo.power.mode
            $('exDays').value = equipo.power.ex
        }
    
        $('shop').selectModal = new selectModal( 'shop', 'Tienda', 'grid', true )
        $('tipo').selectModal = new selectModal( 'tipo', 'Tipo', 'list' )
        $('hFormato').selectModal = new selectModal( 'hFormato', 'Formato', 'list' )
        $('sleepType').selectModal = new selectModal( 'sleepType', 'Tipo de sleep', 'list' )
    
        $('hFormato').onchange = (e)=> {
            $$$('.horarios > div').forEach(el => { el.classList.add('hidden') })
            let values = e.currentTarget.value.split('+')
            values.forEach(e => { $('h'+e).classList.remove('hidden') })
        }

        $('hFormato').dispatchEvent(new Event( 'change', {'bubbles':true} ))
    }

    send (add=false) {
        if ( !$('datosEquipo').reportValidity() ) {
            return false
        } else {
            const equipoForm = new FormData($('datosEquipo'))
            if (add)    { equipoForm.append('mode', 'add') }
            else        { equipoForm.append('mode', 'edit') }
            
            fetch('/api/devices', {method: 'POST', body: equipoForm}).then(resp => resp.json()).then( (data)=> {
                if ( data.status == 'ok' )    { this.refresh() }
                else                          { alert(`ERROR: ${data.error}`) }
            })
    
            return true
        }
    }

    delete (id) {
        modalConfirm(`¿Borrar equipo ${id}?`, ()=> {
            fetchPost('/api/devices', {mode:'delete', id:id}).then(resp => resp.json()).then( (data)=> {
                if (data.status == 'ok')     { $(`e${id}`).remove() }
                else                         { alert(`ERROR: ${data.error}`) }
            })
        })
    }

    printList () {
        const textFilter = this.search.value.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
        // Limpia la lista
        const elList = $('ul_devices')
        while (elList.firstChild) { elList.removeChild(elList.lastChild) }
    
        let filtered = []
        for ( let el of Object.values(this.json) ) {
            const name = el.name.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            if ( textFilter == '' || name.search( textFilter ) != -1 ) {
                filtered.push(el)
            }
        }
        sortJson(filtered, this.order.value, this.order.value=='id')
    
        if (filtered.length == 0) {
            contentBox( $('main_devices'), 'info', 'No hay equipos' )
        } else {
            contentBox( $('main_devices'), 'info', false )
            for (let equipo of filtered) {
                let li = document.createElement('li'); li.id = `e${equipo.id}`
                li.dataset.id = equipo.id
                li.innerHTML = Mustache.render( $('rowEquipo').innerHTML, equipo )
                li.querySelectorAll('.tooltip').forEach(el => { el.tooltip = new toolTip(el) })
                li.querySelector('.trigger-actions').onmouseenter = ev => { toggleRowActions(ev) }

                /*---------- Botones de acciones  ----------*/
                li.querySelector('.eParrilla').onclick = ()=>   { localStorage.parrillaDevice = equipo.id; window.open(`/parrilla`) }
                li.querySelector('.bDetails').onclick = ()=>    { this.details(equipo.id) }
                li.querySelector('.eLogs').onclick = ()=>       { this.logs(equipo.id) }
                if (LOGIN.can.edit.devices) {
                    li.querySelector('.bEdit').onclick = ()=>   { this.modal(equipo.id) }
                    li.querySelector('.bDelete').onclick = ()=> { this.delete(equipo.id) }
                }

                elList.appendChild(li)
            }
        }
    }

    refresh () {
        return new Promise((resolve)=> {
            fetchPost('/api/devices', {mode:'list'}).then(resp => resp.json()).then( (data)=> {
                this.json = data
                this.printList()
                resolve()
            })
        })
    }
}

export default DEVICES