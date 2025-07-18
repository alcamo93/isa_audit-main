<script>
/**
 * Close control 
 */
function closePanel() {
    $('.loading').removeClass('d-none');
    $('.control').addClass('d-none');
    setTimeout(() => {
        reloadActionKeepPage()
        $('.loading').addClass('d-none');
        $('.action').removeClass('d-none');
    }, 1000)
}
/** set value main panel */
function showMainPanel(idActionPlan, section){
    currentAP.idActionPlan = idActionPlan;
    $('.loading').removeClass('d-none');
    $('.action').addClass('d-none');
    getDataActionService()
    .then(data => {
        // Global data Action Plan register
        currentAP.section = section;
        currentAP.realCloseDate = (section == 'action') ? null : data.expired.real_close_date;
        currentAP.idRequirement = data.id_requirement;
        currentAP.noRequirement = data.requirement.no_requirement;
        currentAP.idSubrequirement = data.id_subrequirement;
        currentAP.noSubrequirement = (data.subrequirement == null) ? null : data.subrequirement.no_subrequirement;
        currentAP.status = data.status.status || 'N/A';
        currentAP.complex = (data.complex == 1) ? true : false;
        currentAP.hasSubrequirement = null;//(hasSubrequirement != null) ? true : false;
        currentAP.permission = data.permission;
        // Global data tasks
        currentTask.idActionPlan = data.id_action_plan;
        currentTask.idPeriodCurrent = (data.subrequirement == null) ? data.requirement.id_obtaining_period : data.subrequirement.id_obtaining_period;
        if (tables.tasks == null) tables.tasks = tasksTableInstance();
        else tables.tasks.ajax.reload();
        // btnAdd block and control block task view table
        if (currentAP.section == 'expired') currentAP.blockTask = (data.expired.id_status == 25 || data.expired.id_status == 27);    
        else currentAP.blockTask = (data.id_status == 25 || data.id_status == 27);
        // block create task
        const btnAddTask = document.querySelector('#buttonAddTask');
        if ( currentAP.blockTask || !currentAP.permission ) {
            btnAddTask.style.display = 'none';
            btnAddTask.disabled = true;
        }
        else {
            btnAddTask.style.display = 'block';
            btnAddTask.disabled = false;
        }
        // Headers pages
        $('#sectionTask').html((currentAP.section == 'action') ? 'Tareas' : 'Tareas vencidas');
        $('.titleRequirement').html(currentAP.noRequirement);
        $('.titleStatus').html((currentAP.section == 'action' ) ? currentAP.status : data.expired.status.status);
        if (currentAP.idSubrequirement == null) {
            $('.forSubrequiremenst').html('')
            $('.forSubrequiremenst').addClass('d-none')
        }
        else {
            $('.forSubrequiremenst').html(currentAP.noSubrequirement)
            $('.forSubrequiremenst').removeClass('d-none')
        }
        $('.loading').addClass('d-none');
        $('.control').removeClass('d-none');
    })
    .catch(e => {
        toastAlert(e, 'error');
        $('.loading').addClass('d-none');
        $('.action').removeClass('d-none');
    });
}
</script>