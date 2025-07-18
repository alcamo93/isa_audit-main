import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getRelationGuideline(idForm, idQuestion, paramsObj, filtersObj) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/legal/guideline${query}`,
    method: 'GET',
  })
}

export function getRelationArticles(idForm, idQuestion, paramsObj, filtersObj) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/legal/article${query}`,
    method: 'GET',
  })
}

export function setRelationLegal(idForm, idQuestion, idLegalBasis) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/legal/relation/${idLegalBasis}`,
    method: 'POST',
  })
}