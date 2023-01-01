'use strict'

import L from 'leaflet'

async function fetchImage (url, callback, headers, abort) {
  const _headers = {}
  if (headers) {
    headers.forEach((h) => {
      _headers[h.header] = h.value
    })
  }
  const controller = new AbortController()
  const signal = controller.signal
  if (abort) {
    abort.subscribe(() => {
      controller.abort()
    })
  }
  const f = await fetch(url, {
    method: 'GET',
    headers: _headers,
    mode: 'cors',
    signal: signal
  })
  const blob = await f.blob()
  callback(blob)
}

L.TileLayerWithHeaders = L.TileLayer.extend({
  initialize: function (url, options, headers, abort) {
    L.TileLayer.prototype.initialize.call(this, url, options)
    this.headers = headers
    this.abort = abort
  },
  createTile (coords, done) {
    const url = this.getTileUrl(coords)
    const tile = document.createElement('img')

    L.DomEvent.on(tile, 'load', L.Util.bind(this._tileOnLoad, this, done, tile))
    L.DomEvent.on(tile, 'error', L.Util.bind(this._tileOnError, this, done, tile))

    tile.setAttribute('role', 'presentation')

    fetchImage(
      url,
      (resp) => {
        const reader = new FileReader()
        reader.onload = () => {
          tile.src = reader.result
        }
        reader.readAsDataURL(resp)
        done(null, tile)
      },
      this.headers,
      this.abort
    )
    return tile
  }
})


L.TileLayerH = function (url, options, headers, abort) {
  return new L.TileLayerWithHeaders(url, options, headers, abort);
};