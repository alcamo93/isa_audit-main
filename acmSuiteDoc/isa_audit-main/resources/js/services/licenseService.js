import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getLicenses(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/license${query}`,
    method: 'GET',
  })
}
export function storeLicense(data) {
  return webInstance({
    url: '/v2/license',
    method: 'POST',
    data,
  })
}
export function updateLicense(id, data) {
  return webInstance({
    url: `/v2/license/${id}`,
    method: 'PUT',
    data,
  })
}
export function getLicense(id) {
  return webInstance({
    url: `/v2/license/${id}`,
    method: 'GET',
  })
}
export function deleteLicense(id) {
  return webInstance({
    url: `/v2/license/${id}`,
    method: 'DELETE',
  })
}
export function renewalLicense(id, data) {
  return webInstance({
    url: `/v2/license/${id}/renewal`,
    method: 'POST',
    data,
  })
}
export function changeStatus(id) {
  return webInstance({
      url: `/v2/license/${id}/change-status`,
      method: 'PUT',
  })
}