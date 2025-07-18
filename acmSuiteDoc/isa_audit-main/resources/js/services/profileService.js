import webInstance from './index'
import queryParams from '../utils/queryParams'

export function whatProfile(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
      url: `/v2/what/profile${query}`,
      method: 'GET',
  })
}
export function getProfiles(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/profile${query}`,
    method: 'GET',
  })
}
export function storeProfile(data) {
  return webInstance({
    url: `/v2/profile`,
    method: 'POST',
    data,
  })
}
export function getProfile(id, paramsObj = {}) {
  const query = queryParams.serializeParams(paramsObj)
  return webInstance({
    url: `/v2/profile/${id}${query}`,
    method: 'GET',
  })
}
export function updateProfile(id, data) {
  return webInstance({
    url: `/v2/profile/${id}`,
    method: 'PUT',
    data,
  })
}
export function deleteProfile(id) {
  return webInstance({
    url: `/v2/profile/${id}`,
    method: 'DELETE'
  })
}