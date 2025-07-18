import moment from 'moment'

export function endDateFuture(initDate) {
  const months = 12 // end_date is 1 year
  const date = new Date(initDate)
  date.setMonth(date.getMonth() + months)
  const month = date.getMonth() + 1
  const day = date.getDate()
  const formatDay = (day + 1) <= 9 ? `0${day}` : day
  const formatMonth = month <= 9 ? `0${month}` : month
  return `${date.getFullYear()}-${formatMonth}-${formatDay}`
}
export function diffDays(init_date, end_date) {
  if (init_date == null || end_date == null) return null 
  const initDate = moment(init_date)
  const endDate = moment(end_date)
  const calc = endDate.diff(initDate, 'days')
  return calc
}
export function dateFuture(initDate, numberPeriod, unitPeriodKey) {
  const date = new Date(initDate.split(' ')[0])
  if (unitPeriodKey == 'days') {
    date.setDate(date.getDate() + numberPeriod) 
  }
  if (unitPeriodKey == 'months') {
    date.setMonth(date.getMonth() + numberPeriod) 
  }
  if (unitPeriodKey == 'years') {
    date.setFullYear(date.getFullYear() + numberPeriod) 
  }
  const month = date.getMonth() + 1
  const day = date.getDate()
  const formatDay = day <= 9 ? `0${day}` : day
  const formatMonth = month <= 9 ? `0${month}` : month
  return `${date.getFullYear()}-${formatMonth}-${formatDay}`
}
export function subtractDays(numDays, date) {
  const formdate = moment(date)
  const calc = formdate.subtract(numDays, 'days')
  return calc
}
export function todayDate() {
  const date = new Date()
  const month = date.getMonth() + 1
  const day = date.getDate()
  const formatDay = (day + 1) <= 9 ? `0${day}` : day
  const formatMonth = month <= 9 ? `0${month}` : month
  return `${date.getFullYear()}-${formatMonth}-${formatDay}`
}