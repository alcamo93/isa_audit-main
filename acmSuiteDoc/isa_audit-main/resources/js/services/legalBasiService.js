import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getLegalBasis(idGuideline, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/guideline/${idGuideline}/legal_basi${query}`,
    method: 'GET',
  })
}

export function storeLegalBasi(idGuideline, data) {
  return webInstance({
    url: `/v2/catalogs/guideline/${idGuideline}/legal_basi`,
    method: 'POST',
    data,
  })
}

export function getLegalBasi(idGuideline, id, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/guideline/${idGuideline}/legal_basi/${id}${query}`,
    method: 'GET',
  })
}

export function updateLegalBasi(idGuideline, id, data) {
  return webInstance({
    url: `/v2/catalogs/guideline/${idGuideline}/legal_basi/${id}`,
    method: 'PUT',
    data,
  })
}

export function deleteLegalBasi(idGuideline, id) {
  return webInstance({
    url: `/v2/catalogs/guideline/${idGuideline}/legal_basi/${id}`,
    method: 'DELETE'
  })
}