import webInstance from './index'
import queryParams from '../utils/queryParams'

// Requirements

export function getQuestionAnswers(idForm, idQuestion, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/answer${query}`,
    method: 'GET',
  })
}

export function setQuestionAnswer(idForm, idQuestion, data) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/answer`,
    method: 'POST',
    data,
  })
}

export function getQuestionAnswer(idForm, idQuestion, idAnswerQuestion, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/answer/${idAnswerQuestion}${query}`,
    method: 'GET',
  })
}

export function updateQuestionAnswer(idForm, idQuestion, idAnswerQuestion, data) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/answer/${idAnswerQuestion}`,
    method: 'PUT',
    data,
  })
}

export function deleteQuestionAnswer(idForm, idQuestion, idAnswerQuestion) {
  return webInstance({
    url: `/v2/catalogs/form/${idForm}/question/${idQuestion}/answer/${idAnswerQuestion}`,
    method: 'DELETE'
  })
}