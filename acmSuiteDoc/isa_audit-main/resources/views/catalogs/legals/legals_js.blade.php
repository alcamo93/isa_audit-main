<script>
$(document).ready( () => {
    setFormValidation('#setGuidelineForm');
    setFormValidation('#updateGuidelineForm');
    setFormValidation('#setBasisForm');
    setFormValidation('#updateBasisForm');
    const toolbar = {
        dialogsInBody: true,
        dialogsFade: true,
        lang: 'es-ES',
        placeholder: 'Especifica el formato del texto del artículo',
        tabsize: 2,
        height: 200,
        minHeight: null,
        maxHeight: null, 
        toolbar: [
            ['font', ['bold', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
        ]
    }
    $('#quote_s').summernote(toolbar);
    $('#quote_u').summernote(toolbar);
});

const guideline = {
    data: []
}
const basis = {
    data : []
}
/*************** Active menu ***************/
activeMenu(8, 'Fundamentos Legales');
/**
 * Guidelines datatables
 */
const guidelinesTable = $('#guidelinesTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/legals/guidelines',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content, 
            data.filterName = document.querySelector('#filterName').value,
            data.filter_application_type = document.querySelector('#filter_application_type').value,
            data.filter_legal_classification = document.querySelector('#filter_legal_classification').value,
            data.filter_state = document.querySelector('#filter_state').value,
            data.filter_city = document.querySelector('#filter_city').value,
            data.filterInitials = document.querySelector('#filterInitials').value
        }
    },
    columns: [
        { data: 'guideline', orderable : true },
        { data: 'initials_guideline', orderable : true },
    ],
    columnDefs: [
        {
            render: function ( data, type, row ) {

                guideline.data[row.id_guideline] = row

                let btnBasis = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Artículos" 
                                    onclick="openBasis('${row.id_guideline}','${row.id_application_type}')">
                                    <i class="fa fa-outdent la-lg"></i>
                                </a>`;
                let btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    onclick="openEditGuideline(${row.id_guideline}, '${row.guideline}', '${row.initials_guideline}', ${row.id_application_type}, ${row.id_legal_c}, ${row.id_state}, ${row.id_city}, '${row.last_date_format}')">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>` : '';
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    onclick="deleteGuideline(${row.id_guideline},'${row.guideline}')">
                                    <i class="fa fa-times fa-lg"></i>
                                </a>` : '';
                return btnBasis+btnEdit+btnDelete;
            },
            targets: 2
        }
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
    }
});
/**
 * Reload Guidelines Datatable
 */
const reloadGuidelines = () => { guidelinesTable.ajax.reload() }
const reloadGuidelinesKeepPage = () => { guidelinesTable.ajax.reload(null, false) }
/**
 * Open modal add form
 */
function openAddGuidelines(){
    document.querySelector('#setGuidelineForm').reset();
    $('#setGuidelineForm').validate().resetForm();
    $('#setGuidelineForm').find(".error").removeClass("error");
    $('#state_s').attr('data-rule-required', false)
    $('#citys').attr('data-rule-required', false)
    $('.statesSelection_s').addClass('d-none')
    $('.citiesSelection_s').addClass('d-none')
    $('#addModalGuideline').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set Guideline form 
 */
$('#setGuidelineForm').submit( (event) => {
    event.preventDefault();
    if($('#setGuidelineForm').valid()) {
        showLoading('#addModalGuideline')
        //handler notificaction
        $.post('/catalogs/legals/guidelines/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            guideline_s: document.querySelector('#guideline_s').value,
            initials_guideline_s: document.querySelector('#initials_guideline_s').value,
            application_type_s: document.querySelector('#application_type_s').value,
            legal_classification_s: document.querySelector('#legal_classification_s').value,
            state_s: document.querySelector('#state_s').value,
            city_s: document.querySelector('#city_s').value,
            lastDate : document.querySelector('#lastDate_s').value
        },
        (data) => {
            toastAlert(data.msg, data.status);
            showLoading('#addModalGuideline')
            if(data.status == 'success'){
                reloadGuidelinesKeepPage();
                $('#addModalGuideline').modal('hide');
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#addModalGuideline');
        })
    }
});
const fieldsLocation = {
    '1': (lastLetter) => {
        $('#state_'+lastLetter).attr('data-rule-required', false)
        $('#city'+lastLetter).attr('data-rule-required', false)
        $('.statesSelection_'+lastLetter).addClass('d-none')
        $('.citiesSelection_'+lastLetter).addClass('d-none')
    },
    '2': (lastLetter) => {
        $('#state_'+lastLetter).attr('data-rule-required', true)
        $('#city'+lastLetter).attr('data-rule-required', false)
        $('.statesSelection_'+lastLetter).removeClass('d-none')
        $('.citiesSelection_'+lastLetter).addClass('d-none')
    },
    '4': (lastLetter) => {
        $('#state_'+lastLetter).attr('data-rule-required', true)
        $('#city'+lastLetter).attr('data-rule-required', true)
        $('.statesSelection_'+lastLetter).removeClass('d-none')
        $('.citiesSelection_'+lastLetter).removeClass('d-none')
    }
}
function handleFieldsLocations(idApplicationType, lastLetter) {
    document.querySelector('#state_'+lastLetter).value = '';
    document.querySelector('#city_'+lastLetter).value = '';
    $('#state_'+lastLetter).attr('data-rule-required', false)
    $('#city'+lastLetter).attr('data-rule-required', false)
    $('.statesSelection_'+lastLetter).addClass('d-none')
    $('.citiesSelection_'+lastLetter).addClass('d-none')
    const setFilter = fieldsLocation[idApplicationType](lastLetter);
}
/**
 * Open modal edit form
 */
function openEditGuideline(idGuideline, guideline, initialsGuideline, idApplicationType, idLegalC, idState, idCity, lastDate){
    document.querySelector('#updateGuidelineForm').reset();
    $('#updateGuidelineForm').validate().resetForm();
    $('#updateGuidelineForm').find(".error").removeClass("error");
    document.querySelector('#titleEditGuideline').innerHTML = `Edición de: "${guideline}"`
    handleFieldsLocations(idApplicationType, 'u');
    document.querySelector('#guideline_u').value = guideline;
    document.querySelector('#guideline_u').setAttribute('idGuideline', idGuideline);
    document.querySelector('#initials_guideline_u').value = initialsGuideline;
    document.querySelector('#application_type_u').value = idApplicationType;
    document.querySelector('#legal_classification_u').value = idLegalC;
    document.querySelector('#state_u').value = idState;
    if (idApplicationType == 4) {
        setCities(idState, '#city_u')
        .then(() => {
            console.log('hola', idCity);
            document.querySelector('#city_u').value = idCity;
        })
    }
    document.querySelector('#lastDate_u').value = lastDate;
    $('#editModalGuideline').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit update corporate form 
 */
$('#updateGuidelineForm').submit( (event) => {
    event.preventDefault();
    if($('#updateGuidelineForm').valid()) {
        showLoading('#editModalGuideline')
        //handler notificaction
        $.post('/catalogs/legals/guidelines/update', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idguideline : document.querySelector('#guideline_u').getAttribute('idGuideline'),
            guideline : document.querySelector('#guideline_u').value,
            initials_guideline : document.querySelector('#initials_guideline_u').value,
            application_type : document.querySelector('#application_type_u').value,
            legal_classification : document.querySelector('#legal_classification_u').value,
            state : document.querySelector('#state_u').value,
            city : document.querySelector('#city_u').value,
            lastDate : document.querySelector('#lastDate_u').value
        },
        (data) => {
            toastAlert(data.msg, data.status);
            showLoading('#editModalGuideline')
            if(data.status == 'success'){
                reloadGuidelinesKeepPage();
                $('#editModalGuideline').modal('hide');
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#addModalGuideline');
        })
    }
});
/**
 * Delete corporate
 */
function deleteGuideline(idElement, guidelineName) {
    Swal.fire({
        title: `¿Estas seguro de eliminar "${guidelineName}"?`,
        text: 'El cambio será permanente al confirmar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            // send to server
            $('.loading').removeClass('d-none')
            $.post('{{asset('/catalogs/legals/guidelines/delete')}}',
            {
                _token:document.querySelector('meta[name="csrf-token"]').content,
                idGuideline: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success'){
                    reloadGuidelinesKeepPage();
                }
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
                $('.loading').addClass('d-none')
            })
        }
    });
}
/*******************************************************************************************************************/
/****************************************          Basis         ***************************************************/
/*******************************************************************************************************************/
/**
 * Basis datatables
 */
const basisTable = $('#basisTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/legals/basis',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content
            data.id_guideline = document.querySelector('#basisTable').getAttribute('id_guideline'),
            data.filterName = document.querySelector('#filterBasisName').value,
            data.filterArt = null,
            data.filterOrder = 'main'
        }
    },
    columns: [
        { data: 'order', orderable : true, class: 'text-center' },
        { data: 'legal_basis', orderable : true, class: 'text-center' },
        { data: 'id_legal_basis', orderable : false, class: 'text-center' },
    ],
    columnDefs: [
        {
            render: function ( data, type, row ) {
                
                basis.data[row.id_legal_basis] = row

                let btnBasis = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ver más" 
                                    onclick="showFullBasis('${row.id_legal_basis}')">
                                    <i class="fa fa-eye fa-lg"></i>
                                </a>`;
                let btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    onclick="openEditBasis(${row.id_legal_basis})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>` : '';
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    onclick="deleteBasis(${row.id_legal_basis},'${row.legal_basis}')">
                                    <i class="fa fa-times fa-lg"></i>
                                </a>` : '';
                return btnBasis+btnEdit+btnDelete;
            },
            targets: 2
        }
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
    }
});
/**
 * Reload Basis Datatable
 */
const reloadBasis = () => { basis.data = []; basisTable.ajax.reload() }
const reloadBasisKeepPage = () => { basis.data = []; basisTable.ajax.reload(null, false) }

/**
 * Shows Basis Datatable
 */
function openBasis(id_guideline, id_application_type){
    $('.guidelines').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(function(){
        $('#basisTable').attr('id_guideline', id_guideline)
        $('#basisTable').attr('id_application_type', id_application_type)
        $('.loading').addClass('d-none')
        $('.basis').removeClass('d-none')
        reloadBasis();
        stateBasis(id_guideline);
    }, 1000);
}
/**
 * Hide basis datable
 */
function returnToGuidelines (){
    $('.basis').addClass('d-none')
    $('.loading').removeClass('d-none')
    $('#basisTable').attr('id_guideline', '')
    $('#basisTable').attr('id_application_type', '')
    $('#filterBasisName').val('')
    setTimeout(function(){
        basis.data = [];
        $('.loading').addClass('d-none')
        $('.guidelines').removeClass('d-none')
        reloadBasisKeepPage()
    }, 1500);
}
/**
 * Open add basis modal
 */
function openAddBasis() {
    $('#quote_s').summernote('reset');
    document.querySelector('#setBasisForm').reset();
    $('#setBasisForm').validate().resetForm();
    $('#setBasisForm').find(".error").removeClass("error");
    $('#addModalBasis').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set Guideline form 
 */
$('#setBasisForm').submit( (event) => {
    event.preventDefault();
    let richText = document.querySelector('#quote_s').value;
    if (richText == '') {
        toastAlert(`El campo 'Cita Legal' no puede estar vacio`, 'warning');
        return;
    }
    if($('#setBasisForm').valid()) {
        //handler notificaction
        showLoading('#addModalBasis')
        $.post('/catalogs/legals/basis/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            basis: document.querySelector('#basis_s').value,
            quote: document.querySelector('#quote_s').value,
            publish : document.querySelector('#publish_s').value,
            guideline: document.querySelector('#basisTable').getAttribute('id_guideline'),
            applicationType: document.querySelector('#basisTable').getAttribute('id_application_type'),
            order: document.querySelector('#order_s').value,
        },
        (data) => {
            toastAlert(data.msg, data.status);
            showLoading('#addModalBasis')
            if(data.status == 'success'){
                reloadBasisKeepPage();
                $('#addModalBasis').modal('hide');
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#addModalBasis');
        })
    }
});
/**
 * Service get legal data
 */
function getLegalService(idBasis){
    return new Promise((resolve, reject) => {
        $.get('/catalogs/legals/basis', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idBasis: idBasis
        },
        data => {
            resolve(data)
        })
        .fail(e => {
            reject(e.statusText);
        });
    })
}
/**
 * Open modal edit form
 */
function openEditBasis(idBasis){
    document.querySelector('#updateBasisForm').reset();
    $('#updateBasisForm').validate().resetForm();
    $('#updateBasisForm').find(".error").removeClass("error");
    $('.loading').removeClass('d-none')
    getLegalService(idBasis)
    .then(data => {
        $('.loading').addClass('d-none')
        document.querySelector('#titleEditBasis').innerHTML = `Edición de: "${data.legal_basis}"`
        $('#basis_u').attr('idBasis', idBasis)
        document.querySelector('#basis_u').value = data.legal_basis
        document.querySelector('#publish_u').value = data.publish
        document.querySelector('#order_u').value = data.order
        $('#quote_u').summernote('code', data.legal_quote)
        $('#editModalBasis').modal({backdrop:'static', keyboard: false});
    })
    .catch(e => {
        $('.loading').addClass('d-none')
        toastAlert(e, 'error');
    });
}
/**
 * Handler to submit update corporate form 
 */
$('#updateBasisForm').submit( (event) => {
    event.preventDefault();
    let richText = document.querySelector('#quote_u').value;
    if (richText == '') {
        toastAlert(`El campo 'Cita Legal' no puede estar vacio`, 'warning');
        return;
    }
    if($('#updateBasisForm').valid()) {
        showLoading('#editModalBasis')
        //handler notificaction
        $.post('/catalogs/legals/basis/update', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
              idBasis : $('#basis_u').attr('idBasis'),
              basis : document.querySelector('#basis_u').value,
              quote : document.querySelector('#quote_u ').value,
              publish : document.querySelector('#publish_u').value,
              guideline: $('#basisTable').attr('id_guideline'),
              applicationType: $('#basisTable').attr('id_application_type'),
              order: document.querySelector('#order_u').value,
        },
        (data) => {
            toastAlert(data.msg, data.status);
            showLoading('#editModalBasis')
            if(data.status == 'success'){
                reloadBasisKeepPage();
                $('#editModalBasis').modal('hide');
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#editModalBasis')
        })
    }
});
/**
 * Delete corporate
 */
function deleteBasis(idElement, basisName) {
    Swal.fire({
        title: `¿Estas seguro de eliminar "${basisName}"?`,
        text: 'El cambio será permanente al confirmar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            // send to server
            $('.loading').removeClass('d-none')
            $.post('/catalogs/legals/basis/delete',
            {
                _token:document.querySelector('meta[name="csrf-token"]').content,
                idBasis: idElement
            },
            data => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success'){
                    reloadBasisKeepPage();
                }
            })
            .fail(e => {
                toastAlert(e.statusText, 'error');
                $('.loading').addClass('d-none')
            })
        }
    });
}
/**
 * Get data by id basis
 */
function getDataBasisService(idBasis){
    return new Promise((resolve, reject) => {
        $.get('/catalogs/legals/basis', {
            _token:document.querySelector('meta[name="csrf-token"]').content,
            idBasis: idBasis
        },
        (data) => {
           resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        })
    });
}
/**
* Show full info
*/
function showFullBasis(idBasis){
    document.querySelector('#titleBasis').innerHTML = '';
    document.querySelector('#basisQuote').innerHTML = '';
    $('.loading').removeClass('d-none')
    getDataBasisService(idBasis)
    .then(data => {
        document.querySelector('#titleBasis').innerHTML = data.legal_basis;
        document.querySelector('#basisQuote').innerHTML = data.legal_quote;
        $('.loading').addClass('d-none')
        $('#fullBasisViewModal').modal({backdrop:'static', keyboard: false})
    })
    .catch(e => {
        $('.loading').addClass('d-none')
        toastAlert(e, 'error');
    })
}
</script>