import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getKnowledgeTopic(id, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/knowledge/topic/${id}${query}`,
    method: 'GET',
  })
}