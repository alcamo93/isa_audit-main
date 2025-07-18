import webInstance from './index'
import queryParams from '../utils/queryParams'

// Requirements

export function getReqRecomendations(idRequirement, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/specific/requirement/${idRequirement}/recomendation${query}`,
    method: 'GET',
  })
}

export function setReqRecomendation(idRequirement, data) {
  return webInstance({
    url: `/v2/specific/requirement/${idRequirement}/recomendation`,
    method: 'POST',
    data,
  })
}

export function getReqRecomendation(idRequirement, id, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/specific/requirement/${idRequirement}/recomendation/${id}${query}`,
    method: 'GET',
  })
}

export function updateReqRecomendation(idRequirement, id, data) {
  return webInstance({
    url: `/v2/specific/requirement/${idRequirement}/recomendation/${id}`,
    method: 'PUT',
    data,
  })
}

export function deleteReqRecomendation(idRequirement, id) {
  return webInstance({
    url: `/v2/specific/requirement/${idRequirement}/recomendation/${id}`,
    method: 'DELETE'
  })
}