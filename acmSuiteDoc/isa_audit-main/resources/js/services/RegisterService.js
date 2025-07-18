import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getRegisters(paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)

  return webInstance({
      url: `/v2/catalogs/register${query}`,
      method: 'GET',
  })
}

export function storeRegister(data) {
  return webInstance({
      url: '/v2/catalogs/register',
      method: 'POST',
      data,
  })
}

export function getRegister(id, paramsObj = {}, filtersObj = {}) {
  const query = queryParams.serializeParams(paramsObj, filtersObj)
  return webInstance({
      url: `/v2/catalogs/register/${id}${query}`,
      method: 'GET',
  })
}

export function updateRegister(id, data) {
  return webInstance({
      url: `/v2/catalogs/register/${id}`,
      method: 'PUT',
      data,
  })
}

export function deleteRegister(id) {
  return webInstance({
      url: `/v2/catalogs/register/${id}`,
      method: 'DELETE'
  })
}

export function changeCurrent(id, data) {
  return webInstance({
      url: `/v2/catalogs/register/${id}/change-current`,
      method: 'PUT',
      data,
  })
}