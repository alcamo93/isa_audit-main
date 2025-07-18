import webInstance from './index'
import queryParams from '../utils/queryParams'

/* -------------------------------------------------- matters */
export function getMatters(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/matter${query}`,
		method: 'GET',
	})    
}
/* -------------------------------------------------- aspects */
export function getAspects(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/aspect${query}`,
		method: 'GET',
	})
}
/* -------------------------------------------------- evaluation_type */
export function getEvaluationTypes() {
	return webInstance({
		url: `/v2/source/evaluation`,
		method: 'GET',
	})
}
/* -------------------------------------------------- scopes */
export function getScopes() {
	return webInstance({
		url: `/v2/source/scope`,
		method: 'GET',
	})
}
/* -------------------------------------------------- status */
export function getStatus(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/status${query}`,
		method: 'GET',
	})
}
/* -------------------------------------------------- priorities */
export function getPriorities(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/priority${query}`,
		method: 'GET',
	})
}
/* -------------------------------------------------- categories */
export function getCategories(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/category${query}`,
		method: 'GET',
	})
}
/* -------------------------------------------------- conditions */
export function getConditions(paramsObj = {}, filtersObj = {}) {
    const query = queryParams.serializeParams(paramsObj, filtersObj)
    return webInstance({
        url: `/v2/source/condition${query}`,
        method: 'GET',
    })
}
/* -------------------------------------------------- Industries */
export function getIndustries(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/industry${query}`,
		method: 'GET',
	})
}
export function getCountries(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/country${query}`,
		method: 'GET',
	})
}
export function getStates(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/state${query}`,
		method: 'GET',
	})
}
export function getCities(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/city${query}`,
		method: 'GET',
	})
}
export function getProfiles(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/profile${query}`,
		method: 'GET',
	})
}
export function getProfileTypes(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/profile_type${query}`,
		method: 'GET',
	})
}
export function getLicenses(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/license${query}`,
		method: 'GET',
	})
}
export function getPeriods(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/period${query}`,
		method: 'GET',
	})
}
export function getApplicationTypes(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/application_type${query}`,
		method: 'GET',
	})
}
export function getApplicationTypesSpecific(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/application_type/specific${query}`,
		method: 'GET',
	})
}
export function getLegalClassifications(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/legal_classification${query}`,
		method: 'GET',
	})
}
export function getRequirementTypes(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/requirement_type${query}`,
		method: 'GET',
	})
}
export function getEvidences(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/evidence${query}`,
		method: 'GET',
	})
}
export function getQuestionTypes(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/question_type${query}`,
		method: 'GET',
	})
}
export function getAnswerValue(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/answer_value${query}`,
		method: 'GET',
	})
}
export function getAuditsSource(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/audit${query}`,
		method: 'GET',
	})
}
export function getCustomersSource(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/customer${query}`,
		method: 'GET',
	})
}
export function getCorporatesSource(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/corporate${query}`,
		method: 'GET',
	})
}
export function getUsersSource(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/user${query}`,
		method: 'GET',
	})
}
export function getPeriodicities(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/periodicity${query}`,
		method: 'GET',
	})
}
export function getFormsSource(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/form${query}`,
		method: 'GET',
	})
}
export function getCategoryRisksSource(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/risk${query}`,
	})
}
export function getProcessSource(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/process${query}`,
		method: 'GET',
	})
}
export function getRiskAttributesSource(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/risk_attribute${query}`,
	})
}
export function getTopicsSource(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/topic${query}`,
		method: 'GET',
	})
}
export function getGuidelineSource(paramsObj = {}, filtersObj = {}) {
	const query = queryParams.serializeParams(paramsObj, filtersObj)
	return webInstance({
		url: `/v2/source/guideline${query}`,
		method: 'GET',
	})
}