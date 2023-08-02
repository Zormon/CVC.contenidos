import {toolTip, fetchPost, sortJson, modalBox, selectModal, toggleRowActions, contentBox, modalConfirm, $, $$$} from '/js/exports.js?3'

class USERS {
    constructor (json, searchElement, orderElement) {
        this.json = json
        this.search = searchElement
        this.order = orderElement
    }

    details (id) {
        alert(`Estado de ${id}`)
    }

    modal (id) {
        if (!id) {
            let html = Mustache.render( $('modalUser').innerHTML, {} )
            modalBox (html, [ {text:'Cancelar'}, {text:'Añadir', action:()=> { return this.send(true) }} ])
        } else {
            let user = this.json[id]
            let html = Mustache.render( $('modalUser').innerHTML, user )
            modalBox (html, [ {text:'Cancelar'}, {text:'Editar', action:()=> { return this.send() }} ])
            user.shops.forEach( s=> { try {  $('t'+s).selected = true }catch(e){}} )
        }
    
        $('isAdmin').onclick = (e)=> {
            if (!e.currentTarget.checked) {
                $$$('#permList input[type=checkbox]').forEach(el => { el.disabled = false } )
            } else {
                $$$('#permList input[type=checkbox]').forEach(el => { el.disabled = true } )
            }
        }

        $('shops').selectModal = new selectModal( 'shops', 'Tiendas', 'grid', true, [] )
    }
    
    send (add=false) {
        $$$('#permList input[type=checkbox]').forEach(el => { el.disabled = false } )
        if ( !$('userData').checkValidity() ) {
            $('userData').reportValidity()
            return false
        } else {
            const userForm = new FormData($('userData'))
            if (add)    { userForm.append('mode', 'add') }
            else        { userForm.append('mode', 'edit') }
            
            fetch('/api/users', {method: 'POST', body: userForm}).then(resp => resp.json()).then( (data)=> {
                if ( data.status == 'ok' ) { 
                    if (data.tmpPass) { alert(`Contraseña temporal: ${data.tmpPass}`) }
                    this.refresh() 
                }
                else { alert(`ERROR: ${data}`) }
            })
    
            return true
        }
    }

    delete (id) {
        modalConfirm(`¿Borrar usuario <em>${this.json[id].login}</em> ?`, ()=> {
            fetchPost('/api/users', {mode:'delete', id:id}).then(resp => resp.json()).then( (data)=> {
                if (data.status == 'ok')   { $(`u${id}`).remove() }
                else                { alert(`ERROR: ${data}`) }
            })
        })
    }

    printList () {
        const textFilter = this.search.value.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
        // Limpia la lista
        const elList = $('ul_users')
        while (elList.firstChild) { elList.removeChild(elList.lastChild) }
        
        let filtered = []
        for ( let el of Object.values(this.json) ) {
            const name = el.name.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
            if ( textFilter == '' || namesearch( textFilter ) != -1 ) {
                filtered.push(el)
            }
        }
        sortJson(filtered, this.order.value)

        if (filtered.length == 0) {
            contentBox( $('main_users'), 'info', 'No hay usuarios' )
        } else {
            contentBox( $('main_users'), 'info', false )
            for (let user of filtered) {
                let li = document.createElement('li'); li.id = `u${user.id}`;
                li.dataset.id = user.id;
                li.innerHTML = Mustache.render( $('rowUser').innerHTML, user )
                li.querySelectorAll('.tooltip').forEach(el => { el.tooltip = new toolTip(el) })
                li.querySelector('.trigger-actions').onmouseenter = ev => { toggleRowActions(ev) }

                /*---------- Botones de acciones  ----------*/
                li.querySelector('.uDetails').onclick = ()=>    { this.details(user.id) }
                if (LOGIN.can.edit.users) {
                    li.querySelector('.uEdit').onclick = ()=>   { this.modal(user.id) }
                    li.querySelector('.uDelete').onclick = ()=> { this.delete(user.id) }
                }

                elList.appendChild(li)
            }
        }
    }

    refresh () {
        return new Promise((resolve)=> {
            fetchPost('/api/users', {mode:'list'}).then(resp => resp.json()).then( (data)=> {
                this.json = data
                this.printList()
                resolve()
            })
        })
    }
}


export default USERS