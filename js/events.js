import {toolTip, fetchPost, modalBox, selectModal, modalConfirm, toggleRowActions, contentBox, $, $$, $$$, sortJson} from '/js/exports.js?3'

class EVENTS {
    constructor (json, searchElement, orderElement) { 
        this.json = json 
        this.search = searchElement
        this.order = orderElement
        
        this.event
    }

    logs(id) {
        alert(`Historial de ${id}`)
    }

    details(id) {
        alert(`Estado de ${id}`)
    }

    modal(id=false) {
        if (!id) {
            let html = Mustache.render( $('modalEvent').innerHTML, {} )
            modalBox (html, [ {text:'Cancelar'}, {text:'Añadir', action:()=> { return this.send(true) }} ])
            $('week1').checked = $('week2').checked = $('week3').checked = $('week4').checked = $('week5').checked = $('week6').checked = true
        } else {
            this.event = this.json[id]
            this.event.week1 = this.event.weekdays & 1<<0? 'checked':'';
            this.event.week2 = this.event.weekdays & 1<<1? 'checked':'';
            this.event.week3 = this.event.weekdays & 1<<2? 'checked':'';
            this.event.week4 = this.event.weekdays & 1<<3? 'checked':'';
            this.event.week5 = this.event.weekdays & 1<<4? 'checked':'';
            this.event.week6 = this.event.weekdays & 1<<5? 'checked':'';
            this.event.week7 = this.event.weekdays & 1<<6? 'checked':'';

            let html = Mustache.render( $('modalEvent').innerHTML, this.event )
            modalBox (html, [ {text:'Cancelar'}, {text:'Editar', action:()=> { return this.send() }} ])
            this.event.devices.forEach( id=> { try {  $('eq'+id).selected = true }catch(e){}} )

            $('media').value = this.event.media.id
        }
    
        $('devices').selectModal = new selectModal( 'devices', 'Equipos', 'grid', true, GLOBAL.groups )
        $('media').selectModal = new selectModal( 'media', 'Contenido', 'grid', true)
    }

    send (add=false) {
        if ( !$('datosEvent').reportValidity() ) {
            return false
        } else {
            const eventForm = new FormData($('datosEvent'))
            if (add)    { eventForm.append('mode', 'add') }
            else        { eventForm.append('mode', 'edit') }

            let weekdays = 0
            weekdays += eventForm.get('week1')!==null && 1<<0; eventForm.delete('week1')
            weekdays += eventForm.get('week2')!==null && 1<<1; eventForm.delete('week2')
            weekdays += eventForm.get('week3')!==null && 1<<2; eventForm.delete('week3')
            weekdays += eventForm.get('week4')!==null && 1<<3; eventForm.delete('week4')
            weekdays += eventForm.get('week5')!==null && 1<<4; eventForm.delete('week5')
            weekdays += eventForm.get('week6')!==null && 1<<5; eventForm.delete('week6')
            weekdays += eventForm.get('week7')!==null && 1<<6; eventForm.delete('week7')
            
            eventForm.append('weekdays', weekdays)
            
            fetch('/api/events', {method: 'POST', body: eventForm}).then(resp => resp.json()).then( (data)=> {
                if ( data.status == 'ok' )    { this.refresh() }
                else                          { alert(`ERROR: ${data.error}`) }
            })
    
            return true
        }
    }

    delete (id) {
        modalConfirm(`¿Borrar evento ${this.json[id].name}?`, ()=> {
            fetchPost('/api/events', {mode:'delete', id:id}).then(resp => resp.json()).then( (data)=> {
                if (data.status == 'ok')     { delete this.json[id]; this.printList() }
                else                         { alert(`ERROR: ${data.error}`) }
            })
        })
    }

    printList () {
        const textFilter = this.search.value.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
        // Limpia la lista
        const elList = $('ul_events')
        while (elList.firstChild) { elList.removeChild(elList.lastChild) }
    
        let filtered = []
        for (let el of Object.values(this.json)) {
            const name = el.name.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            if ( textFilter == '' || name.search( textFilter ) != -1 ) {
                filtered.push(el)
            }
        }
        sortJson(filtered, this.order.value)
    
        if (filtered.length == 0) {
            contentBox( $('main_events'), 'info', 'No hay eventos' )
        } else {
            contentBox( $('main_events'), 'info', false )
            for (let event of filtered) {
                let li = document.createElement('li'); li.id = `e${event.id}`
                li.dataset.id = event.id
                li.innerHTML = Mustache.render( $('rowEvent').innerHTML, event )
                li.querySelectorAll('.tooltip').forEach(el => { el.tooltip = new toolTip(el) })
                li.querySelector('.trigger-actions').onmouseenter = ev => { toggleRowActions(ev) }

                /*---------- Botones de acciones  ----------*/
                li.querySelector('.bDetails').onclick = ()=>    { this.details(event.id) }
                li.querySelector('.bLogs').onclick = ()=>       { this.logs(event.id) }
                if (LOGIN.can.edit.events) {
                    li.querySelector('.bEdit').onclick = ()=>   { this.modal(event.id) }
                    li.querySelector('.bDelete').onclick = ()=> { this.delete(event.id) }
                }

                elList.appendChild(li)
            }
        }
    }

    refresh () {
        return new Promise((resolve)=> {
            fetchPost('/api/events', {mode:'list'}).then(resp => resp.json()).then( (data)=> {
                this.json = data
                this.printList()
                resolve()
            })
        })
    }
}

export default EVENTS