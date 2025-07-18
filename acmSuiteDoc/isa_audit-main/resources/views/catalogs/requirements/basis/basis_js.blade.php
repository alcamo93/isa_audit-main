<script>
/********************************************************************************************************/
/***************************************  Basis selection  **********************************************/
/********************************************************************************************************/
const basis = {
    id: null, // requirement or subrequirement
    data : [],
    basis:[],
    url: 'requirements' // 'requirements or subrequirements
}

const basisTable = $('#basisSelectionTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage_basis_selection.json'
    },
    ajax: { 
        url: '/catalogs/legals/basis',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.id_guideline = document.querySelector('#filterGuideline').value,
            data.filterArt = document.querySelector('#filterArt').value,
            data.filterName = null,
            data.filterOrder = 'requirements'
        }
    },
    columns: [
        { data: 'guideline', orderable : true, class: 'text-center' },
        { data: 'order', orderable : true, class: 'text-center' },
        { data: 'legal_basis', className: 'td-actions text-center', orderable : true },
        { data: 'id_legal_basis', className: 'td-actions text-center', width:150, orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                let color = ''
                let icon = ''
                let status = false
                let index = basis.data.findIndex( o => o.id_legal_basis === data )

                if( index > -1 )
                {
                    color = 'success'
                    icon = 'check'
                    tooltip = 'Clic para remover fundamento'
                    status = true
                }
                else{
                    tooltip = 'Clic para asignar fundamento'
                    color = 'danger'
                    icon = 'times'
                }

                return `<a class="btn btn-${color} btn-link btn-xs" data-toggle="tooltip" title="${tooltip}"
                            href="javascript:setBasis(${basis.id}, ${data}, ${status})">
                            <i class="fa fa-${icon} fa-lg"></i>
                        </a>`
            },
            targets: 3
        }
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
    }
});

const selectedBasisTable = $('#basisSelectedTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage_basis_selected.json'
    },
    ajax: { 
        url: '/catalogs/requirements/requirements-basis-dt',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.id = basis.id,
            data.filterGuideline = document.querySelector('#filterGuidelineSelected').value,
            data.filterArt = document.querySelector('#filterArtSelected').value
        }
    },
    columns: [
        { data: 'guideline', orderable : true, class: 'text-center' },
        { data: 'order', orderable : true, class: 'text-center' },
        { data: 'legal_basis', className: 'td-actions text-center', orderable : true },
        { data: 'id_legal_basis', className: 'td-actions text-center', width:150, orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                basis.basis.push(row)
                return `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Información" 
                            onclick="showFullBasis(${data})">
                            <i class="fa fa-eye fa-lg"></i>
                        </a>`
            },
            targets: 3
        }
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
    }
});

const rsSelectedBasisTable = $('#rsBasisSelectedTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/requirements/subrequirements-basis-dt',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.id = basis.id,
            data.filterGuideline = document.querySelector('#filterGuidelineSelectedRs').value
            data.filterArt = document.querySelector('#filterArtSelectedRs').value
        }
    },
    columns: [
        { data: 'guideline', orderable : true, class: 'text-center' },
        { data: 'order', orderable : true, class: 'text-center' },
        { data: 'legal_basis', className: 'td-actions text-center', orderable : true },
        { data: 'id_legal_basis', className: 'td-actions text-center', width:150, orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                basis.basis.push(row)
                return `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Información" 
                            onclick="showFullBasis(${data})">
                            <i class="fa fa-eye fa-lg"></i>
                        </a>`
            },
            targets: 3
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
 * Reload select basis Datatables
 */
const reloadBasis = () => { 
    getRequirementBasis() 
    basisTable.ajax.reload() 
}
const reloadBasisKeepPage = () => { 
    getRequirementBasis() 
    basisTable.ajax.reload(null, false) 
}
/**
 * Reload selected requirement Datatables
 */
const reloadSelectedBasisTable = () => { 
    selectedBasisTable.ajax.reload() 
}
/**
 * Reload selecter subrequirement Datatables
 */
const reloadRsSelectedBasisTable = () => { 
    rsSelectedBasisTable.ajax.reload() 
}
/**
* When you select a requirement it hides de main screen data and open basis data, then it request the service to obtain requeriment-basis relation info
*/
function selectBasis(id, url, applicationType, state){
    $('.'+url).addClass('d-none')
    $('.loading').removeClass('d-none')
    
    basis.id = id
    basis.url = url
    basis.data = []

    selection.applicationType = applicationType
    selection.state = state
    currentPageBasis(id, url, 2);
    setGuidelines(null, '#filterGuideline', 'Selecciona un reglamento')

    getRequirementBasis()
    .then(()=>{
        $('.loading').addClass('d-none')
        $('.basisSelection').removeClass('d-none')
    })
    .catch(()=>{
        $('.loading').addClass('d-none')
        $('.basisSelection').removeClass('d-none')
    })
}
/**
* Shows only the basis related to a selected requirement/subrequirement
*/
function showSelectedBasis(id, url, selectedBlade, applicationType, state){
    
    basis.id = id
    basis.url = url
    
    selection.applicationType = applicationType
    selection.state = state
    let setIn = (selectedBlade == 'showBasis') ? 1 : 3;
    currentPageBasis(id, url, setIn);

    if(selectedBlade == 'showBasis') setGuidelines(null, '#filterGuidelineSelected', 'Selecciona un reglamento')
    if(selectedBlade == 'showBasisSr') setGuidelines(null, '#filterGuidelineSelectedRs', 'Selecciona un reglamento')
    
    $('.'+basis.url).addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        if(selectedBlade == 'showBasis') reloadSelectedBasisTable()
        if(selectedBlade == 'showBasisSr') reloadRsSelectedBasisTable()
        $('.loading').addClass('d-none')
        $('.'+selectedBlade).removeClass('d-none')
    }, 1000);
}
/**
* Close and reset de Basis screen 
*/
function closeBasisSelection(basisblade){
    basis.id = null
    basis.data = null
    if (basisblade != 'showBasisSr')
    {
        selection.applicationType = null
        selection.state = null
    }
    $('#filterGuideline').val('')
    $('#filterlegalclassification').val('')
    $('#filterGuidelineSelected').val('')
    $('#filterlegalclassificationSelected').val('')
    $('#filterGuidelineSelectedRs').val('')
    $('#filterlegalclassificationSelectedRs').val('')
    $('.'+basis.url).removeClass('d-none')
    $(basisblade).addClass('d-none')
}
/**
* Request requirements-basis relation data and return a promise
*/
function getRequirementBasis(){
    if(basis.id) return new Promise ((resolve, reject) => {
        $.post(
            '/catalogs/requirements/'+basis.url+'-basis/'+basis.id,
            { '_token': document.querySelector('meta[name="csrf-token"]').content },
            (data)=>{
                if(data){
                    basis.data = data
                    resolve(true)
                }
                else reject(false)
            }
        );  
    })
}
/**
* Sets the requirements-basis relation
*/
function setBasis(id, idBasis, status)
{
    return new Promise ((resolve, reject) => {
        $.post(
            '/catalogs/requirements/'+basis.url+'-basis',
            { 
                '_token': document.querySelector('meta[name="csrf-token"]').content,
                id : id,
                idBasis : idBasis,
                status : !status
            },
            (data)=>{
                if(data){
                    toastAlert(data.msg, data.status);
                    if(data.status == 'success'){
                        reloadBasisKeepPage()
                        resolve(true)
                    }
                }
                else reject(false)
            }
        );  
    })
}
/**
* Filters the guidelines select
 */
function setGuidelines(idLegalClassification, idSelect, text){
    return new Promise ((resolve, reject) => {
        $.post(
            '/catalogs/legals/guidelines/selection',
            { 
                '_token': document.querySelector('meta[name="csrf-token"]').content,
                filter_legal_classification : idLegalClassification,
                filter_application_type : selection.applicationType,
                filter_state : selection.state
            },
            (data)=>{
                if(data.length > 0){
                    let options = '<option value="" selected>'+text+'</option>'
                    data.forEach((info, index)=>{
                        options += '<option value="'+info.id_guideline+'">'+info.initials_guideline+'</option>'
                    })
                    $(idSelect).html(options)
                    reloadBasis()
                    resolve(true)
                }
                else {
                    let options = '<option value="" selected>No se cuenta con Ley/Reglamento/Norma que coincida</option>'
                    $(idSelect).html(options)
                    resolve(true)
                }
            }
        );  
    })
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