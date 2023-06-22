import {contentBox, $, selectModal, modalBox, fetchPost, sortJson, toolTip, toggleRowActions, modalConfirm} from '/js/exports.js?3'

class SHOPS {
    constructor (json, searchElement, orderElement) {
        this.json = json
        this.search = searchElement
        this.order = orderElement
    }

    makeCall(phone) {
        location = 'tel:'+phone
    }

    sendMail(email) {
        location = 'mailto:'+email
    }

    modal(id=false) {
        if (!id) {
            let html = Mustache.render( $('modalShop').innerHTML, {} )
            modalBox (html, [ {text:'Cancelar'}, {text:'Añadir', action:()=> { return this.edit(true) }} ])
        } else {
            let shop = this.json[id]
            let html = Mustache.render( $('modalShop').innerHTML, shop )
            modalBox (html, [ {text:'Cancelar'}, {text:'Editar', action:()=> { return this.edit(false)}} ])
            $('canal').value = shop.canal
        }

        $('canal').selectModal = new selectModal( 'canal', 'Hilo musical', 'list' )
    }

    edit(add=false) {
        if ( !$('shopData').checkValidity()) {
            $('shopData').reportValidity()
            return false
        } else {
            const shopForm = new FormData($('shopData'))
            if (add)     { shopForm.append('mode', 'add') }
            else         { shopForm.append('mode', 'edit') }
            
            fetch('/api/shops', {method: 'POST', body: shopForm}).then(resp => resp.json()).then( (data)=> {
                if ( data.status == 'ok' )    { this.refresh() }
                else                          { alert(`ERROR: ${data.error}`) }
            })
    
            return true
        }
    }

    delete(id) {
        modalConfirm(`¿Borrar tienda ${this.json[id].name}?`, ()=> {
            fetchPost('/api/shops', {mode:'delete', id:id}).then(resp => resp.json()).then( (data)=> {
                if (data.status == 'ok')     { delete this.json[id]; this.printList() }
                else                         { alert(`ERROR: ${data.error}`) }
            })
        })
    }

    /**
     * Imprime las tarjetas con una plantilla de mustache desde el array this.json
     */
    printList() {
        // Limpia la lista
        const textFilter = this.search.value.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
        const elList = $('ul_shops')
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
            contentBox( $('shopList'), 'info', 'No hay tiendas' )
        } else {
            contentBox( $('shopList'), 'info', false )
            for (let shop of filtered) {
                let li = document.createElement('li'); li.id = `t${shop.id}`
                li.dataset.id = shop.id
                if (shop.ndevices != 1) { shop.pdevices = true }
                li.innerHTML = Mustache.render( $('rowShop').innerHTML, shop )
                li.querySelectorAll('.tooltip').forEach(el => { el.tooltip = new toolTip(el) })
                li.querySelector('.trigger-actions').onmouseenter = ev => { toggleRowActions(ev) }

                const callBtn = li.querySelector('.tCall')
                if (callBtn) { callBtn.onclick = ()=> { this.makeCall(shop.telefono) } }

                const mailBtn = li.querySelector('.tMail')
                if (mailBtn) { mailBtn.onclick = ()=> { this.sendMail(shop.email) } }


                if (LOGIN.can.edit.shops) {
                    li.querySelector('.tEdit').onclick = (e)=> { this.modal(shop.id) }
                    li.querySelector('.tDelete').onclick = (e)=> { this.delete(shop.id) }
                }
                elList.appendChild(li)
            }
        }
    }

    refresh() {
        return new Promise((resolve)=> {
            fetchPost('/api/shops', {mode:'list'}).then(resp => resp.json()).then( (data)=> {
                this.json = data
                this.printList()
                resolve()
            })
        })
    }
}


export default SHOPS