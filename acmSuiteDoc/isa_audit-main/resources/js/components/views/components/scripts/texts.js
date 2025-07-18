export function groupItems(items) {
  let groupList = []
  // group by Aspect
  const uniqueAspect =  [...new Set(items.map(item => item.requirement.aspect.id_aspect))]
  const groupAspect = uniqueAspect.map(e => items.filter(i => e == i.requirement.aspect.id_aspect))
  // group by Requirement
  groupAspect.forEach(aspect => {
    const uniqueReq = [...new Set(aspect.map(item => item.requirement.no_requirement))]
    const tmpGgroupReq = uniqueReq.map(e => aspect.filter(i => e == i.requirement.no_requirement) )
    groupList.push(tmpGgroupReq)
  })
  return groupList
}
export function getLabelGroup(data, level) {
  if (level == 'aspect') return `Aspecto: ${data[0][0].requirement.aspect.aspect}`
  if (level == 'requirement') return `${data[0].requirement.no_requirement}: ${data[0].requirement.requirement}`
}
export function getNoRequirementText(row) {
  const { subrequirement, requirement } = row
  const name = (subrequirement != null) ? subrequirement.no_subrequirement : requirement.no_requirement
  return name
}
export function getRequirementText(row) {
  const { subrequirement, requirement } = row
  const text = (subrequirement != null) ? subrequirement.subrequirement : requirement.requirement
  return text
}
export function getKeyGroupObligation(data, level) {
  const random = Math.random()
  if (level == 'aspect') return `${data[0][0].requirement.aspect.id_aspect}_${random}`
  if (level == 'requirement') return data.id_obligation
}
export function getKeyGroupAction(data, level) {
  const random = Math.random()
  if (level == 'aspect') return `${data[0][0].requirement.aspect.id_aspect}_${random}`
  if (level == 'requirement') return data.id_action_plan
}
export function getKeyGroupLibrary(data, level) {
  const random = Math.random()
  if (level == 'aspect') return `${data[0][0].requirement.aspect.id_aspect}_${random}`
  if (level == 'requirement') return data.id_evaluate_requirement
}
export function truncateText(textTrucate, defaultText, limit = 17) {
  const text = textTrucate || defaultText
  const stringTruncate = (text.length > limit) ? `${text.substr(0, limit)}…` : text
  return stringTruncate
}
export function getTextTypeTask(data) {
  const { main_task } = data
  return main_task ? 'Tarea' : 'Subtarea'
}
export function getFieldRequirement(row, field) {
  const { subrequirement, requirement } = row
  const item = (subrequirement != null) ? subrequirement : requirement
  let str = '----'
  if (field == 'evidence' && item.evidence != null) {
    str = `${item.evidence.evidence}`
  }
  if (field == 'only_evidence' && item.evidence != null) {
    str = item.document != null ? item.document : ''
  }
  if (field == 'condition' && item.condition != null) {
    str = (subrequirement != null) ? subrequirement.condition.condition : requirement.condition.condition
  }
  if (field == 'matter' && item.matter != null) {
    str = (subrequirement != null) ? subrequirement.matter.matter : requirement.matter.matter
  }
  if (field == 'aspect' && item.aspect != null) {
    str = (subrequirement != null) ? subrequirement.aspect.aspect : requirement.aspect.aspect
  }
  if (field == 'help') {
    str = (subrequirement != null) ? subrequirement.help_subrequirement : requirement.help_requirement
  }
  if (field == 'document') {
    str = (subrequirement != null) ? subrequirement.document : requirement.document
  }
  if (field == 'description') {
    str = (subrequirement != null) ? subrequirement.description : requirement.description
  }
  if (field == 'application_type') {
    str = (subrequirement != null) ? subrequirement.application_type.application_type : requirement.application_type.application_type
  }
  return str
}