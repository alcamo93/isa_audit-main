import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getContacts(idCustomer, idCorporate, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/customer/${idCustomer}/corporate/${idCorporate}/contact${query}`,
    method: 'GET',
  })
}
export function storeContact(idCustomer, idCorporate, data) {
  return webInstance({
    url: `/v2/customer/${idCustomer}/corporate/${idCorporate}/contact`,
    method: 'POST',
    data,
  })
}
export function getContact(idCustomer, idCorporate, id, paramsObj = {}) {
  const query = queryParams.serializeParams(paramsObj)
  return webInstance({
    url: `/v2/customer/${idCustomer}/corporate/${idCorporate}/contact/${id}${query}`,
    method: 'GET',
  })
}
export function updateContact(idCustomer, idCorporate, id, data) {
  return webInstance({
    url: `/v2/customer/${idCustomer}/corporate/${idCorporate}/contact/${id}`,
    method: 'PUT',
    data,
  })
}
export function deleteContact(idCustomer, idCorporate, id) {
  return webInstance({
    url: `/v2/customer/${idCustomer}/corporate/${idCorporate}/contact/${id}`,
    method: 'DELETE'
  })
}