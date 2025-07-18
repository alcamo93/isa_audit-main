import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getUsers(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
      url: `/v2/user${query}`,
      method: 'GET',
  })
}
export function storeUser(data) {
  return webInstance({
    url: `/v2/user`,
    method: 'POST',
    data,
  })
}
export function getUser(id, paramsObj = {}) {
  const query = queryParams.serializeParams(paramsObj)
  return webInstance({
    url: `/v2/user/${id}${query}`,
    method: 'GET',
  })
}
export function updateUser(id, data) {
  return webInstance({
    url: `/v2/user/${id}`,
    method: 'PUT',
    data,
  })
}
export function deleteUser(id) {
  return webInstance({
    url: `/v2/user/${id}`,
    method: 'DELETE'
  })
}
export function updateUserPassword(id, data) {
  return webInstance({
    url: `/v2/user/${id}/password`,
    method: 'PUT',
    data,
  })
}