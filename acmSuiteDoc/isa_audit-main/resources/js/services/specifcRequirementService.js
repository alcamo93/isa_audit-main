import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getSpecificRequirements(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/specific/requirement${query}`,
    method: 'GET',
  })
}

export function storeSpecificRequirement(data) {
  return webInstance({
    url: `/v2/specific/requirement`,
    method: 'POST',
    data,
  })
}

export function getSpecificRequirement(id, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/specific/requirement/${id}${query}`,
    method: 'GET',
  })
}

export function updateSpecificRequirement(id, data) {
  return webInstance({
    url: `/v2/specific/requirement/${id}`,
    method: 'PUT',
    data,
  })
}

export function deleteSpecificRequirement(id) {
  return webInstance({
    url: `/v2/specific/requirement/${id}`,
    method: 'DELETE'
  })
}