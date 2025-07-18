import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getRelationGuideline(idForm, idRequirement, idSubrequirement, paramsObj, filtersObj) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/subrequirement/${idSubrequirement}/legal/guideline${query}`,
    method: 'GET',
  })
}

export function getRelationArticles(idForm, idRequirement, idSubrequirement, paramsObj, filtersObj) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/subrequirement/${idSubrequirement}/legal/article${query}`,
    method: 'GET',
  })
}

export function setRelationLegal(idForm, idRequirement, idSubrequirement, idLegalBasis) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/requirement/${idRequirement}/subrequirement/${idSubrequirement}/legal/relation/${idLegalBasis}`,
    method: 'POST',
  })
}