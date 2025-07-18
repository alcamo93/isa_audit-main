import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getTasks(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
      url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/task${query}`,
      method: 'GET',
  })
}
export function getTask(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
      url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/task/${idTask}${query}`,
      method: 'GET',
  })
}
export function setTask(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/task`,
    method: 'POST',
    data,
  })
}
export function updateTask(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/task/${idTask}`,
    method: 'PUT',
    data,
  })
}
export function updateMainTask(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/task/${idTask}/main`,
    method: 'PUT',
    data,
  })
}
export function deleteTask(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask) {
  return webInstance({
      url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/task/${idTask}`,
      method: 'DELETE'
  })
}
export function getTasksFiles(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
      url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/task${query}/file`,
      method: 'GET',
  })
}
export function updateTaskStatus(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/task/${idTask}${query}/status`,
    method: 'PUT',
    data,
  })
}
export function updateTaskExpired(idAuditProcess, idAplicabilityRegister, section, idSectionRegister, idActionRegister, idActionPlan, idTask, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/${section}/${idSectionRegister}/action/${idActionRegister}/plan/${idActionPlan}/task/${idTask}/expired`,
    method: 'PUT',
    data,
  })
}