import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getAddresses(idCustomer, idCorporate, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/customer/${idCustomer}/corporate/${idCorporate}/address${query}`,
    method: 'GET',
  })
}
export function storeAddress(idCustomer, idCorporate, data) {
  return webInstance({
    url: `/v2/customer/${idCustomer}/corporate/${idCorporate}/address`,
    method: 'POST',
    data,
  })
}
export function getAddress(idCustomer, idCorporate, id, paramsObj = {}) {
  const query = queryParams.serializeParams(paramsObj)
  return webInstance({
    url: `/v2/customer/${idCustomer}/corporate/${idCorporate}/address/${id}${query}`,
    method: 'GET',
  })
}
export function updateAddress(idCustomer, idCorporate, id, data) {
  return webInstance({
    url: `/v2/customer/${idCustomer}/corporate/${idCorporate}/address/${id}`,
    method: 'PUT',
    data,
  })
}
export function deleteAddress(idCustomer, idCorporate, id) {
  return webInstance({
    url: `/v2/customer/${idCustomer}/corporate/${idCorporate}/address/${id}`,
    method: 'DELETE'
  })
}