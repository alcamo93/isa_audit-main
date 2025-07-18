import webInstance from './index'
import queryParams from '../utils/queryParams'

export function storeActionRegister(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action`,
    method: 'POST',
    data,
  })
}
export function getActionRegister(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, paramsObj = {}) {
  const query = queryParams.serializeParams(paramsObj)
  return webInstance({
      url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}${query}`,
      method: 'GET',
  })
}
export function getActionPlanReport(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, filtersObj = {}) {
  const query = queryParams.serializeParams({}, filtersObj)
  return webInstance({
      url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/report/plan${query}`,
      method: 'GET',
      responseType: 'blob',
  })
}