import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getNotifications(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/notification${query}`,
    method: 'GET',
  })
}
export function getTotalNotifications(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/notification/total${query}`,
    method: 'GET',
  })
}
export function updateNotification(data) {
  return webInstance({
    url: `/v2/notification/update`,
    method: 'PUT',
    data
  })
}
export function deleteNotification(id) {
  return webInstance({
    url: `/v2/notification/${id}`,
    method: 'DELETE',
  })
}