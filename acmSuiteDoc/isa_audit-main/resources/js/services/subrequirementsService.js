import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getSubrequirements(idForm, idRequirement, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/subrequirement${query}`,
    method: 'GET',
  })
}

export function storeSubrequirement(idForm, idRequirement, data) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/subrequirement`,
    method: 'POST',
    data,
  })
}

export function getSubrequirement(idForm, idRequirement, idSubequirement, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/subrequirement/${idSubequirement}${query}`,
    method: 'GET',
  })
}

export function updateSubrequirement(idForm, idRequirement, idSubequirement, data) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/subrequirement/${idSubequirement}`,
    method: 'PUT',
    data,
  })
}

export function deleteSubrequirement(idForm, idRequirement, idSubequirement) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/subrequirement/${idSubequirement}`,
    method: 'DELETE'
  })
}