import webInstance from './index'
import queryParams from '../utils/queryParams'

// matters services

export function getMatters(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/matter${query}`,
    method: 'GET',
  })
}
export function storeMatter(data) {
  return webInstance({
    url: `/v2/catalogs/matter`,
    method: 'POST',
    data,
  })
}
export function getMatter(id, paramsObj = {}) {
  const query = queryParams.serializeParams(paramsObj)
  return webInstance({
    url: `/v2/catalogs/matter/${id}${query}`,
    method: 'GET',
  })
}
export function updateMatter(id, data) {
  return webInstance({
    url: `/v2/catalogs/matter/${id}`,
    method: 'PUT',
    data,
  })
}
export function deleteMatter(id) {
  return webInstance({
    url: `/v2/catalogs/matter/${id}`,
    method: 'DELETE'
  })
}

// aspects services

export function getAspects(idMatter, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/matter/${idMatter}/aspect${query}`,
    method: 'GET',
  })
}
export function storeAspect(idMatter, data) {
  return webInstance({
    url: `/v2/catalogs/matter/${idMatter}/aspect`,
    method: 'POST',
    data,
  })
}
export function getAspect(idMatter, id, paramsObj = {}) {
  const query = queryParams.serializeParams(paramsObj)
  return webInstance({
    url: `/v2/catalogs/matter/${idMatter}/aspect/${id}${query}`,
    method: 'GET',
  })
}
export function updateAspect(idMatter, id, data) {
  return webInstance({
    url: `/v2/catalogs/matter/${idMatter}/aspect/${id}`,
    method: 'PUT',
    data,
  })
}
export function deleteAspect(idMatter, id) {
  return webInstance({
    url: `/v2/catalogs/matter/${idMatter}/aspect/${id}`,
    method: 'DELETE'
  })
}
export function setImage(id, data) {
  return webInstance({
    url: `/v2/customer/${id}/image`,
    method: 'POST',
    data,
  })
}