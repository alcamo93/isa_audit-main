import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getAplicabilityRegister(idAuditProcess, idAplicabilityRegister, params = {}) {
  const query = queryParams.serializeParams(params)
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/register${query}`,
    method: 'GET',
  })
}
export function getShareByAplicabilityRegister(idAuditProcess, idAplicabilityRegister, params = {}, filters = {}) {
  const query = queryParams.serializeParams(params, filters)
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/register${query}&option=share`,
    method: 'GET',
  })
}
export function completeAplicabilityRegister(idAuditProcess, idAplicabilityRegister) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/register/complete`,
    method: 'POST'
  })
}
export function getAnswersReportApplicability(idAuditProcess, idAplicabilityRegister) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/register/report/answers`,
    method: 'GET',
    responseType: 'blob',
  })
}
export function getResultsReportApplicability(idAuditProcess, idAplicabilityRegister) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/register/report/results`,
    method: 'GET',
    responseType: 'blob',
  })
}