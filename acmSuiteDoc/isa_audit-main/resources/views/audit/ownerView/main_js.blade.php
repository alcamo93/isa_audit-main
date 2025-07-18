<script>
/*************************** Corporates to Audit ***************************/
/**
 * Audit register tables
 */
const auditRegisterTable = $('#auditRegisterTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: "/assets/lenguage.json"
    },
    ajax: { 
        url: '/audit/resgisters',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.fIdStatus = document.querySelector('#filterIdStatus').value,
            data.fIdCustomer = document.querySelector('#filterIdCustomer').value,
            data.fIdCorporate = document.querySelector('#filterIdCorporate').value
        }
    },
    columns: [
        { data: 'cust_trademark', orderable : true },
        { data: 'corp_tradename', orderable : true },
        { data: 'contract', className: 'text-center', orderable : true },
        { data: 'total', className: 'text-center', orderable : true },
        { data: 'status', className: 'text-center', orderable : true },
        { data: 'id_audit_register', className: 'text-center td-actions', orderable : false, visible: true}
    ],
    columnDefs: [
        {
            render: ( data, type, row ) => {
                return `${Math.round(data)}%`;
            },
            targets: 3
        },
        {
            render: ( data, type, row ) => {
                var color = '';
                switch (data) {
                    case 'Sin auditar':
                        color = 'warning';
                        break;
                    case 'Auditado':
                        color = 'success';
                        break;
                    case 'Auditando':
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
            targets: 4
        },
        {
            render: (data, type, row) => {     
                var btnTable = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Mostrar Evaluaciones" 
                                    href="javascript:setAspectsToAudit(${data}, '${row.cust_trademark}', '${row.corp_tradename}', ${row.id_contract})">
                                    <i class="fa fa-table fa-lg"></i>
                                </a>`;
                var btnFinalizar = '';
                var btnReport = '';
                if (row.status == 'Auditado') {
                    var btnFinalizar = `<a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Concluir Auditoria" 
                                    href="javascript:setInActionPlan(${row.id_audit_register}, ${row.id_contract})">
                                    <i class="fa fa-check-circle-o fa-lg"></i>
                                </a>`;
                }
                if (row.status == 'Finalizado') {
                    var btnReport = `<a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Generar reporte de Auditoria" 
                                    href="{{asset('/audit/report')}}/${row.id_corporate}/${row.id_audit_register}/${row.id_contract}">
                                    <i class="fa fa-file-excel-o fa-lg"></i>
                                </a>`;
                }
                return btnTable+btnFinalizar+btnReport;
            },
            targets: 5
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
const reloadAuditRegister = () => { auditRegisterTable.ajax.reload(null, false) }
/**
 * Set data in action plan
 */
function setInActionPlan(idAuditRegister, idContract){
    Swal.fire({
        title: '¿Estás seguro de marcar como finalizado?',
        text: "Una vez finalizado no se podra editar nada de auditoria",
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
            $.post('{{ asset('/audit/action/set') }}', {
                '_token': '{{ csrf_token() }}',
                idAuditRegister: idAuditRegister,
                idContract: idContract
            },
            (data) => {
                $('.loading').addClass('d-none');
                if (data.status == 'success' || data.status == 'warning') {
                    okAlert(data.title, data.msg, data.status);
                    reloadAuditRegister();
                }else{
                    toastAlert(data.msg, data.status);
                }
            });   
        }
    });
}
</script>