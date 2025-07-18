import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getGuidelines(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/guideline${query}`,
    method: 'GET',
  })
}

export function storeGuideline(data) {
  return webInstance({
    url: '/v2/catalogs/guideline',
    method: 'POST',
    data,
  })
}

export function getGuideline(id, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/guideline/${id}${query}`,
    method: 'GET',
  })
}

export function updateGuideline(id, data) {
  return webInstance({
    url: `/v2/catalogs/guideline/${id}`,
    method: 'PUT',
    data,
  })
}

export function deleteGuideline(id) {
  return webInstance({
    url: `/v2/catalogs/guideline/${id}`,
    method: 'DELETE'
  })
}

export function getRelationTopics(idGuideline, paramsObj, filtersObj) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/guideline/${idGuideline}/topic${query}`,
    method: 'GET',
  })
}

export function setRelationTopics(idGuideline, idTopic) {
  return webInstance({
    url: `/v2/catalogs/guideline/${idGuideline}/topic/relation/${idTopic}`,
    method: 'POST',
  })
}

export function getRelationAspects(idGuideline, paramsObj, filtersObj) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/guideline/${idGuideline}/aspect${query}`,
    method: 'GET',
  })
}

export function setRelationAspects(idAspect, idMatter, idGuideline) {
  return webInstance({
    url: `/v2/catalogs/guideline/${idGuideline}/aspect/relation/${idAspect}/${idMatter}`,
    method: 'POST',
  })
}