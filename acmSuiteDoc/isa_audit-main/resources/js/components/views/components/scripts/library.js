export function getCurrentFile(item) {
  const data = {
    id: item.id_evaluate_requirement,
    has_record: false,
    record: {},
    file_loaded: false,
    library: {},
    librariableType: '',
    librariableId: 0,
    has_section: true,
    permissions: {
      can_approve: false,
      can_upload: false
    }
  }
  if ( Boolean(item.action_plans.length) ) {
    const records = item.action_plans.reduce((result, record) => {
      const tasks = record.tasks.filter(task => Boolean(task.main_task) );
      result.push(...tasks);
      return result;
    }, []);
    const tasksWithLibraries = records.filter(task => task.libraries.length > 0)
    const record = Boolean(tasksWithLibraries.length) ? tasksWithLibraries[0] : records[0]
    data.has_record = true
    data.record = record
    // record library
    data.file_loaded = Boolean(tasksWithLibraries.length)
    data.library = data.file_loaded ? record.libraries[0] : {}
    // librariable
    data.librariableType = record !== null ? 'Task' : ''
    data.librariableId = record !== null ? record.id_task : 0
    // permissions
    data.permissions.can_approve = record.permissions.can_approve
    data.permissions.can_upload = record.permissions.can_upload
    return data
  } 
  if ( Boolean(item.obligations.length) ) {
    const records = item.obligations
    const obligationsWithLibraries = records.filter(task => task.libraries.length > 0)
    const record = Boolean(obligationsWithLibraries.length) ? obligationsWithLibraries[0] : records[0]
    data.has_record = true
    data.record = record
    // record library
    data.file_loaded = Boolean(obligationsWithLibraries.length)
    data.library = data.file_loaded ? record.libraries[0] : {}
    // librariable
    data.librariableType = record !== null ? 'Obligation' : ''
    data.librariableId = record !== null ? record.id_obligation : 0
    // permissions
    data.permissions.can_approve = record.permissions.can_approve
    data.permissions.can_upload = record.permissions.can_upload
    return data
  }
  data.has_section = false
  return data
}