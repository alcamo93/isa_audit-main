import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getAuditAspectsAll(idAuditProcess, idAplicabilityRegister, idAuditRegister, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/${idAuditRegister}/aspect/all${query}`,
    method: 'GET',
  })
}
export function getAuditAspect(idAuditProcess, idAplicabilityRegister, idAuditRegister, idAuditMatter) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/${idAuditRegister}/matter/${idAuditMatter}/aspect${query}`,
    method: 'GET'
  })
}
export function completeAuditAspect(idAuditProcess, idAplicabilityRegister, idAuditRegister, idAuditMatter, idAuditAspect) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/${idAuditRegister}/matter/${idAuditMatter}/aspect/${idAuditAspect}/complete`,
    method: 'POST'
  })
}