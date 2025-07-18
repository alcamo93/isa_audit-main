import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getListProcess(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
      url: `/v2/process${query}`,
      method: 'GET',
  })
}
export function storeProcess(data) {
  return webInstance({
    url: `/v2/process`,
    method: 'POST',
    data,
  })
}
export function getProcess(id, paramsObj = {}) {
  const query = queryParams.serializeParams(paramsObj)
  return webInstance({
      url: `/v2/process/${id}${query}`,
      method: 'GET',
  })
}
export function updateProcess(id, data) {
  return webInstance({
    url: `/v2/process/${id}`,
    method: 'PUT',
    data,
  })
}
export function deleteProcess(id) {
  return webInstance({
    url: `/v2/process/${id}`,
    method: 'DELETE'
  })
}
export function setRenewal(id, data) {
  return webInstance({
    url: `/v2/process/${id}/renewal`,
    method: 'POST',
    data,
  })
}