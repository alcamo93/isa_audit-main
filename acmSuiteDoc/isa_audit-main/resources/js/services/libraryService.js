import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getLibraries(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/library${query}`,
    method: 'GET',
  })
}
export function storeLibrary(data) {
  return webInstance({
    url: '/v2/library',
    method: 'POST',
    data,
  })
}
export function updateLibrary(id, data) {
  return webInstance({
    url: `/v2/library/${id}/update`,
    method: 'POST',
    data,
  })
}
export function getLibrary(id) {
  return webInstance({
    url: `/v2/library/${id}`,
    method: 'GET',
  })
}
export function renewalLibrary(id, data) {
  return webInstance({
    url: `/v2/library/${id}/renewal`,
    method: 'POST',
    data,
  })
}
export function approveLibrary(id, data) {
  return webInstance({
    url: `/v2/library/${id}/approve`,
    method: 'PUT',
    data,
  })
}
export function execBackupLibrary(id){
  return webInstance({
    url: `/v2/library/${id}/download`,
    method: 'GET',
  })
}