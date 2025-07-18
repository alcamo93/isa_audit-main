import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getCustomers(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/dashboard/customers${query}`,
    method: 'GET',
  })
}

export function getCorporatesByCustomer(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/dashboard/corporates${query}`,
    method: 'GET',
  })
}

export function getCorporates(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/dashboard/corporate/only${query}`,
    method: 'GET',
  })
}

export function getCorporateGlobal(idAuditProcess, idAplicabilityRegister) {
  return webInstance({
    url: `/v2/dashboard/corporate/project/${idAuditProcess}/applicability/${idAplicabilityRegister}/all`,
    method: 'GET',
  });
}

export function getCustomerGlobalObligation(idCustomer) {
  return webInstance({
    url: `/v2/dashboard/customers/${idCustomer}/obligations`,
    method: 'GET',
  });
}

export function getCustomerGlobalObligationHistorical(idCustomer) {
  return webInstance({
    url: `/v2/dashboard/customers/${idCustomer}/obligations/historical`,
    method: 'GET',
  });
}

export function getCustomerGlobalAudit(idCustomer) {
  return webInstance({
    url: `/v2/dashboard/customers/${idCustomer}/audit`,
    method: 'GET',
  });
}

export function getCustomerGlobalAuditHistorical(idCustomer) {
  return webInstance({
    url: `/v2/dashboard/customers/${idCustomer}/audit/historical`,
    method: 'GET',
  });
}

export function getCustomerGlobalCompliance(idCustomer) {
  return webInstance({
    url: `/v2/dashboard/customers/${idCustomer}/compliance`,
    method: 'GET',
  });
}

export function getCustomerGlobalComplianceHistorical(idCustomer) {
  return webInstance({
    url: `/v2/dashboard/customers/${idCustomer}/compliance/historical`,
    method: 'GET',
  });
}

export function getCorporateObligation(idAuditProcess, idAplicabilityRegister) {
  return webInstance({
    url: `/v2/dashboard/corporate/project/${idAuditProcess}/applicability/${idAplicabilityRegister}/obligations`,
    method: 'GET',
  });
}

export function getCorporateObligationActionPlan(idAuditProcess, idAplicabilityRegister) {
  return webInstance({
    url: `/v2/dashboard/corporate/project/${idAuditProcess}/applicability/${idAplicabilityRegister}/obligations/actions`,
    method: 'GET',
  });
}

export function getDataAuditCorporate(idAuditProcess, idAplicabilityRegister, idAuditRegister) {
  return webInstance({
    url: `/v2/dashboard/corporate/project/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/${idAuditRegister}`,
    method: 'GET',
  });
}

export function getDataAuditActionCorporate(idAuditProcess, idAplicabilityRegister, idAuditRegister) {
  return webInstance({
    url: `/v2/dashboard/corporate/project/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/${idAuditRegister}/actions`,
    method: 'GET',
  });
}

export function getDataComplianceCorporate(idAuditProcess, idAplicabilityRegister, idAuditRegister) {
  return webInstance({
    url: `/v2/dashboard/corporate/project/${idAuditProcess}/applicability/${idAplicabilityRegister}/compliance/${idAuditRegister}`,
    method: 'GET',
  });
}

export function getDataComplianceActionCorporate(idAuditProcess, idAplicabilityRegister, idAuditRegister) {
  return webInstance({
    url: `/v2/dashboard/corporate/project/${idAuditProcess}/applicability/${idAplicabilityRegister}/compliance/${idAuditRegister}/actions`,
    method: 'GET',
  });
}

export function getRecordsDashboardObligation(idAuditProcess, idAplicabilityRegister, idObligationRegister, params, filters) {
  const query = queryParams.serializeParams(params, filters)
  return webInstance({
    url: `/v2/dashboard/corporate/project/${idAuditProcess}/applicability/${idAplicabilityRegister}/obligation/${idObligationRegister}/record${query}`,
    method: 'GET',
  });
}

export function getRecordsDashboardAudit(idAuditProcess, idAplicabilityRegister, idAuditRegister, params, filters) {
  const query = queryParams.serializeParams(params, filters)
  return webInstance({
    url: `/v2/dashboard/corporate/project/${idAuditProcess}/applicability/${idAplicabilityRegister}/audit/${idAuditRegister}/record${query}`,
    method: 'GET',
  });
}

export function getRecordsDashboardCompliance(idAuditProcess, idAplicabilityRegister, idAuditRegister, params, filters) {
  const query = queryParams.serializeParams(params, filters)
  return webInstance({
    url: `/v2/dashboard/corporate/project/${idAuditProcess}/applicability/${idAplicabilityRegister}/compliance/${idAuditRegister}/record${query}`,
    method: 'GET',
  });
}

export function getRecordsDashboardActionPlan(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, params, filters) {
  const query = queryParams.serializeParams(params, filters)
  return webInstance({
    url: `/v2/dashboard/corporate/project/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/record${query}`,
    method: 'GET'
  })
}