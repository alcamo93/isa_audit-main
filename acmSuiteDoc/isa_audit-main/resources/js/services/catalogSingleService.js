import webInstance from './index'
import queryParams from '../utils/queryParams'

export function getSingleProcess(id, paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/single/process/${id}${query}`,
		method: 'GET',
	})
}
export function getSingleRequirement(id, paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/single/requirement/${id}${query}`,
		method: 'GET',
	})
}
export function getSingleSubrequirement(id, paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/single/subrequirement/${id}${query}`,
		method: 'GET',
	})
}
export function getQuestion(id, paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/single/question/${id}${query}`,
		method: 'GET',
	})
}