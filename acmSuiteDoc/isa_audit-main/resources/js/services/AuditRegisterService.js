import webInstance from './index'
import queryParams from '../utils/queryParams'

export function completeAuditRegister(idAuditProcess, idAplicabilityRegister, idAuditRegister) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/${idAuditRegister}/register/complete`,
    method: 'POST'
  })
}

export function getAuditRegister(idAuditProcess, idAplicabilityRegister, idAuditRegister, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/${idAuditRegister}/register${query}`,
    method: 'GET',
  })
}
export function storeAuditRegister(idAuditProcess, idAplicabilityRegister, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/register`,
    method: 'POST',
    data,
  })
}
export function getReportAudit(idAuditProcess, idAplicabilityRegister, idAuditRegister) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/${idAuditRegister}/register/report/audit`,
    method: 'GET',
    responseType: 'blob',
  })
}
export function getDocumentReportAudit(idAuditProcess, idAplicabilityRegister, idAuditRegister) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/${idAuditRegister}/register/report/document`,
    method: 'GET',
    responseType: 'blob',
  })
}
export function getProgressReportAudit(idAuditProcess, idAplicabilityRegister, idAuditRegister) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/${idAuditRegister}/register/report/progress`,
    method: 'GET',
    responseType: 'blob',
  })
}