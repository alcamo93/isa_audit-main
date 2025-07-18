import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getAnswerQuestionDependency(idForm, idQuestion, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/dependency/records${query}`,
    method: 'GET',
  })
}

export function setAnswerQuestionDependency(idForm, idQuestion, idAnswerQuestion, idQuestionBlock) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/answer/${idAnswerQuestion}/dependency/${idQuestionBlock}`,
    method: 'POST'
  })
}

export function removeAnswerQuestionDependency(idForm, idQuestion) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/dependency/remove`,
    method: 'DELETE',
  })
}