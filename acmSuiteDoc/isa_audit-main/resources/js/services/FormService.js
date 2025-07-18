import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getForms(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/forms${query}`,
    method: 'GET',
  })
}

export function storeForm(data) {
  return webInstance({
    url: '/v2/catalogs/forms',
    method: 'POST',
    data,
  })
}

export function getForm(id, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/forms/${id}${query}`,
    method: 'GET',
  })
}

export function updateForm(id, data) {
  return webInstance({
    url: `/v2/catalogs/forms/${id}`,
    method: 'PUT',
    data,
  })
}

export function deleteForm(id) {
  return webInstance({
    url: `/v2/catalogs/forms/${id}`,
    method: 'DELETE'
  })
}

export function changeCurrent(id, data) {
  return webInstance({
    url: `/v2/catalogs/forms/${id}/change-current`,
    method: 'PUT',
    data,
  })
}

export function duplicateForm(id, data) {
  return webInstance({
    url: `/v2/catalogs/forms/${id}/duplicate`,
    method: 'POST',
    data,
  })
}