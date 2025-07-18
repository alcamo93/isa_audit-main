<script>
$(document).ready( () => {
    setFormValidation('#setContractForm');
    setFormValidation('#updateContractForm');
    setFormValidation('#renewContractForm');
});
activeMenu(4, 'Contratos');

/**
 * Status
 */
const ACTIVE = 1;
const INACTIVE = 2;
/**
 * contracts datatables
 */
const contractsTable = $('#contractsTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/contracts',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.fContract = document.querySelector('#filterContract').value,
            data.fIdStatus = document.querySelector('#filterIdStatus').value,
            data.fIdCustomer = document.querySelector('#filterIdCustomer').value,
            data.fIdCorporate = document.querySelector('#filterIdCorporate').value
        }
    },
    columns: [
        { data: 'contract', orderable : false },
        { data: 'cust_trademark', orderable : true },
        { data: 'corp_tradename', orderable : true },
        { data: 'start_date', className: 'text-center', orderable : true },
        { data: 'end_date', className: 'text-center', orderable : true },
        { data: 'status', className: 'text-center', orderable : false, visible: true},
        { data: 'id_contract', className: 'td-actions text-center', orderable : false, visible: ACTIONS}
    ],
    columnDefs: [
        {
            render:  ( data, type, row ) => {
                
                let status = ''
                let statusChange = (row.id_status == ACTIVE) ? INACTIVE : ACTIVE
                if(data == 'Activo') status = 'checked'
                let btn = (MODIFY) ? `<input 
                    class="my-switch" 
                    type="checkbox" 
                    data-toggle="switch"
                    onchange="updateStatusContract(${row.id_corporate}, ${row.id_contract}, ${statusChange})" 
                    ${status}>
                    <span class="toggle" data-toggle="tooltip" title="Ver más" ></span>`: ''
                return btn

                 
            },
            targets: 5
        },
        {
            render: (data, type, row) => {
                let btnWatch = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ver" 
                                    href="javascript:openWatchContract('${row.contract}', ${row.id_contract})">
                                    <i class="fa fa-eye fa-lg"></i>
                                </a>`;
                let btnUpdate = '';

                if(MODIFY)
                {
                    switch(row.id_status)
                    {
                        case 1:
                            btnUpdate = `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Extender" 
                                                href="javascript:openUpdateContract(${row.id_contract})">
                                                <i class="fa fa-calendar-plus-o fa-lg"></i>
                                            </a>` ;
                            break;
                        case 2:
                            btnUpdate = `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Renovar" 
                                                href="javascript:openRenewContract(${row.id_contract})">
                                                <i class="fa fa-calendar-plus-o fa-lg"></i>
                                            </a>` ;
                            break;

                        default:
                            btnUpdate = '';
                        break;
                    }
                }
                
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    href="javascript:deleteContract('${row.contract}', ${row.id_contract})">
                                    <i class="fa fa-times fa-lg"></i>
                                </a>` : '';

                let btnModify = (MODIFY) ? `<button  class="btn btn-warning btn-link btn-xs btn-edit-contract" data-toggle="tooltip" title=""
                                    data-original-title="Editar" data-contract="${row.id_contract}">
                                    <i class="fa fa-edit fa-lg"></i>
                                </button >` : '';

                return btnWatch+btnUpdate+btnDelete+btnModify;
            },
            targets: 6
        }
    ],
    rowCallback: function ( row, data ) {
        $('input.my-switch', row).attr({
            'data-on-color':'success', 
            'data-off-color':'dark', 
            'data-on-text':'<i class="fa fa-check"></i>', 
            'data-off-text':'<i class="fa fa-times"></i>'
        }).bootstrapSwitch();
    },
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
    }
});
/**
 * Reload contracts Datatables
 */
const reloadContracts = () => { contractsTable.ajax.reload() }
const reloadContractsKeepPage = () => { contractsTable.ajax.reload(null, false) }
/**
 * Open modal add form
 */
function openAddContract () {
    document.querySelector('#setContractForm').reset();
    $('#idContract').val(0);
    $('#titleAdd').html('Agregar Contrato');
    $('.not-update-data').removeClass('d-none');
    $('.update-data').addClass('d-none');

    $('#setContractForm').validate().resetForm();
    $('#setContractForm').find(".error").removeClass("error");
    $('#licensesDataTable tbody').find('tr').remove();
    setCorporates(0, '#s-idCorporate');
    var tbody = `<tr><th class="text-center" colspan="4">Selecciona una licencia</i></th></tr>`;
    $('#licensesDataTable tbody').append(tbody);
    document.querySelector('#s-dateStart').disabled = true;
    $('#addModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set contract form 
 */
$('#setContractForm').submit( (event) => {
    event.preventDefault();
    if($('#setContractForm').valid()) {
        showLoading('#addModal');
        //handler notificaction
        $.post('/contracts/set', {
            '_token': '{{ csrf_token() }}',
            idContract: document.querySelector('#idContract').value,
            contract: document.querySelector('#s-contract').value,
            idCustomer: document.querySelector('#s-idCustomer').value,
            idCorporate: document.querySelector('#s-idCorporate').value,
            idLicense: document.querySelector('#s-idLicense').value,
            dateStart: document.querySelector('#s-dateStart').value,
            dateEnd: document.querySelector('#s-dateEnd').value
        },
        (data) => {
            showLoading('#addModal');
            switch (data.status) {
                case 'success':
                    toastAlert(data.msg, data.status);
                    reloadContractsKeepPage();
                    $('#addModal').modal('hide');
                    break;
                case 'warning':
                    okAlert(data.title, data.msg, data.status);
                    break;
                case 'error':
                    toastAlert(data.msg, data.status);
                    reloadContractsKeepPage();
                    $('#addModal').modal('hide');
                    break;
                default:
                    break;
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#addModal');
        });
    }
});
/**
 * Open modal update form
 */
function openUpdateContract(idContract) {
    document.querySelector('#updateContractForm').reset();
    $('#updateContractForm').validate().resetForm();
    $('#updateContractForm').find(".error").removeClass("error");
    $('#licensesUpdateDataTable tbody').find('tr').remove();
    var tbody = `<tr>
                <th class="text-center" colspan="4">Selecciona una licencia</i></th>
            </tr>`;
    $('#up-idContract').val(idContract)
    $('#licensesUpdateDataTable tbody').append(tbody);
    $('#updateModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Open modal renew form
 */
function openRenewContract (idContract) {
    document.querySelector('#renewContractForm').reset();
    $('#renewContractForm').validate().resetForm();
    $('#renewContractForm').find(".error").removeClass("error");
    $('#licensesRenewDataTable tbody').find('tr').remove();
    var tbody = `<tr>
                <th class="text-center" colspan="4">Selecciona una licencia</i></th>
            </tr>`;
    $('#r-idContract').val(idContract)
    $('#licensesRenewDataTable tbody').append(tbody);
    document.querySelector('#r-dateStart').disabled = true;
    $('#renewModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Send update extension
 */
$('.btnContractExtension').click( (e)=> {
    e.preventDefault()
    let form = $(e.currentTarget).attr('f')
    if($(form).valid()) setContractExtension($(e.currentTarget).attr('et'), $(e.currentTarget).attr('m'))
})

/**
 * Open modal edit form
 */
function openWatchContract (contract, idContract) {
    document.querySelector('#titleEdit').innerHTML = `Información: "${contract}"`;
    // get contract
    getContractInfo(idContract)
    .then((data)=>{
        if (data.contract.length > 0) {
            let contract = data.contract[0];
            $('#idContract').html(contract.id_contract);
            $('#u-contract').html(contract.contract);
            $('#u-idCustomer').html(contract.cust_trademark);
            $('#u-idCorporate').html(contract.corp_tradename);
            $('#u-dateStart').html(convertDate(contract.start_date));
            $('#u-dateEnd').html(convertDate(contract.end_date));
            // Contract Details 
            let detail = data.details[0];
            $('#idContractDetail').val(detail.id_contract_detail);
            $('#u-idLicense').html(detail.license);
            $('#u-usrGlobals').html(detail.usr_global);
            $('#u-usrCorporates').html(detail.usr_corporate);
            $('#u-usrOperatives').html(detail.usr_operative);
            $('#u-idPeriod').val(detail.id_period);
            $('#u-idPeriod-text').html($("#u-idPeriod option:selected").html());
            $('#fullViewModal').modal({backdrop:'static', keyboard: false});
        }
        else reloadContractsKeepPage();
    })
    .catch((e)=>{
        toastAlert(e.statusText, 'error');
    })
}
/**
 * Handler to submit update contract form 
 
$('#updateContractForm').submit( (event) => {
    event.preventDefault();
    if($('#updateContractForm').valid()) {
        showLoading('#editModal');
        // Set contract data
        var contract = {};
        contract['idContract'] = document.querySelector('#idContract').value;
        contract['contract'] = document.querySelector('#u-contract').value;
        contract['dateStart'] = document.querySelector('#u-dateStart').value;
        contract['dateEnd'] = document.querySelector('#u-dateEnd').value;
        // Set detail data
        var detail = {};
        detail['idContractDetail'] = document.querySelector('#idContractDetail').value;
        detail['usrGlobals'] = document.querySelector('#u-usrGlobals').value;
        detail['usrCorporates'] = document.querySelector('#u-usrCorporates').value;
        detail['usrOperatives'] = document.querySelector('#u-usrOperatives').value;
        detail['idPeriod'] = document.querySelector('#u-idPeriod').value;
        //handler notificaction
        $.post('{{ asset('/contracts/update') }}', {
            '_token': '{{ csrf_token() }}',
            contract: contract,
            detail: detail
        },
        (data) => {
            showLoading('#editModal');
            toastAlert(data.msg, data.status);
            if(data.status == 'success' || data.status == 'error'){
                reloadContractsKeepPage();
                $('#editModal').modal('hide');
            }
        });
    }
});*/
/**
 * Delete contract
 */
function deleteContract(contract, idElement) {
    Swal.fire({
        title: `¿Estas seguro de eliminar "${contract}"?`,
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
            $.post('{{asset('/contracts/delete')}}',
            {
                '_token':'{{ csrf_token() }}',
                idContract: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                if(data.status == 'success'){
                    reloadContractsKeepPage();
                }
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
            });
        }
    });
}

/**
 *
 */
$(document).on('click', '.btn-edit-contract', function(event){
    let idContract = $(event.currentTarget).data('contract');
    getContractInfo(idContract).then((data) => {
        $('#addModal').modal('show');
        $('#titleAdd').html('Editar Contrato');
        $('.not-update-data').addClass('d-none');
        $('.update-data').removeClass('d-none');
        $('#customerName').empty().html(data.contract[0].cust_trademark);
        $('#corporateName').empty().html(data.contract[0].corp_tradename);
        document.querySelector('#idContract').value = data.contract[0].id_contract;
        document.querySelector('#s-contract').value = data.contract[0].contract;

        $('#licensesDataTable tbody').find('tr').remove();
        let tbody = '';
        for (let index = 0; index < data.details.length; index++) {
            tbody +=
                `<tr>
                    <th class="text-center"><span class="badge badge-secondary"> ${data.details[index].usr_global} ${(data.details[index].usr_global != 1) ? 'Usuarios' : 'Usuario'} </span></th>
                    <th class="text-center"><span class="badge badge-secondary"> ${data.details[index].usr_corporate} ${(data.details[index].usr_corporate != 1) ? 'Usuarios' : 'Usuario'} </span></th>
                    <th class="text-center"><span class="badge badge-secondary"> ${data.details[index].usr_operative} ${(data.details[index].usr_operative != 1) ? 'Usuarios' : 'Usuario'} </span></th>
                    <th class="text-center"><span class="badge badge-primary"> ${data.details[index].period} </span></th>
                </tr>`;
        }
        $('#licensesDataTable').append(tbody);
    }).catch((e) => {

    });
});
/**
 * Filter licenses with Select2
 */
// $('#s-idLicense').select2({
//     language: {
//         noResults: function() {
//             return "No hay resultado";        
//         },
//         searching: function() {
//             return "Buscando..";
//         }
//     },
//     dropdownParent: $('#addModal'),
//     ajax: {
//         url: '{{asset('/licences/filter')}}',
//         dataType: 'json',
//         type: "GET",
//         data: function (params) {
//             return  {
//                 filter: params.term   
//             };
//         },
//         processResults: function (data) {
//             return {
//                 results: data
//             };
//         },
//         cache: true
//     }
// });

function getDetailsLicenses(idLicense, table, selectorDateStart, selectorDateEnd, setStartDate = false, idContract = null ){
    document.querySelector(selectorDateStart).value = '';
    document.querySelector(selectorDateEnd).value = '';
    $.get(`{{asset('/licenses')}}/${idLicense}`,
    {
        '_token':'{{ csrf_token() }}'
    },
    (data) => {
        var tbody = '';
        if (data.length > 0) {
            $(table+' tbody').find('tr').remove();
            for (let index = 0; index < data.length; index++) {
                tbody +=
                 `<tr>
                    <th class="text-center"><span class="badge badge-secondary"> ${data[index].usr_global} ${(data[index].usr_global != 1) ? 'Usuarios' : 'Usuario'} </span></th>
                    <th class="text-center"><span class="badge badge-secondary"> ${data[index].usr_corporate} ${(data[index].usr_corporate != 1) ? 'Usuarios' : 'Usuario'} </span></th>
                    <th class="text-center"><span class="badge badge-secondary"> ${data[index].usr_operative} ${(data[index].usr_operative != 1) ? 'Usuarios' : 'Usuario'} </span></th>
                    <th class="text-center"><span class="badge badge-primary"> ${data[index].period} </span></th>
                </tr>`;
            }
            $(table+' tbody').append(tbody);
            if(setStartDate)
            {   
                getContractInfo($(idContract).val())
                .then((data) => {
                    let contract = data.contract[0]
                    let startDate = new Date(contract.end_date + ' 00:00:00')
                    startDate.setDate(startDate.getDate() + 1)
                    $(selectorDateStart).val(startDate.toISOString().split('T')[0])
                    $(selectorDateStart).trigger('change')
                })
                .catch((e) => {
                    toastAlert(e, 'error');
                })
            }
            else $(selectorDateStart).attr('disabled', false);
        }
    })
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
    });
}
/**
 * Calculate date end
 */
function calculateDateEnd(event, selectorDateEnd, selectorLicense){
    var dateStart = event.target.value;
    $.post('/contracts/set/dates', {
        '_token': '{{ csrf_token() }}',
        dateStart: dateStart,
        idLicense: document.querySelector(selectorLicense).value
    },
    (data) => {
        document.querySelector(selectorDateEnd).value = data;
    })
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
    });
}
/**
 * Calculate date in update
 */
function calculateUpdate(){
    var dateStart = document.querySelector('#u-dateStart').value;
    var idPeriod = document.querySelector('#u-idPeriod').value;
    $.post('/contracts/update/dates', {
        '_token': '{{ csrf_token() }}',
        dateStart: dateStart,
        idPeriod: idPeriod
    },
    (data) => {
        if (data == 'error') {
            toastAlert('Periodo no valido', 'warning');
        }else{
            document.querySelector('#u-dateEnd').value = data;
        }
    });
}
/**
 * Update/Renew contract
 */
function setContractExtension(type, modal)
{
    showLoading(modal)
    let pre = ''
    if( type == '0' ) pre = '#up-'
    else pre = '#r-'

    $.post(
        '/contracts/set-extension',
        {
            '_token': '{{ csrf_token() }}',
            type,
            startDate: $(pre+'dateStart').val(),
            endDate: $(pre+'dateEnd').val(),
            idContract : $(pre+'idContract').val(),
            idLicense: $(pre+'idLicense').val()
        },
        (data) => {
            showLoading(modal)
            toastAlert(data.msg, data.status);
            reloadContractsKeepPage();
            $(modal).modal('hide');
        }
    )
    .fail((e) => {
        showLoading('#updateModal')
    })
}

/**
 * update contract status 
 */
function updateStatusContract(idCorporate, idContract, idStatus){
    $.post('/contracts/status', {
        '_token': '{{ csrf_token() }}',
        idCorporate: idCorporate,
        idContract: idContract,
        idStatus: idStatus
    },
    (data) => {
        if (data.status == 'success') {
            toastAlert(data.msg, data.status);
            reloadContractsKeepPage();
        }
        else
        {
            okAlert(data.title, data.msg, data.status);
            reloadContractsKeepPage();
        }
    });
}
/**
 * Get contract Info
 */
function getContractInfo(idContract)
{
    return new Promise ((resolve, reject) => {
        $.get(`/contracts/${idContract}`,
            {
                '_token':'{{ csrf_token() }}'
            },
            (data) => {
                resolve(data)
            }
        )
        .fail((e)=>{
            reject(e)
        })
    })
}

/**
 * convert date from AAAA/MM/DD to DD/MM/AAA
 */
function convertDate(date)
{
    date = date.split('-')
    return date[2]+'-'+date[1]+'-'+date[0]
}

</script>
