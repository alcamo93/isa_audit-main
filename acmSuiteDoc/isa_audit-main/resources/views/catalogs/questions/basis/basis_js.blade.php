<script>
/********************************************************************************************************/
/***************************************  Basis selection  **********************************************/
/********************************************************************************************************/
const basis = {
    idQuestion: null,
    data: [],
    basis: []
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
            data.filterOrder = 'questions'
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

                return `<button class="btn btn-${color} btn-link btn-xs" data-toggle="tooltip" title="${tooltip}"
                            onclick="setBasis(${basis.idQuestion}, ${data}, ${status})">
                            <i class="fa fa-${icon} fa-lg"></i>
                        </button>`
            },
            targets: 3
        }
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        initTooltip();
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
        url: '/catalogs/questions/basis/assigned',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.idQuestion = basis.idQuestion,
            data.idAnswerQuestion = answers.idAnswerQuestion,
            data.filterGuideline = document.querySelector('#filterGuidelineSelected').value,
            data.filterArtSelected = document.querySelector('#filterArtSelected').value
        }
    },
    columns: [
        { data: 'guideline', orderable : true, class: 'text-center' },
        { data: 'order', orderable : true, class: 'text-center' },
        { data: 'legal_basis', className: 'td-actions text-center', orderable: true },
        { data: 'id_legal_basis', className: 'td-actions text-center', width: 150, orderable: false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                basis.basis.push(row);
                return `<button class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Información" 
                            onclick="showFullBasis(${data})">
                            <i class="fa fa-eye fa-lg"></i>
                        </button>`;
            },
            targets: 3
        }
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        initTooltip();
    }
});

/**
 * Reload select basis Datatables
 */
const reloadBasis = () => { 
    getQuestionBasis() 
    basisTable.ajax.reload() 
}
const reloadBasisKeepPage = () => { 
    getQuestionBasis() 
    basisTable.ajax.reload(null, false) 
}
/**
 * Reload selected requirement Datatables
 */
const reloadSelectedBasisTable = () => { 
    selectedBasisTable.ajax.reload() 
}
/**
* When you select a requirement it hides de main screen data and open basis data, then it request the service to obtain requeriment-basis relation info
*/
function selectBasis(idQuestion){
    $('.questions').addClass('d-none')
    $('.loading').removeClass('d-none')
    selectQuestion(idQuestion)
    currentQuestion(idQuestion, '#currentQuestion-p3')
    setGuidelines(null, '#filterGuideline', 'Selecciona uno', selection.idApplicationType)
    getQuestionBasis()
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
* Shows only the basis related to a selected question
*/
function showSelectedBasis(idQuestion) {
    selectQuestion(idQuestion)
    currentQuestion(idQuestion, '#currentQuestion-p4')
    setGuidelines(null, '#filterGuidelineSelected', 'Todos', selection.idApplicationType)
    $('.questions').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        reloadSelectedBasisTable()
        $('.loading').addClass('d-none')
        $('.showBasis').removeClass('d-none')
    }, 1000);
}
/**
* Close and reset de Basis screen 
*/
function closeBasisSelection(basisblade){
    basis.id = null
    basis.data = []
    clearQuestionSelection()
    $('#filterGuideline').val('')
    $('#filterlegalclassification').val('')
    $('#filterGuidelineSelected').val('')
    $('#filterlegalclassificationSelected').val('')
    $('#filterGuidelineSelectedRs').val('')
    $('#filterlegalclassificationSelectedRs').val('')
    $('.questions').removeClass('d-none')
    $(basisblade).addClass('d-none')
}
/**
* Request questions/basis relation data and return a promise
*/
function getQuestionBasis(){
    return new Promise ((resolve, reject) => {
        $.post(`/catalogs/questions/basis/data/${basis.idQuestion}`, { 
            _token: document.querySelector('meta[name="csrf-token"]').content 
        },
        (data)=>{
            if(data){
                basis.data = data
                $('.loading').addClass('d-none')
                resolve(true)
            }
            else {
                $('.loading').addClass('d-none')
                reject(false)
            }
        })
        .fail((e)=>{
            $('.loading').addClass('d-none')
            toastAlert(e.statusText, 'error');
            reject(false)
        });
    });
}
/**
* Sets the question-basis relation
*/
function setBasis(idQuestion, idBasis, status){
    $('.loading').removeClass('d-none')
    return new Promise ((resolve, reject) => {
        $.post('/catalogs/questions/basis/relation', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idQuestion: idQuestion,
            idAnswerQuestion: answers.idAnswerQuestion,
            idBasis: idBasis,
            status: !status
        },
        (data)=>{
            if(data){
                toastAlert(data.msg, data.status);
                if(data.status == 'success'){
                    reloadBasisKeepPage()
                    resolve(true)
                }
                $('.loading').addClass('d-none')
            }
            else {
                $('.loading').addClass('d-none')
                reject(false)
            }
        })
        .fail((e)=>{
            $('.loading').addClass('d-none')
            toastAlert(e.statusText, 'error');
            reject(false)
        });
    });
}
/**
* Filters the guidelines select
 */
function setGuidelines(idLegalClassification, idSelect, text){
    $('.loading').removeClass('d-none')
    return new Promise ((resolve, reject) => {
        $.post('/catalogs/legals/guidelines/selection', { 
            _token: document.querySelector('meta[name="csrf-token"]').content,
            filter_legal_classification : idLegalClassification,
            filter_application_type : selection.idApplicationType
        },
        (data)=>{
            if (data) {
                let options = '<option value="" selected>'+text+'</option>'
                data.forEach((info, index)=>{
                    options += '<option value="'+info.id_guideline+'">'+info.initials_guideline+'</option>'
                })
                $(idSelect).html(options)
                reloadBasisKeepPage()
                $('.loading').addClass('d-none')
                resolve(true)
            }
            else {
                $('.loading').addClass('d-none')
                reject(false)
            }
        })
        .fail((e)=>{
            $('.loading').addClass('d-none')
            toastAlert(e.statusText, 'error');
            reject(false)
        });
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