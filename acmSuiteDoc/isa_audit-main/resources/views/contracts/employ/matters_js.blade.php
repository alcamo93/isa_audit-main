<script>
/**************************** Registers Matters ****************************/

/**
 * open register matter and aspects
 */
function openMattersRegisters(contract, idContract, idCorporate) {
    document.querySelector('#idContract').value = idContract;
    document.querySelector('#idCorporate').value = idCorporate;
    $.post(`{{asset('/contracts/validate')}}`,
    {
        '_token':'{{ csrf_token() }}',
        idContract: idContract,
        idCorporate: idCorporate
    },
    (data) => {
        if (data.status == 'success') {
            openEmploy();
            document.querySelector('#idAplicabilityRegister').value = data.idAplicabilityRegister;
            document.querySelector('#idContract').value = idContract;
            reloadRegistersMatters();
        }
        else if ( data.status == 'warning' ) {
            okAlert(data.title, data.msg, data.status);
        }
        else {
            toastAlert(data.msg, data.status);
        }
    })
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
    });
}
/**
 * Show secction employ
 */
function openEmploy(){
    $('.section-main').addClass('d-none');
    $('.loading').removeClass('d-none');
    setTimeout(function(){
        $('.loading').addClass('d-none');
        $('.section-matters').removeClass('d-none');
    }, 1000);
}
/**
 * close register matter and aspects
 */
function closeMatterSection() {
    $('.section-matters').addClass('d-none');
    $('.loading').removeClass('d-none');
    setTimeout(function(){
        $('.loading').addClass('d-none');
        $('.section-main').removeClass('d-none');
    }, 1000);
}

/**
 * Registers matters
 */
const registersMattersTable = $('#registersMattersTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/contracts/registers/matters',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.idAplicabilityRegister = document.querySelector('#idAplicabilityRegister').value
        }
    },
    columns: [
        { data: 'matter', orderable : true },
        { data: 'contracted', className: 'td-actions text-center', orderable : false, visible: true}
    ],
    columnDefs: [
        {
            render:  ( data, type, row ) => {
                var btnAspects = '';
                var btnManager = '';
                if (data != null) {
                    tooltip = 'Clic para remover materia';
                    var color = 'success';
                    var icon = 'check';
                    btnAspects = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Establecer Aspectos" 
                                    href="javascript:openAspectsRegisters('${row.contract}', ${row.id_contract_matter}, ${row.id_matter})">
                                    <i class="fa fa-list-ul fa-lg"></i>
                                </a>`;
                }else{
                    tooltip = 'Clic para asignar materia';
                    var color = 'danger';
                    var icon = 'times';
                }
                var action = (data === null) ? 'insert' : 'remove';
                var btnContracted = `<a class="btn btn-${color} btn-link btn-xs" data-toggle="tooltip" title="${tooltip}"
                            href="javascript:setMatter(${row.id_matter}, '${action}')">
                            <i class="fa fa-${icon} fa-lg"></i>
                        </a>`;
                return btnContracted+btnAspects;
            },
            targets: 1
        },
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
    }
});
/**
 * Reload registers Datatables
 */
const reloadRegistersMatters = () => { registersMattersTable.ajax.reload() }
const reloadRegistersMattersKeepPage = () => { registersMattersTable.ajax.reload(null, false) }
/**
 * Set matter
 */
function setMatter(idMatter, action){
    $.post(`{{asset('/contracts/matter/update')}}`,
    {
        '_token':'{{ csrf_token() }}',
        idAplicabilityRegister: document.querySelector('#idAplicabilityRegister').value,
        idContract: document.querySelector('#idContract').value,
        idMatter: idMatter,
        action: action
    },
    (data) => {
        toastAlert(data.msg, data.status);
        reloadRegistersMattersKeepPage();
    })
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
    });
}
/**************************** Registers Aspects ****************************/

/**
 * open register aspects
 */
function openAspectsRegisters(contract, idContractMatter, idMatter){
    document.querySelector('#idContractMatter').value = idContractMatter;
    document.querySelector('#idMatter').value = idMatter;
    $('.section-matters').addClass('d-none');
    $('.loading').removeClass('d-none');
    reloadRegistersAspects();
    setTimeout(function(){
        $('.loading').addClass('d-none');
        $('.section-aspects').removeClass('d-none');
    }, 1000);
}
/**
 * close register aspects
 */
function closeAspectsSection(){
    $('.section-aspects').addClass('d-none');
    $('.loading').removeClass('d-none');
    setTimeout(function(){
        $('.loading').addClass('d-none');
        $('.section-matters').removeClass('d-none');
    }, 1000);
}
/**
 * Registers aspects
 */
const registersAspectsTable = $('#registersAspectsTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/contracts/registers/aspects',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.idContractMatter = document.querySelector('#idContractMatter').value,
            data.idMatter = document.querySelector('#idMatter').value
        }
    },
    columns: [
        { data: 'aspect', orderable : true },
        { data: 'contracted', className: 'td-actions text-center', orderable : false, visible: true}
    ],
    columnDefs: [
        {
            render:  ( data, type, row ) => {
                if (data) {
                    tooltip = 'Clic para remover aspecto';
                    var color = 'success';
                    var icon = 'check';
                }else{
                    tooltip = 'Clic para asignar aspecto';
                    var color = 'danger';
                    var icon = 'times';
                }
                var action = (data === null) ? 'insert' : 'remove';
                var btnContracted = `<a class="btn btn-${color} btn-link btn-xs" data-toggle="tooltip" title="${tooltip}"
                            href="javascript:setAspect(${row.id_aspect}, '${action}')">
                            <i class="fa fa-${icon} fa-lg"></i>
                        </a>`;
                return btnContracted;
            },
            targets: 1
        },
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
    }
});
/**
 * Reload registers Datatables
 */
const reloadRegistersAspects = () => { registersAspectsTable.ajax.reload() }
const reloadRegistersAspectsKeepPage = () => { registersAspectsTable.ajax.reload(null, false) }
/**
 * set aspect
 */
function setAspect(idAspect, action){
    $.post(`{{asset('/contracts/aspect/update')}}`,
    {
        '_token':'{{ csrf_token() }}',
        idAplicabilityRegister: document.querySelector('#idAplicabilityRegister').value,
        idContractMatter: document.querySelector('#idContractMatter').value,
        idContract: document.querySelector('#idContract').value,
        idMatter: document.querySelector('#idMatter').value,
        idAspect: idAspect,
        action: action
    },
    (data) => {
        toastAlert(data.msg, data.status);
        reloadRegistersAspectsKeepPage();
    })
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
    });
}
</script>