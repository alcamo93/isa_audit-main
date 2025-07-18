export default {
	serializeParams(paramsObj = {}, filtersObj = {}) {
		const removeEmpty = obj => {
			Object.keys(obj).forEach(k => (!obj[k] && obj[k] !== undefined) && delete obj[k])
			return obj
		}
		const sanitizedFilter = removeEmpty(filtersObj)
		const sanitizedParameters = removeEmpty(paramsObj)

		const stringParams = Object.keys(sanitizedParameters)
			.map(key => `${key}=${sanitizedParameters[key]}`)
			.join('&')
	
		const stringFilters = Object.keys(sanitizedFilter)
			.map(key => `filters[${key}]=${sanitizedFilter[key]}`)
			.join('&')

		const queryParams = stringParams.length != 0 ? `?${stringParams}` : ''
		const contactQueries = queryParams.length == 0 ? '?' : '&'
		const queryFilters = stringFilters.length != 0 ? `${contactQueries}${stringFilters}` : ''
		const query = `${queryParams}${queryFilters}`

		return query
	}
}
