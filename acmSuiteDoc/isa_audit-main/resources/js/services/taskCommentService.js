import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getTaskComments(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/task/${idTask}/comment${query}`,
    method: 'GET',
  })
}
export function getTaskComment(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask, idComment, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
      url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/task/${idTask}/comment/${idComment}${query}`,
      method: 'GET',
  })
}
export function setTaskComment(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/task/${idTask}/comment`,
    method: 'POST',
    data,
  })
}
export function updateTaskComment(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask, idComment, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/task/${idTask}/comment/${idComment}`,
    method: 'PUT',
    data,
  })
}
export function deleteTaskComment(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask, idComment) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/task/${idTask}/comment/${idComment}`,
    method: 'DELETE'
  })
}