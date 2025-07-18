import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getQuestions(idForm, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question${query}`,
    method: 'GET',
  })
}

export function storeQuestion(idForm, data) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question`,
    method: 'POST',
    data,
  })
}

export function getQuestion(idForm, id, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${id}${query}`,
    method: 'GET',
  })
}

export function updateQuestion(idForm, id, data) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${id}`,
    method: 'PUT',
    data,
  })
}

export function deleteQuestion(idForm, id) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${id}`,
    method: 'DELETE'
  })
}

export function updateStatusQuestion(idForm, id) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${id}/status`,
    method: 'PUT'
  })
}