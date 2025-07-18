import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getObligations(idAuditProcess, idAplicabilityRegister, idObligationRegister, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
      url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/obligation/${idObligationRegister}${query}`,
      method: 'GET',
  })
}
export function storeObligation(idAuditProcess, idAplicabilityRegister, idObligationRegister, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/obligation/${idObligationRegister}`,
    method: 'POST',
    data,
  })
}
export function getObligation(idAuditProcess, idAplicabilityRegister, idObligationRegister, id, paramsObj = {}) {
  const query = queryParams.serializeParams(paramsObj)
  return webInstance({
      url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/obligation/${idObligationRegister}/record/${id}${query}`,
      method: 'GET',
  })
}
export function updateObligationUser(idAuditProcess, idAplicabilityRegister, idObligationRegister, id, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/obligation/${idObligationRegister}/record/${id}/user`,
    method: 'PUT',
    data,
  })
}
export function updateObligationHasFile(idAuditProcess, idAplicabilityRegister, idObligationRegister, id, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/obligation/${idObligationRegister}/record/${id}/file`,
    method: 'PUT',
    data,
  })
}
export function deleteObligation(idAuditProcess, idAplicabilityRegister, idObligationRegister, id) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/obligation/${idObligationRegister}/record/${id}`,
    method: 'DELETE'
  })
}