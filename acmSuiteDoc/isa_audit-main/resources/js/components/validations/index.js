import es from 'vee-validate/dist/locale/es';
import { localize, extend } from 'vee-validate'
import {
  required as rule_required,
  email as rule_email,
  min as rule_min,
  max as rule_max,
  confirmed as rule_confirmed,
  regex as rule_regex,
  between as rule_between,
  alpha as rule_alpha,
  integer as rule_integer,
  digits as rule_digits,
  alpha_dash as rule_alpha_dash,
  alpha_num as rule_alpha_num,
  length as rule_length,
  max_value as rule_max_value,
  min_value as rule_min_value,
  required_if as rule_required_if,
  numeric as rule_numeric,
  double as rule_double
} from 'vee-validate/dist/rules'
import { validator_password } from './validatiors'

localize('es', es);
/* Exportando reglas generales */
export const required = extend('required', rule_required)

export const email = extend('email', rule_email)

export const min = extend('min', rule_min)
export const max = extend('max', rule_max)

export const confirmed = extend('confirmed', rule_confirmed)

export const regex = extend('regex', rule_regex)

export const between = extend('between', rule_between)

export const alpha = extend('alpha', rule_alpha)

export const integer = extend('integer', rule_integer)

export const digits = extend('digits', rule_digits)

export const alpha_dash = extend('alpha_dash', rule_alpha_dash)

export const alpha_num = extend('alpha_num', rule_alpha_num)

export const length = extend('length', rule_length)

export const min_value = extend('min_value', rule_min_value)

export const max_value = extend('max_value', rule_max_value)

export const required_if = extend('required_if', rule_required_if)

export const numeric = extend('numeric', rule_numeric)

export const password = extend('password', validator_password)

export const double = extend('double', rule_double)