const validatorPassword = password => {
	// At least 2 numbers
	if ((password.match(/\d/g) || []).length < 2) return false
	// At least 2 uppercase letters
	if ((password.match(/[A-Z]/g) || []).length < 2) return false
	// At least 2 lowercase letters
	if ((password.match(/[a-z]/g) || []).length < 2) return false 
	// At least one special character
	if (!/[\W_]/.test(password)) return false
	// success
	return true
}

export const validator_password = { 
	validate: validatorPassword,  
	message: 'Debe contener al menos 2 números, 2 mayúsculas, 2 minúsculas y al menos 1 carácter especial'
}



