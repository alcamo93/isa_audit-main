import webInstance from './index'
import queryParams from '../utils/queryParams'

// Services risk categories
export function getRiskCategories(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
      url: `/v2/catalogs/risk${query}`,
      method: 'GET',
  })
}
export function storeRiskCategory(data) {
  return webInstance({
    url: '/v2/catalogs/risk',
    method: 'POST',
    data,
  })
}
export function getRiskCategory(id, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/risk/${id}${query}`,
    method: 'GET',
  })
}
export function updateRiskCategory(id, data) {
  return webInstance({
    url: `/v2/catalogs/risk/${id}`,
    method: 'PUT',
    data,
  })
}
export function deleteRiskCategory(id) {
  return webInstance({
    url: `/v2/catalogs/risk/${id}`,
    method: 'DELETE'
  })
}

// Services risk interpretations
export function getRiskInterpretations(idRiskCategory, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
      url: `/v2/catalogs/risk/${idRiskCategory}/interpretation${query}`,
      method: 'GET',
  })
}
export function storeRiskInterpretation(idRiskCategory, data) {
  return webInstance({
    url: `/v2/catalogs/risk/${idRiskCategory}/interpretation`,
    method: 'POST',
    data,
  })
}

// Services risk helps
export function getRiskHelps(idRiskCategory, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/catalogs/risk/${idRiskCategory}/help${query}`,
    method: 'GET',
  })
}
export function storeRiskHelp(idRiskCategory, data) {
  return webInstance({
    url: `/v2/catalogs/risk/${idRiskCategory}/help`,
    method: 'POST',
    data,
  })
}
export function getRiskHelp(idRiskCategory, id, data) {
  return webInstance({
    url: `/v2/catalogs/risk/${idRiskCategory}/help/${id}`,
    method: 'GET',
    data,
  })
}
export function updateRiskHelp(idRiskCategory, id, data) {
  return webInstance({
    url: `/v2/catalogs/risk/${idRiskCategory}/help/${id}`,
    method: 'PUT',
    data,
  })
}