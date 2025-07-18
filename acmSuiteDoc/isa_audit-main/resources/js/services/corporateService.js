import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getCorporates(idCustomer, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/customer/${idCustomer}/corporate${query}`,
    method: 'GET',
  })
}
export function storeCorporate(idCustomer, data) {
  return webInstance({
    url: `/v2/customer/${idCustomer}/corporate`,
    method: 'POST',
    data,
  })
}
export function getCorporate(idCustomer, id, paramsObj = {}) {
  const query = queryParams.serializeParams(paramsObj)
  return webInstance({
    url: `/v2/customer/${idCustomer}/corporate/${id}${query}`,
    method: 'GET',
  })
}
export function updateCorporate(idCustomer, id, data) {
  return webInstance({
    url: `/v2/customer/${idCustomer}/corporate/${id}`,
    method: 'PUT',
    data,
  })
}
export function deleteCorporate(idCustomer, id) {
  return webInstance({
    url: `/v2/customer/${idCustomer}/corporate/${id}`,
    method: 'DELETE'
  })
}
export function setImage(idCustomer, id, data) {
  return webInstance({
    url: `/v2/customer/${idCustomer}/corporate/${id}/image`,
    method: 'POST',
    data,
  })
}