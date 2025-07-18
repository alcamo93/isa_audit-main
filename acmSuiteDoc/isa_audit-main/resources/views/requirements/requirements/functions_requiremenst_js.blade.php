<script>
activeMenu(15, 'Condicionantes, actas u otros'); 

const SUB = false;

const requirementsTable = $('#requirementsTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    order: [[ 0, "asc" ]],
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/requirements/requirements',
        type: 'POST',
        data: (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.IdForm = null,
            data.filterRequirementNumber = document.querySelector('#filterRequirementNumber').value,
            data.filterRequirement = document.querySelector('#filterRequirement').value,
            data.IdMatter = document.querySelector('#filterIdMatter').value,
            data.IdAspect = document.querySelector('#filterIdAspect').value,
            data.IdEvidence = document.querySelector('#filterIdEvidence').value,
            data.IdObtainingPeriod = document.querySelector('#filterIdObtainingPeriod').value,
            data.IdUpdatePeriod = document.querySelector('#filterIdUpdatePeriod').value,
            data.IdCondition = 1,
            data.IdAplicationType = document.querySelector('#filterIdAplicationType').value,
            data.IdState = document.querySelector('#filterIdState').value,
            data.IdCity = document.querySelector('#filterIdCity').value,
            data.IdRequirementType = 11, // specific requirements
            data.filterIdCustomer = document.querySelector('#filterIdCustomer').value,
            data.filterIdCorporate = document.querySelector('#filterIdCorporate').value
        }
    }, 
    columns: [
        { data: 'order', className: 'td-actions text-center', orderable : true },
        { data: 'no_requirement', orderable : true },
        { data: 'requirement', orderable : true },
        { data: 'id_requirement', className: 'td-actions text-center', width:50, orderable : false,  visible: SUB},
        { data: 'id_requirement', className: 'td-actions text-center', width:200, orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => { 
                let btnSubrequirements = 'N/A'
                if(row.id_requirement_type == 5) {
                    btnSubrequirements = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Subrequerimientos" 
                        onclick="showSubrequirements(${data})">
                        <i class="fa fa-outdent la-lg"></i>
                    </a>`;
                }
                return btnSubrequirements
            },
            targets: 3
        },
        {
            render: (data, type, row) => {

                let index = requirements.data.indexOf(row)
                if(index == -1 ) requirements.data.push(row)
                
 
                let showFullRequirement = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ver más" 
                                    onclick="showDataRequirement(${data})">
                                    <i class="fa fa-eye fa-lg"></i>
                                </a>`;
                let btnRecomendations = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Recomendaciones" 
                                    onclick="openRecomendations(${data}, 1)">
                                    <i class="fa fa-sticky-note fa-lg"></i>
                                </a>`;
                let btnSelectBasis = (MODIFY) ? `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Selección de fundamentos" 
                                    onclick="selectBasis(${data}, 'requirements', ${row.id_application_type}, ${row.id_state},${row.id_city})">
                                    <i class="fa fa-list  fa-lg"></i>
                                </a>`: '';
                let btnSelectedBasis = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Visualización de fundamentos asignados" 
                                    onclick="showSelectedBasis(${data}, 'requirements', 'showBasis', ${row.id_application_type}, ${row.id_state},${row.id_city})">
                                    <i class="fa fa-list-alt fa-lg"></i>
                                </a>`;
                let btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    onclick="openRequirementModel(${data}, false, false)">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>` : '';
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    onclick="deleteRequirement(${data})">
                                    <i class="fa fa-times fa-lg"></i>
                                </a>` : '';
                return showFullRequirement+btnRecomendations+btnSelectBasis+btnSelectedBasis+btnEdit+btnDelete;
            },
            targets: 4
        }
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
    }
});

function setObligation(idCorporate, selectorIdObligations){
    console.log(idCorporate, selectorIdObligations);
}

function setDataInForm() {
    document.querySelector('#s-idCustomer').value = requirements.current.id_customer;
    return setCorporates(requirements.current.id_customer, '#s-idCorporate')
    .then(() => {
        document.querySelector('#s-idCorporate').value = requirements.current.id_corporate;
        document.querySelector('#noRequirement').setAttribute('idRequirement', requirements.current.id_requirement);
        document.querySelector('#noRequirement').value = requirements.current.no_requirement;
        document.querySelector('#requirement').value = requirements.current.requirement;
        document.querySelector('#requirementDesc').value = requirements.current.description;
        // $('#requirementHelp').summernote('code', requirements.current.help_requirement);
        // $('#requirementAcceptance').summernote('code', requirements.current.acceptance);
        document.querySelector('#IdMatter').value = requirements.current.id_matter;
        // document.querySelector('#IdEvidence').value = requirements.current.id_evidence;
        // setSpecificDocument(requirements.current.id_evidence, '#document');
        // document.querySelector('#document').value = requirements.current.document;
        // document.querySelector('#IdObtainingPeriod').value = requirements.current.id_obtaining_period;
        document.querySelector('#orderReq').value = requirements.current.order;
        // document.querySelector('#IdUpdatePeriod').value = requirements.current.id_update_period || '';
        // setAplicationType(requirements.current.id_requirement_type, '#IdAplicationType', '#IdState', '#IdCity', null);
        document.querySelector('#IdAplicationType').value = requirements.current.id_application_type;
        // document.querySelector('#IdObligation').value = requirements.current.id_obligation;
        return setAspects(requirements.current.id_matter, '#IdAspect', '', false)
        .then(() => {
            document.querySelector('#IdAspect').value = requirements.current.id_aspect;
        })
    })
    .catch(e => {
        toastAlert(e, 'error');
    })
}

function getDataInForm(){
    return {
        _token:  document.querySelector('meta[name="csrf-token"]').content,
        IdForm: null,
        idCustomer: document.querySelector('#s-idCustomer').value,
        idCorporate: document.querySelector('#s-idCorporate').value,
        IdMatter : document.querySelector('#IdMatter').value,
        IdAspect : document.querySelector('#IdAspect').value,
        // IdEvidence : document.querySelector('#IdEvidence').value,
        IdCondition : 1, // Critico
        // document : document.querySelector('#document').value,
        // IdObtainingPeriod : document.querySelector('#IdObtainingPeriod').value,
        // IdUpdatePeriod : document.querySelector('#IdUpdatePeriod').value,
        order :document.querySelector('#orderReq').value,
        IdRequirementType : 11, // Specific requirement
        IdAplicationType : document.querySelector('#IdAplicationType').value,
        IdState : null,
        IdCity : null,
        // IdObligation: document.querySelector('#IdObligation').value,
        idRequirement : document.querySelector('#noRequirement').getAttribute('idRequirement'),
        noRequirement : document.querySelector('#noRequirement').value,
        requirement : document.querySelector('#requirement').value,
        requirementDesc : document.querySelector('#requirementDesc').value,
        // requirementHelp : document.querySelector('#requirementHelp').value,
        // requirementAcceptance : document.querySelector('#requirementAcceptance').value
    }
}
</script>