import webInstance from './index'

export function storeBackup(data) {
  return webInstance({
    url: '/v2/backup',
    method: 'POST',
    data,
  })
}
export function getBackup(id) {
  return webInstance({
    url: `/v2/backup/${id}`,
    method: 'GET',
  })
}
export function downloadBackup(id){
  return webInstance({
    url: `/v2/backup/${id}/download`,
    method: 'GET',
    responseType: 'blob'
  })
}