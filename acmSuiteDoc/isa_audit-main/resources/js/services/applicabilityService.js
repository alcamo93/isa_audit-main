import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getAplicability(idAuditProcess, idAplicabilityRegister, idContractMatter, idContractAspect, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/matter/${idContractMatter}/aspect/${idContractAspect}/evaluate${query}`,
    method: 'GET',
  })
}
export function setAnswerValue(idAuditProcess, idAplicabilityRegister, idContractMatter, idContractAspect, idEvaluateQuestion, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/matter/${idContractMatter}/aspect/${idContractAspect}/evaluate/${idEvaluateQuestion}/answer`,
    method: 'POST',
    data: data,
  })
}
export function setComment(idAuditProcess, idAplicabilityRegister, idContractMatter, idContractAspect, idEvaluateQuestion, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/matter/${idContractMatter}/aspect/${idContractAspect}/evaluate/${idEvaluateQuestion}/comment`,
    method: 'POST',
    data: data,
  })
}