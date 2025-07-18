import webInstance from './index'

export function getRiskEvaluate(idAuditProcess, idAplicabilityRegister, sectionKey, idRegisterSection, registerableId) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/aplicability/${idAplicabilityRegister}/risk/${sectionKey}/${idRegisterSection}/evaluate/${registerableId}`,
    method: 'GET',
  })
}
export function setRiskEvaluate(idAuditProcess, idAplicabilityRegister, sectionKey, idRegisterSection, registerableId, data) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/aplicability/${idAplicabilityRegister}/risk/${sectionKey}/${idRegisterSection}/evaluate/${registerableId}`,
    method: 'POST',
    data
  })
}