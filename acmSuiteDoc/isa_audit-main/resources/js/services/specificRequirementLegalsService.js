import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getRelationGuideline(idRequirement, paramsObj, filtersObj) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/specific/requirement/${idRequirement}/legal/guideline${query}`,
    method: 'GET',
  })
}

export function getRelationArticles(idRequirement, paramsObj, filtersObj) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/specific/requirement/${idRequirement}/legal/article${query}`,
    method: 'GET',
  })
}

export function setRelationLegal(idRequirement, idLegalBasis) {
  return webInstance({
    url: `/v2/specific/requirement/${idRequirement}/legal/relation/${idLegalBasis}`,
    method: 'POST',
  })
}