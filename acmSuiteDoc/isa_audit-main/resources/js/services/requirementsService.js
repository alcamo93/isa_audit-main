import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getRequirements(idForm, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement${query}`,
    method: 'GET',
  })
}

export function storeRequirement(idForm, data) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement`,
    method: 'POST',
    data,
  })
}

export function getRequirement(idForm, id, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${id}${query}`,
    method: 'GET',
  })
}

export function updateRequirement(idForm, id, data) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${id}`,
    method: 'PUT',
    data,
  })
}

export function deleteRequirement(idForm, id) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${id}`,
    method: 'DELETE'
  })
}