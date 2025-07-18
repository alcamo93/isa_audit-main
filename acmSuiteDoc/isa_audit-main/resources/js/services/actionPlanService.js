import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getActionsMain(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
      url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan${query}`,
      method: 'GET',
  })
} 
export function getActionsExpired(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
      url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/expired${query}`,
      method: 'GET',
  })
}
export function getAction(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
      url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}${query}`,
      method: 'GET',
  })
}
export function updateActionPriority(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/priority`,
    method: 'PUT',
    data,
  })
}
export function updateActionUser(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/user`,
    method: 'PUT',
    data,
  })
}
export function updateActionExpired(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/expired`,
    method: 'PUT',
    data,
  })
}