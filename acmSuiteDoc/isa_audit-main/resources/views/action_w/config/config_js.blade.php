<script>



// /*************** Assigned users tasks ***************/

// /*
//  * Asigned User Task
//  */
// function asignedUserTask(idTask, name, idActionPlan){
//     currentTask.idActionPlan = idActionPlan;
//     $.get('/action/task/data', {
//         _token: document.querySelector('meta[name="csrf-token"]').content,
//         idActionPlan: idActionPlan,
//         idTask: idTask
//     },
//     (data) => {
//         if (data.users.length > 0) {
//             html = '';
//             data.users.forEach(element => {
//                 html += `<option value="${element.id_user}">${element.complete_name}</option>`;
//             });
//         }else{
//             html = `<option value="">No cuenta con Usuarios</option>`;
//         }
//         document.querySelector('#s-idUserTask').innerHTML = html;
//         document.querySelector('#s-idUserTask').value = data.task.id_user_asigned;
//         document.querySelector('#s-idTask').value = idTask;

        
//     })
//     .fail((e)=>{
//         toastAlert(e.statusText, 'error');
//     });
//     document.querySelector('#asignedTaskForm').reset();
//     $('#asignedTaskForm').validate().resetForm();
//     $('#asignedTaskForm').find(".error").removeClass("error");
//     $('#asignedTaskModal').modal({backdrop:'static', keyboard: false});
//     document.querySelector('#asignedTaskTitle').innerHTML = `Asignación de Responsable: ${name}`;
// }

// /**
//  * Handler to submit assigned user
//  */
// $('#asignedTaskForm').submit( (event) => {
//     event.preventDefault() 
//     if($('#asignedTaskForm').valid()) {
//         showLoading('#asignedTaskModal')
//         //handler notificaction
//         $.post('/action/tasks/users/assigned/set', {
//             _token: document.querySelector('meta[name="csrf-token"]').content,
//             idActionPlan: currentTask.idActionPlan,
//             idTask: document.querySelector('#s-idTask').value,
//             idUser: document.querySelector('#s-idUserTask').value
//         },
//         (data) => {
//             showLoading('#asignedTaskModal')
//             toastAlert(data.msg, data.status);
//             if (data.status == 'success') {
//                 reloadTasksKeepPage();
//                 reloadSubActionKeepPage();
//                 reloadActionKeepPage();
//                 $('#asignedTaskModal').modal('hide');
//             }
//         })
//         .fail((e)=>{
//             toastAlert(e.statusText, 'error');
//         });
//     }
// });

// /*************** Assigned real close date ***************/

// /**
//  * Assigned real close date modal
//  */
// function asignedDate(idActionPlan, name){
//     $('#rowRealCloseDate').addClass('d-none');
//     $('.requestClass').addClass('d-none');
//     $('.pendientClass').addClass('d-none');
//     document.querySelector('#dateIdActionPlan').value = idActionPlan;
//     $.get('/action/data', {
//         _token: document.querySelector('meta[name="csrf-token"]').content,
//         idActionPlan: idActionPlan
//     },
//     (data) => {
//         if ( (data.action.closeDate === null) || (TODAY <= data.action.closeDate) ) {
//             document.querySelector('#s-closeDate').min = data.limits.initDate;
//             document.querySelector('#s-closeDate').max = data.limits.closeDate;
//             document.querySelector('#s-closeDate').value = data.action.closeDate;
//         }
//         else if ( (TODAY > data.action.closeDate) && (data.action.permit == 0) ) {
//             document.querySelector('#s-closeDate').min = data.limits.initDate;
//             document.querySelector('#s-closeDate').max = data.limits.closeDate;
//             document.querySelector('#s-closeDate').value = data.action.closeDate;
//             document.querySelector('#s-closeDate').disabled = true;
//         }
//         else if ( (TODAY > data.action.closeDate) && (data.action.permit == 1) ) {
//             document.querySelector('#s-realCloseDate').min = data.limits.initDate;
//             document.querySelector('#s-realCloseDate').max = data.limits.realCloseDate;
//             document.querySelector('#s-realCloseDate').value = data.action.realCloseDate;
//             document.querySelector('#s-closeDate').value = data.action.closeDate;
//             document.querySelector('#s-closeDate').disabled = true;
//             $('#rowRealCloseDate').removeClass('d-none');
//         }
//         else if ( (TODAY > data.action.realCloseDate) && (data.action.permit == 1) ) {
//             document.querySelector('#s-realCloseDate').min = TODAY;
//             document.querySelector('#s-realCloseDate').value = data.action.realCloseDate;
//             document.querySelector('#s-closeDate').value = data.action.closeDate;
//             document.querySelector('#s-closeDate').disabled = true;
//             $('#rowRealCloseDate').removeClass('d-none');
//         }
//         if ( (TODAY > data.action.closeDate) && (data.action.realCloseDate == null) && (data.action.permit != 1) ) {
//             if (data.action.permit == 0) {
//                 $('.requestClass').removeClass('d-none');
//             }
//             else{
//                 $('.pendientClass').removeClass('d-none');
//             }
//         }
//     })
//     .fail((e)=>{
//         toastAlert(e.statusText, 'error');
//     });
//     document.querySelector('#asignedCloseDateForm').reset();
//     $('#asignedCloseDateForm').validate().resetForm();
//     $('#asignedCloseDateForm').find(".error").removeClass("error");
//     $('#asignedDateModal').modal({backdrop:'static', keyboard: false});
//     document.querySelector('#asignedDateTitle').innerHTML = `Asignación de Fechas: ${name}`;
// }
// /**
//  * Handler to submit assigned user
//  */
// $('#asignedCloseDateForm').submit( (event) => {
//     event.preventDefault() 
//     if($('#asignedCloseDateForm').valid()) {
//         showLoading('#asignedDateModal')
//         //handler notificaction
//         $.post('/action/requirements/date/set', {
//             _token: document.querySelector('meta[name="csrf-token"]').content,
//             realCloseDate: document.querySelector('#s-realCloseDate').value,
//             closeDate: document.querySelector('#s-closeDate').value,
//             idActionPlan: document.querySelector('#dateIdActionPlan').value
//         },
//         (data) => {
//             showLoading('#asignedDateModal')
//             toastAlert(data.msg, data.status);
//             if (data.status == 'success') {
//                 reloadSubActionKeepPage();
//                 reloadActionKeepPage();
//                 $('#asignedDateModal').modal('hide');
//             }
//         })
//         .fail((e)=>{
//             toastAlert(e.statusText, 'error');
//             showLoading('#asignedDateModal')
//         });
//     }
// });

// /**
//  * Set answer request permit real close date modal
//  */
// function setPermitAdmin(idActionPlan, name, permit, user){
//     Swal.fire({                 
//         title: `La Fecha de Cierre expiró el usuario ${user} ha solicitado autorización para habilitar la Fecha de Cierre Real`,
//         text: `¿Desea habilitar la Fecha de Cierre Real para ${name}?`,
//         icon: 'question',
//         showCancelButton: true,
//         confirmButtonColor: '#87CB16',
//         cancelButtonColor: '#d33',
//         confirmButtonText: 'Autorizar',
//         cancelButtonText: 'No Autorizar',
//         allowOutsideClick: false,
//         showCloseButton: true
//     }).then((result) => {
//         if (result.value) {
//             setPermit(idActionPlan, 1);
//         }
//         else if (result.dismiss == 'cancel') {
//             setPermit(idActionPlan, 0);  
//         }
//     });
// }
// /**
//  * Set request permit
//  */
// function requestPermit(idActionPlan, name){
//     Swal.fire({
//         title: `La Fecha de Cierre expiró`,
//         text: `¿Desea solicitar autorización para habiliatar Fecha de Cierre Real en ${name}?`,
//         icon: 'question',
//         allowOutsideClick: false,
//         showCancelButton: true,
//         confirmButtonColor: '#3085d6',
//         cancelButtonColor: '#d33',
//         confirmButtonText: 'Solicitar',
//         cancelButtonText: 'Cancelar'
//     }).then((result) => {
//         if (result.value) {
//             setPermit(idActionPlan, 2);
//         }
//     });
// }
// /**
//  * set value permit
//  */
// function setPermit(idActionPlan, permit){
//     $.post('/action/requirements/request', {
//         _token: document.querySelector('meta[name="csrf-token"]').content,
//         idActionPlan: idActionPlan,
//         permit: permit
//     },
//     (data) => {
//         toastAlert(data.msg, data.status);
//         if (data.status == 'success') {
//             reloadSubActionKeepPage();
//             reloadActionKeepPage();
//         }
//     })
//     .fail((e)=>{
//         toastAlert(e.statusText, 'error');
//     });
// }

// /*************** Reminder ***************/

// /**
//  * Show reminders close date
//  */
// function reminderCloseDate(noRequirement, idActionPlan, type){
//     document.querySelector('#remindersCloseForm').reset();
//     $('#remindersCloseForm').validate().resetForm();
//     $('#remindersCloseForm').find(".error").removeClass("error");
//     document.querySelector("#textClose").innerHTML = 'Fecha de Cierre';
//     document.querySelector("#textCloseFooter").innerHTML = 'Fecha de Cierre';
//     // Data AP
//     $.get('/action/data/reminders', {
//         _token: document.querySelector('meta[name="csrf-token"]').content,
//         idActionPlan: idActionPlan,
//         idObligation: null,
//         idTask: null,
//         typeDate: 0
//     },
//     (data) => {
//         flatpickr('#s-reminder', {
//             allowInput: false,
//             clickOpens: false,
//             inline: true,
//             position: 'below',
//             monthSelectorType: 'static',
//             mode: 'multiple',
//             dateFormat: 'Y-m-d',
//             minDate: 'today',
//             maxDate: data.action.closeDate,
//             defaultDate: data.dates,
//             onChange: (selectedDates, dateStr, instance) => {
//                 setDatesReminder(idActionPlan, null, null, selectedDates, 0);
//             },
//         });
//     })
//     .fail((e)=>{
//         toastAlert(e.statusText, 'error');
//     });
//     let typeText = (type == 0) ? 'Requerimiento' : 'Subrequerimiento';
//     document.querySelector('#remindersCloseTitle').innerHTML =  `${typeText} No: ${noRequirement}`;
//     $('#remindersCloseModal').modal({backdrop:'static', keyboard: false});
// }
// /**
//  * Show reminders real close date
//  */
// function reminderRealCloseDate(noRequirement, idActionPlan, type){
//     document.querySelector('#remindersRealCloseForm').reset();
//     $('#remindersRealCloseForm').validate().resetForm();
//     $('#remindersRealCloseForm').find(".error").removeClass("error");
//     document.querySelector("#textRealClose").innerHTML = 'Fecha de Cierre Real';
//     document.querySelector("#textRealCloseFooter").innerHTML = 'Fecha de Cierre Real';
//     // Data AP
//     $.get('/action/data/reminders', {
//         _token: document.querySelector('meta[name="csrf-token"]').content,
//         idActionPlan: idActionPlan,
//         idObligation: null,
//         idTask: null,
//         typeDate: 1
//     },
//     (data) => {
//         flatpickr('#s-reminderReal', {
//             allowInput: false,
//             clickOpens: false,
//             inline: true,
//             position: 'below',
//             monthSelectorType: 'static',
//             mode: 'multiple',
//             dateFormat: 'Y-m-d',
//             minDate: 'today',
//             maxDate: data.action.realCloseDate,
//             defaultDate: data.dates,
//             onChange: (selectedDates, dateStr, instance) => {
//                 setDatesReminder(idActionPlan, null, null, selectedDates, 1);
//             },
//         });
//     })
//     .fail((e)=>{
//         toastAlert(e.statusText, 'error');
//     });
//     let typeText = (type == 0) ? 'Requerimiento' : 'Subrequerimiento';
//     document.querySelector('#remindersRealCloseTitle').innerHTML =  `${typeText} No: ${noRequirement}`;
//     $('#remindersRealCloseModal').modal({backdrop:'static', keyboard: false});
// }

// /**
//  * Dates in action plan
//  */
// function setTypeDate(noRequirement, idActionPlan, idRequirement, typeDate){
//     document.querySelector('#datesTypesForm').reset();
//     $('#datesTypesForm').validate().resetForm();
//     $('#datesTypesForm').find(".error").removeClass("error");
//     const msgTypeDate = (typeDate == 'init') ? 'Inicio' : 'Cierre';
//     document.querySelector("#typeDateTitle").innerHTML = `Fecha de ${msgTypeDate} para ${noRequirement}`;
//     flatpickr('#s-initDateAP', {
//         allowInput: false,
//         clickOpens: false,
//         inline: true,
//         position: 'below',
//         monthSelectorType: 'static',
//         mode: 'single',
//         dateFormat: 'Y-m-d',
//         minDate: 'today',
//         // maxDate: data.action.realCloseDate,
//         // defaultDate: data.dates,
//         onChange: (selectedDates, dateStr, instance) => {
//             setDatesAP(idActionPlan, selectedDates, typeDate);
//         },
//         disable: [
//             date => {
//                 // return true to disable
//                 // return (date.getDay() === 0 || date.getDay() === 6);
//                 return (date.getDay() === 0);
//             }
//         ],
//         locale: {
//             firstDayOfWeek: 1 // start week on Monday
//         }
//     });
//     $('#setTypesDateModal').modal({backdrop:'static', keyboard: false});
// }

// function setDatesAP(idActionPlan, selectedDates, typeDate){
//     toastAlert('Fecha asignada', 'success');
// }
</script>