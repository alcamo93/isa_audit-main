export function getImageItemPath(item, type, usage) {
	
	if (type == 'customer') {
		const { images } = item 
		if ( images.length == 0 ) return null 
		const image = images.find(img => img.usage == usage)
		return image == null ? null : image.full_path	
	}

	if (type == 'corporate') {
		const { image } = item 
		return image == null ? null : image.full_path	
	}

	if (type == 'generic') {
		const full_path = item.image
		return full_path
	}

	return null
}