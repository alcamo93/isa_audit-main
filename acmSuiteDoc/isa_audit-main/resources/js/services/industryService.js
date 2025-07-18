import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getIndustries(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/industry${query}`,
    method: 'GET',
  })
}
export function storeIndustry(data) {
  return webInstance({
    url: `/v2/catalogs/industry`,
    method: 'POST',
    data,
  })
}
export function getIndustry(id, paramsObj = {}) {
  const query = queryParams.serializeParams(paramsObj)
  return webInstance({
    url: `/v2/catalogs/industry/${id}${query}`,
    method: 'GET',
  })
}
export function updateIndustry(id, data) {
  return webInstance({
    url: `/v2/catalogs/industry/${id}`,
    method: 'PUT',
    data,
  })
}
export function deleteIndustry(id) {
  return webInstance({
    url: `/v2/catalogs/industry/${id}`,
    method: 'DELETE'
  })
}