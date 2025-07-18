import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getRelationGuideline(idForm, idRequirement, paramsObj, filtersObj) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/legal/guideline${query}`,
    method: 'GET',
  })
}

export function getRelationArticles(idForm, idRequirement, paramsObj, filtersObj) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/legal/article${query}`,
    method: 'GET',
  })
}

export function setRelationLegal(idForm, idRequirement, idLegalBasis) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/legal/relation/${idLegalBasis}`,
    method: 'POST',
  })
}