import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getContractAspectsAll(idAuditProcess, idAplicabilityRegister, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/aspect/all${query}`,
    method: 'GET',
  })
}
export function completeContractAspect(idAuditProcess, idAplicabilityRegister, idContractMatter, idContractAspect) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/matter/${idContractMatter}/aspect/${idContractAspect}/complete`,
    method: 'POST'
  })
}
export function syncContractAspect(idAuditProcess, idAplicabilityRegister, idContractMatter, idContractAspect) {
  return webInstance({
    url: `/v2/process/${idAuditProcess}/applicability/${idAplicabilityRegister}/matter/${idContractMatter}/aspect/${idContractAspect}/sync`,
    method: 'POST'
  })
}