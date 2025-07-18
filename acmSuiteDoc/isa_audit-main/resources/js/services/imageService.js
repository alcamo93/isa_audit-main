import webInstance from './index'

export function getImage(id) {
  return webInstance({
    url: `/v2/image/${id}`,
    method: 'GET',
  })
}
export function storeImage(data) {
  return webInstance({
    url: '/v2/image',
    method: 'POST',
    data,
  })
}
export function getContentImage(id) {
  return webInstance({
    url: `/v2/image/download/${id}`,
    method: 'GET',
    responseType: 'blob'
  })
}
export function deleteImage(id) {
  return webInstance({
    url: `/v2/image/${id}`,
    method: 'DELETE',
  })
}