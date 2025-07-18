import webInstance from './index'

export function storeObligationRegister(idAuditProcess, idAplicabilityRegister, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/obligation`,
    method: 'POST',
    data,
  })
}
export function getReportObligation(idAuditProcess, idAplicabilityRegister, idObligationRegister) {
  return webInstance({
      url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/obligation/${idObligationRegister}/report`,
      method: 'GET',
      responseType: 'blob',
  })
}