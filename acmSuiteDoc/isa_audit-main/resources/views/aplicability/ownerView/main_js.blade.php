<script>
/*************************** Corporates to Aplicability ***************************/
/**
 * Aplicability register tables
 */
const aplicabilityRegisterTable = $('#aplicabilityRegisterTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: "/assets/lenguage.json"
    },
    ajax: { 
        url: '/aplicability/resgisters',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.fIdStatus = document.querySelector('#filterIdStatus').value,
            data.fIdCustomer = document.querySelector('#filterIdCustomer').value,
            data.fIdCorporate = document.querySelector('#filterIdCorporate').value
        }
    },
    columns: [
        { data: 'cust_trademark', className: 'text-center', orderable : true },
        { data: 'corp_tradename', className: 'text-center', orderable : true },
        { data: 'contract', className: 'text-center', orderable : true },
        { data: 'status', className: 'text-center', orderable : true },
        { data: 'id_aplicability_register', className: 'text-center td-actions', orderable : false, visible: true}
    ],
    columnDefs: [
        {
            render: ( data, type, row ) => {
                let color = '';
                switch (data) {
                    case 'Sin clasificar':
                        color = 'warning';
                        break;
                    case 'Clasificado':
                        color = 'success';
                        break;
                    case 'Evaluando':
                        color = 'info';
                        break;
                    case 'Finalizado':
                        color = 'success';
                        break;
                    default:
                        break;
                }
                return `<span class="badge badge-${color} text-white">${data}</span>`;
            },
            targets: 3
        },
        {
            render: (data, type, row) => {     
                let btnTable = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Mostrar Evaluaciones" 
                                        href="javascript:setAspectsToAplicability(${row.id_aplicability_register}, '${row.cust_trademark}', '${row.corp_tradename}')">
                                        <i class="fa fa-table fa-lg"></i>
                                    </a>`;
                let btnFinalizar = '';
                if (row.status == 'Clasificado') { 
                    btnFinalizar = `<a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Concluir Aplicabilidad" 
                                    href="javascript:setInAudit(${row.id_aplicability_register}, ${row.id_contract})">
                                    <i class="fa fa-check-circle-o fa-lg"></i>
                                </a>`;
                }
                return btnTable+btnFinalizar;
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
/**
 * Reload contracts Datatables
 */
const reloadAplicabilityRegister = () => { aplicabilityRegisterTable.ajax.reload() }
const reloadAplicabilityRegisterKeepPage = () => { aplicabilityRegisterTable.ajax.reload(null, false) }
/**
 * Set aplicability in Audit
 */
function setInAudit(idAplicabilityRegister, idContract){
    Swal.fire({
        title: '¿Estás seguro de marcar como finalizado?',
        text: "Una vez finalizado no se podra editar nada de aplicabilidad",
        icon: 'warning',
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, finalizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $('.loading').removeClass('d-none');
            $.post('/aplicability/audit/set', {
                '_token': '{{ csrf_token() }}', 
                idAplicabilityRegister: idAplicabilityRegister,
                idContract: idContract
            },
            (data) => {
                $('.loading').addClass('d-none');
                if (data.status == 'success' || data.status == 'warning') {
                    okAlert(data.title, data.msg, data.status);
                    reloadAplicabilityRegisterKeepPage();
                }else{
                    toastAlert(data.msg, data.status);
                }
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
                $('.loading').addClass('d-none');
            });
        }
    });
}
</script>
