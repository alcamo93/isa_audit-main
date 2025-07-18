import webInstance from './index'

export function getDataRenderDetail(items, encripted) {
  return webInstance({
    url: `/v2/details/${items}/${encripted}`,
    method: 'GET'
  })
}