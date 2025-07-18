export const allOrigins = (origin) => {
  const options = {
    audit: 'Auditoría',
    obligation: 'Permisos Críticos',
  }

  return options[origin] ?? '?'
}