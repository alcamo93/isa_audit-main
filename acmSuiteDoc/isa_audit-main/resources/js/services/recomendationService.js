import webInstance from './index'
import queryParams from '../utils/queryParams'

// Requirements

export function getReqRecomendations(idForm, idRequirement, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/recomendation${query}`,
    method: 'GET',
  })
}

export function setReqRecomendation(idForm, idRequirement, data) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/recomendation`,
    method: 'POST',
    data,
  })
}

export function getReqRecomendation(idForm, idRequirement, id, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/recomendation/${id}${query}`,
    method: 'GET',
  })
}

export function updateReqRecomendation(idForm, idRequirement, id, data) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/recomendation/${id}`,
    method: 'PUT',
    data,
  })
}

export function deleteReqRecomendation(idForm, idRequirement, id) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/recomendation/${id}`,
    method: 'DELETE'
  })
}

// Subrequirements

export function getSubRecomendations(idForm, idRequirement, idSubrequirement, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/subrequirement/${idSubrequirement}/recomendation${query}`,
    method: 'GET',
  })
}

export function setSubRecomendation(idForm, idRequirement, idSubrequirement, data) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/subrequirement/${idSubrequirement}/recomendation`,
    method: 'POST',
    data,
  })
}

export function getSubRecomendation(idForm, idRequirement, idSubrequirement, id, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/subrequirement/${idSubrequirement}/recomendation/${id}${query}`,
    method: 'GET',
  })
}

export function updateSubRecomendation(idForm, idRequirement, idSubrequirement, id, data) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/subrequirement/${idSubrequirement}/recomendation/${id}`,
    method: 'PUT',
    data,
  })
}

export function deleteSubRecomendation(idForm, idRequirement, idSubrequirement, id) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/subrequirement/${idSubrequirement}/recomendation/${id}`,
    method: 'DELETE'
  })
}