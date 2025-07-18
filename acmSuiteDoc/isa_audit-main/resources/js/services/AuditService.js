import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getAudits(idAuditProcess, idAplicabilityRegister, idAuditRegister, idAuditMatter, idAuditAspect, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/${idAuditRegister}/matter/${idAuditMatter}/aspect/${idAuditAspect}/evaluate${query}`,
    method: 'GET',
  })
}
export function setAuditAnswer(idAuditProcess, idAplicabilityRegister, idAuditRegister, idAuditMatter, idAuditAspect, id, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/${idAuditRegister}/matter/${idAuditMatter}/aspect/${idAuditAspect}/evaluate/${id}/answer`,
    method: 'POST',
    data
  })
}
export function getAudit(idAuditProcess, idAplicabilityRegister, idAuditRegister, idAuditMatter, idAuditAspect, id, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/${idAuditRegister}/matter/${idAuditMatter}/aspect/${idAuditAspect}/evaluate/${id}${query}`,
    method: 'GET',
  })
}
export function setAuditFinding(idAuditProcess, idAplicabilityRegister, idAuditRegister, idAuditMatter, idAuditAspect, id, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/${idAuditRegister}/matter/${idAuditMatter}/aspect/${idAuditAspect}/evaluate/${id}/finding`,
    method: 'POST',
    data
  })
}
export function setImages(idAuditProcess, idAplicabilityRegister, idAuditRegister, idAuditMatter, idAuditAspect, id, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/${idAuditRegister}/matter/${idAuditMatter}/aspect/${idAuditAspect}/evaluate/${id}/images`,
    method: 'POST',
    data
  })
}