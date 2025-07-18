import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getAccount(paramsObj = {}) {
  const query = queryParams.serializeParams(paramsObj)
  return webInstance({
    url: `/v2/account${query}`,
    method: 'GET',
  })
}
export function updateAccount(data) {
  return webInstance({
    url: `/v2/account`,
    method: 'PUT',
    data,
  })
}
export function setImage(data) {
  return webInstance({
    url: `/v2/account/image`,
    method: 'POST',
    data,
  })
}