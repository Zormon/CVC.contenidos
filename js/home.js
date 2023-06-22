
const DEVCHART_COLORS = { Canalcorp: '#157946', Turnomatic: '#c85a26', Totem: '#e00b0b', Vertical: '#b8a500', Otro: 'gray'}
let MEDIACHART_COLORS = [] // Colores de categorias de contenidos
jsonOverview.cats.forEach(e => { MEDIACHART_COLORS[e.name] = `#${e.color}` })

/*=============================================
=            Funciones            =
=============================================*/

function makeChart ( el, labels, data, colors ) {
   let bgcolors=[]

   labels.forEach(lb => {
      if (lb in colors)    { bgcolors.push	( colors[lb] ) }
      else                 { bgcolors.push ( 'black') }
   })
   
   new Chart(_$(el).getContext('2d'), {
      type: 'doughnut',
      data: {
          labels: labels,
          datasets: [{
              backgroundColor: bgcolors,
              data: data
          }]
      }, options: { legend:{ display: false }, tooltips: { enabled: false } }
   })
}

var HOME = {}
// Donut Chart de equipos
HOME.deviceTypes = () => {
   let labels=[], nums=[]
   Object.keys(jsonOverview.devices).forEach((item) => {
      labels.push(item)
      nums.push(jsonOverview.devices[item])
   
      let i = document.createElement('i')
      i.className = 'material-icons'
      if (item in DEVCHART_COLORS)  { i.style = `color: ${DEVCHART_COLORS[item]}` }
      else                          { i.style = `color: black` }
      i.textContent = 'brightness_1'
      let spanLb = document.createElement('span')
      spanLb.textContent = item
      let spanNum = document.createElement('span')
      spanNum.textContent = jsonOverview.devices[item]
   
      _$('devicesChartLegend').appendChild(i)
      _$('devicesChartLegend').appendChild(spanLb)
      _$('devicesChartLegend').appendChild(spanNum)
   })
   makeChart( 'devicesChart', labels, nums, DEVCHART_COLORS )
}

// Donut Chart de contenidos
HOME.mediaTypes = () => {
   labels=[], nums=[]
   Object.keys(jsonOverview.media).forEach((item) => {
      labels.push(item)
      nums.push(jsonOverview.media[item])

      let i = document.createElement('i')
      i.className = 'material-icons'
      if (item in MEDIACHART_COLORS)  { i.style = `color: ${MEDIACHART_COLORS[item]}` }
      else                          { i.style = `color: black` }
      i.textContent = 'brightness_1'
      let spanLb = document.createElement('span')
      spanLb.textContent = item
      let spanNum = document.createElement('span')
      spanNum.textContent = jsonOverview.media[item]

      _$('mediaChartLegend').appendChild(i)
      _$('mediaChartLegend').appendChild(spanLb)
      _$('mediaChartLegend').appendChild(spanNum)
   })
   makeChart( 'mediaChart', labels, nums, MEDIACHART_COLORS )
}

// Ultimos contenidos
HOME.lastMedia = () => {
   let table = _$('lastMediaList')
   // Limpia la lista
   while (table.firstChild) { table.removeChild(table.lastChild) }
   jsonOverview.lastMedia.forEach(media => {
      let tr = document.createElement('tr')
      tr.id = `lM${media.id}`; tr.style.background = `linear-gradient(to right, #${media.color}50, white)`
      tr.innerHTML = Mustache.render( _$('trLastMedia').innerHTML, media )
      table.appendChild(tr)
   })
   
}

/*=====  End of Funciones  ======*/


/*=============================================
=            MAIN            =
=============================================*/

HOME.mediaTypes()
HOME.deviceTypes()
HOME.lastMedia()

/*=====  End of MAIN  ======*/



