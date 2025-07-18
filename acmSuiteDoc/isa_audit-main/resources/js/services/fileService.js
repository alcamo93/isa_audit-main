import webInstance from './index'
import queryParams from '../utils/queryParams'

export function storeFile(data) {
  return webInstance({
    url: '/v2/file',
    method: 'POST',
    data,
  })
}
export function getContentFile(id) {
  return webInstance({
    url: `/v2/file/download/${id}`,
    method: 'GET',
    responseType: 'blob'
  })
}
export function getContentFileBase(id) {
  return webInstance({
    url: `/v2/file/download/base/${id}`,
    method: 'GET',
  })
}