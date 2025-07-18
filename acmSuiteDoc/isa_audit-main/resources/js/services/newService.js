import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getNews(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/new${query}`,
    method: 'GET',
  })
}
export function storeNew(data) {
  return webInstance({
    url: `/v2/new`,
    method: 'POST',
    data,
  })
}
export function getNew(id, paramsObj = {}) {
  const query = queryParams.serializeParams(paramsObj)
  return webInstance({
    url: `/v2/new/${id}${query}`,
    method: 'GET',
  })
}
export function updateNew(id, data) {
  return webInstance({
    url: `/v2/new/${id}`,
    method: 'POST',
    data,
  })
}
export function deleteNew(id) {
  return webInstance({
    url: `/v2/new/${id}`,
    method: 'DELETE'
  })
}

export function updateStatusNew(published, id) {
  return webInstance({
    url: `/v2/new/${published}/${id}/status`,
    method: 'PUT'
  })
}