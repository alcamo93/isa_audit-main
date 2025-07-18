import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getContracts(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/contract${query}`,
    method: 'GET',
  })
}
export function storeContract(data) {
  return webInstance({
    url: '/v2/contract',
    method: 'POST',
    data,
  })
}
export function updateContract(id, data) {
  return webInstance({
    url: `/v2/contract/${id}`,
    method: 'PUT',
    data,
  })
}
export function getContract(id) {
  return webInstance({
    url: `/v2/contract/${id}`,
    method: 'GET',
  })
}
export function deleteContract(id) {
  return webInstance({
    url: `/v2/contract/${id}`,
    method: 'DELETE',
  })
}
export function changeStatus(id) {
  return webInstance({
      url: `/v2/contract/${id}/change-status`,
      method: 'PUT',
  })
}