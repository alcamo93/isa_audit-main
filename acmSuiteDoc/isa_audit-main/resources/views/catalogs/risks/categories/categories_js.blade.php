<script>
$(document).ready( () => {
    setFormValidation('#setCategoryForm');
    setFormValidation('#updateCategoryForm');
    setFormValidation('#setConsequenceForm');
    setFormValidation('#updateConsequenceForm');
    setFormValidation('#setExhibitionForm');
    setFormValidation('#updateExhibitionForm');
    setFormValidation('#setProbabilityForm');
    setFormValidation('#updateProbabilityForm');
    setFormValidation('#riskTextualForm');
    setFormValidation('#setHelpForm');
    setFormValidation('#updateHelpForm');
    const toolbar = {
        dialogsInBody: true,
        dialogsFade: true,
        lang: 'es-ES',
        placeholder: 'Especifica el formato del texto del criterio',
        tabsize: 2,
        height: 200,
        minHeight: null,
        maxHeight: null, 
        toolbar: [
            ['font', ['bold', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
        ]
    }
    $('#s-standard').summernote(toolbar);
    $('#u-standard').summernote(toolbar);
});
/*************** Active menu ***************/
activeMenu(8, 'Nivel de Riesgo');
/**
 * risks/categories datatables
 */
const categoryTable = $('#categoryTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/risks/categories',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.fName = document.querySelector('#filterName').value,
            data.fIdStatus = document.querySelector('#filterIdStatus').value
        }
    },
    columns: [
        { data: 'risk_category', className: 'text-center', orderable : true },
        { data: 'status', className: 'text-center', orderable : true },
        { data: 'id_risk_category', className: 'td-actions text-center', orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                var color = '';
               switch (data) {
                    case 'Activo':
                        color = 'success';
                       break;
                    case 'Inactivo':
                        color = 'danger';
                    break;
                   default:
                       break;
               }
                return `<span class="badge badge-${color} text-white">${data}</span>`; 
            },
            targets: 1
        },
        {
            render: (data, type, row) => {
                let btnDescription = `<button class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Interpretaciónes" 
                                    onclick="openRiskLevelTextual(${data}, '${row.risk_category}')">
                                    <i class="fa fa-flag la-lg"></i>
                                </button>`;
                let btnHelp = `<button class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Valoración" 
                                    onclick="openHelp(${data}, '${row.risk_category}')">
                                    <i class="fa fa-question-circle la-lg"></i>
                                </button>`
                let btnEdit = (MODIFY) ? `<button class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    onclick="openEditcategory(${data}, '${row.risk_category}', ${row.id_status})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </button>` : '';
                let btnDelete = (DELETE) ? `<button class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    onclick="deleteCategory(${data}, '${row.risk_category}')">
                                    <i class="fa fa-times fa-lg"></i>
                                </button>` : '';
                return btnDescription+btnHelp+btnEdit+btnDelete;
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
 * Reload risks/categories Datatables
 */
const reloadCategories = () => { categoryTable.ajax.reload(null, false) }
/**
 * Open modal add form
 */
function openCategoryModal() {
    element.currentIdCategory = null;
    document.querySelector('#setCategoryForm').reset();
    $('#setCategoryForm').validate().resetForm();
    $('#setCategoryForm').find(".error").removeClass("error");
    $('#addCategoryModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set risks/categories form 
 */
$('#setCategoryForm').submit( (event) => {
    event.preventDefault()
    if($('#setCategoryForm').valid()) {
        showLoading('#addCategoryModal')
        //handler notificaction
        $.post('/catalogs/risks/categories/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            name: document.querySelector('#s-name').value,
            idStatus: document.querySelector('#s-idStatus').value
        },
        (data) => {
            toastAlert(data.msg, data.status);
            reloadCategories();
            showLoading('#addCategoryModal')
            if(data.status == 'success') {
                $('#addCategoryModal').modal('hide');
                openRiskLevelTextual(data.info.idRiskCategory, data.info.riskCategory);
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#addCategoryModal');
        })
    }
});
/**
 * Open modal edit form
 */
function openEditcategory(idCategory, name, idStatus) {
    element.currentIdCategory = idCategory;
    document.querySelector('#updateCategoryForm').reset();
    $('#updateCategoryForm').validate().resetForm();
    $('#updateCategoryForm').find(".error").removeClass("error");
    document.querySelector('#titleEditCategory').innerHTML = `Edición del categoría: "${name}"`;
    document.querySelector('#u-name').value = name;
    document.querySelector('#u-idStatus').value = idStatus;
    $('#editCategoryModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit update risks/categories form 
 */
$('#updateCategoryForm').submit( (event) => {
    event.preventDefault()
    if($('#updateCategoryForm').valid()) {
        //handler notificaction
        showLoading('#editCategoryModal');
        $.post('/catalogs/risks/categories/update', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idCategory: element.currentIdCategory,
            name: document.querySelector('#u-name').value,
            idStatus: document.querySelector('#u-idStatus').value
        },
        (data) => {
            toastAlert(data.msg, data.status);
            reloadCategories();
            showLoading('#editCategoryModal');
            if(data.status == 'success') $('#editCategoryModal').modal('hide');
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#editCategoryModal');
        })
    }
});
/**
 * Delete category
 */
function deleteCategory(idElement, categoryName){
    Swal.fire({
        title: `¿Estas seguro de eliminar "${categoryName}"?`,
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
            $.post('/catalogs/risks/categories/delete',
            {
                '_token':'{{ csrf_token() }}',
                idCategory: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success'){
                    reloadCategories();
                }
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
                $('.loading').addClass('d-none')
            })
        }
    });
}
/**
 * textual level
 */
function openRiskLevelTextual(idRiskCategory, riskCategory){
    element.currentIdCategory = idRiskCategory;
    currentInterpretation.one = 0; 
    currentInterpretation.two = 0;
    currentInterpretation.three = 0;
    document.querySelector('#titleRiskTextual').innerHTML = `Interpretación para ${riskCategory}`;
    // showLoading('#riskTextual');
    $.get('/catalogs/risks/categories/interpretations', {
        _token: document.querySelector('meta[name="csrf-token"]').content,
        idRiskCategory: idRiskCategory
    },
    (data) => {
        data.forEach(e => {
            if (e.interpretation == 'Bajo') {
                currentInterpretation.one = e.id_risk_interpretation;
                document.querySelector('#limitOne').value = e.interpretation_max
                defineLimitOne(e.interpretation_max);
            }
            if (e.interpretation == 'Medio') {
                currentInterpretation.two = e.id_risk_interpretation;
                document.querySelector('#limitTwo').value = e.interpretation_max
                defineLimitTwo(e.interpretation_max);
            }
            if (e.interpretation == 'Alto') {
                currentInterpretation.three = e.id_risk_interpretation;
            }
        });
        $('#riskTextual').modal({backdrop:'static', keyboard: false});
    })
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
        showLoading('#riskTextual');
    })
    
}
/**
 * Calculate and set range one
 */
function defineLimitOne(limitOne) {
    let limitOneEnd = parseInt(limitOne);
    let limitTwoStart = parseInt(limitOne) + 1;
    document.querySelector('#limitOneEnd').innerHTML = ` de 0 a ${limitOneEnd}`;
    document.querySelector('#limitTwo').value = limitTwoStart + 1;
    $('#limitTwo').attr('min', (limitTwoStart + 2));
    $('#limitTwo').attr('data-rule-min', (limitTwoStart + 2));
    document.querySelector('#limitTwoStart').innerHTML = ` de ${limitTwoStart} a `;
    document.querySelector('#limitTwoEnd').innerHTML = limitTwoStart +1;   
}
/**
 * Calculate and set range two
 */
function defineLimitTwo(limitTwo) {
    let limitTwoEnd = parseInt(limitTwo);
    let limitThreeStart = parseInt(limitTwo) + 1;
    document.querySelector('#limitTwoEnd').innerHTML = limitTwoEnd;
    document.querySelector('#limitThreeStart').innerHTML = ` de ${limitThreeStart} o mayor`;
}
/**
 * Create prototype Range
 */
let RangeData = function (id_risk_interpretation, interpretation, interpretation_min, interpretation_max, id_risk_category) {
    this.id_risk_interpretation = id_risk_interpretation,
    this.interpretation = interpretation, 
    this.interpretation_min = interpretation_min, 
    this.interpretation_max = interpretation_max, 
    this.id_risk_category = id_risk_category
}
/**
 * Set and Update Ranges
 */
$('#riskTextualForm').submit( (event) => {
    event.preventDefault()
    if($('#riskTextualForm').valid()) {
        let ranges = [];

        let minOne = 0;
        let maxOne = parseInt(document.querySelector('#limitOne').value);

        let minTwo = parseInt(document.querySelector('#limitOne').value) + 1;
        let maxTwo = parseInt(document.querySelector('#limitTwo').value);

        let minThree = parseInt(document.querySelector('#limitTwo').value) + 1;
        let maxThree = maxOne * maxTwo * minThree;

        let rangeOne = new RangeData(currentInterpretation.one, 'Bajo', minOne, maxOne, element.currentIdCategory);
        let rangeTwo = new RangeData(currentInterpretation.two, 'Medio', minTwo, maxTwo, element.currentIdCategory);
        let rangeThree = new RangeData(currentInterpretation.three, 'Alto', minThree, maxThree, element.currentIdCategory);
        
        ranges.push(rangeOne, rangeTwo, rangeThree);
        let jsonRanges = JSON.stringify(ranges);
        // handler notificaction
        showLoading('#riskTextual');
        $.post('/catalogs/risks/categories/interpretations/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            ranges: jsonRanges
        },
        (data) => {
            toastAlert(data.msg, data.status);
            showLoading('#riskTextual');
            if(data.status == 'success') $('#riskTextual').modal('hide');
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#riskTextual');
        })
    }
});
</script>