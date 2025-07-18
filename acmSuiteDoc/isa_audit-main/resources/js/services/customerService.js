import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getCustomers(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/customer${query}`,
    method: 'GET',
  })
}
export function storeCustomer(data) {
  return webInstance({
    url: `/v2/customer`,
    method: 'POST',
    data,
  })
}
export function getCustomer(id, paramsObj = {}) {
  const query = queryParams.serializeParams(paramsObj)
  return webInstance({
    url: `/v2/customer/${id}${query}`,
    method: 'GET',
  })
}
export function updateCustomer(id, data) {
  return webInstance({
    url: `/v2/customer/${id}`,
    method: 'PUT',
    data,
  })
}
export function deleteCustomer(id) {
  return webInstance({
    url: `/v2/customer/${id}`,
    method: 'DELETE'
  })
}
export function setImage(id, usage, data) {
  return webInstance({
    url: `/v2/customer/${id}/image/${usage}`,
    method: 'POST',
    data,
  })
}