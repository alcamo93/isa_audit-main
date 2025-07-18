import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getGuidelines(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/source/guideline${query}`,
    method: 'GET',
  })
}