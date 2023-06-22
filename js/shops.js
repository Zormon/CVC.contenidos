import {contentBox, $, selectModal, modalBox, fetchPost, sortJson} from '/js/exports.js?3'

class SHOPS {
    constructor (json, searchElement, orderElement) {
        this.json = json
        this.search = searchElement
        this.order = orderElement
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
        if ( confirm(`¿Borrar tienda ${this.json[id].name}?`) ) {
            fetchPost('/api/shops', {mode:'delete', id:id}).then(resp => resp.json()).then( (data)=> {
                if (data.status == 'ok')     { delete this.json[id]; this.printList() }
                else                         { alert(`ERROR: ${data.error}`) }
            })
        }
    }

    /**
     * Imprime las tarjetas con una plantilla de mustache desde el array this.json
     */
    printList() {
        // Limpia la lista
        const textFilter = this.search.value.toUpperCase().normalize("NFD").replace(/\p{Diacritic}/gu, "")
        const elList = $('shopList')
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
                const id = shop.id
                let el = document.createElement('div'); el.id = `t${id}`
                el.style.backgroundImage = `url(/img/shops/${id}.webp?${shop.version})`
                el.className = 'card'
                if (shop.ndevices != 1) { shop.pdevices = true }
                el.innerHTML = Mustache.render( $('shopCard').innerHTML, shop )
                el.querySelector('.title').onclick = (e)=> { e.currentTarget.parentElement.parentElement.classList.toggle('expanded') }
                if (LOGIN.can.edit.shops) {
                    el.querySelector('.tEdit').onclick = (e)=> { this.modal(id) }
                    el.querySelector('.tDelete').onclick = (e)=> { this.delete(id) }
                }
                $('shopList').appendChild(el)
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