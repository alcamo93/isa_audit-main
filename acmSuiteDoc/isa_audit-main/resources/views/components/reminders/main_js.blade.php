<script>
const lenguageFlatpickr = {
    firstDayOfWeek: 1,
    weekdays: {
        shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],         
    }, 
    months: {
        shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Оct', 'Nov', 'Dic'],
        longhand: ['Enero', 'Febreo', 'Мarzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    },
}
/**
 * Handler submit dates reminders
 */
function setDatesReminder(idActionPlan, idObligation, idTask, dates, typeDate){
    let datesSend = [];
    dates.forEach(d => {
        let dateTemp = formatDate(d);
        datesSend.push(dateTemp);
    });
    $.post('/action/dates/reminders', {
        '_token': '{{ csrf_token() }}',
        dates: datesSend,
        idActionPlan: idActionPlan,
        idObligation: idObligation,
        idTask: idTask,
        typeDate: typeDate
    },
    (data) => {
        toastAlert(data.msg, data.status);
    });
}
/**
 * Set format dates
 */
function formatDate(date) {
    let d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;
    return [year, month, day].join('-')+' 00:00:00';
}
</script>