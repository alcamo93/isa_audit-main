import webInstance from './index'

export function getReportGlobalObligations(idCustomer) {
  return webInstance({
    url: `/v2/dashboard/customers/${idCustomer}/report/obligation`,
    method: 'GET',
    responseType: 'blob',
  });
}
export function getReportGlobalAudit(idCustomer) {
  return webInstance({
    url: `/v2/dashboard/customers/${idCustomer}/report/audit`,
    method: 'GET',
    responseType: 'blob',
  });
}
export function getReportGlobalCompliance(idCustomer) {
  return webInstance({
    url: `/v2/dashboard/customers/${idCustomer}/report/compliance`,
    method: 'GET',
    responseType: 'blob',
  });
}
export function getReportCorporateObligation(idAuditProcess, idAplicabilityRegister, idObligationRegister) {
  return webInstance({
    url: `/v2/dashboard/corporate/project/${idAuditProcess}/applicability/${idAplicabilityRegister}/report/obligation/${idObligationRegister}`,
    method: 'GET',
    responseType: 'blob',
  });
}

export function getReportCorporateAudit(idAuditProcess, idAplicabilityRegister, idAuditRegister) {
  return webInstance({
    url: `/v2/dashboard/corporate/project/${idAuditProcess}/applicability/${idAplicabilityRegister}/report/audit/${idAuditRegister}`,
    method: 'GET',
    responseType: 'blob',
  });
}

export function getReportCorporateCompliance(idAuditProcess, idAplicabilityRegister, idAuditRegister) {
  return webInstance({
    url: `/v2/dashboard/corporate/project/${idAuditProcess}/applicability/${idAplicabilityRegister}/report/compliance/${idAuditRegister}`,
    method: 'GET',
    responseType: 'blob',
  });
}

