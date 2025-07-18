import webInstance from './index'
import queryParams from '../utils/queryParams'

// requirements

export function getRelationRequirementTypes(idForm, idQuestion, idAnswerQuestion, paramsObj, filtersObj) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/answer/${idAnswerQuestion}/requirement/requirement_types${query}`,
    method: 'GET',
  })
}

export function getRelationRequirements(idForm, idQuestion, idAnswerQuestion, paramsObj, filtersObj) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/answer/${idAnswerQuestion}/requirement/records${query}`,
    method: 'GET',
  })
}

export function setRelationRequirement(idForm, idQuestion, idAnswerQuestion, idRequirement) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/answer/${idAnswerQuestion}/requirement/relation/${idRequirement}`,
    method: 'POST',
  })
}

// subrequirements

export function getRelationSubrequirements(idForm, idQuestion, idAnswerQuestion, idRequirement, paramsObj, filtersObj) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/answer/${idAnswerQuestion}/requirement/${idRequirement}/subrequirements${query}`,
    method: 'GET',
  })
}

export function setRelationSubrequirement(idForm, idQuestion, idAnswerQuestion, idRequirement, idSubrequirement) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/answer/${idAnswerQuestion}/requirement/${idRequirement}/relation/${idSubrequirement}`,
    method: 'POST',
  })
}

export function verifyRelationAllSubrequirements(idForm, idQuestion, idAnswerQuestion, idRequirement) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/answer/${idAnswerQuestion}/requirement/${idRequirement}/verify`,
    method: 'GET',
  })
}

export function setRelationAllSubrequirements(idForm, idQuestion, idAnswerQuestion, idRequirement, data) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/answer/${idAnswerQuestion}/requirement/${idRequirement}/all`,
    method: 'POST',
    data
  })
}