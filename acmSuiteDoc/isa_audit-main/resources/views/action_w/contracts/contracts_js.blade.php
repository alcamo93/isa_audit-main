<script>
const contractsTable = $('#contractsTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/action/register',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.idCustomer = document.querySelector('#filterIdCustomer').value,
            data.idCorporate = document.querySelector('#filterIdCorporate').value,
            data.auditProcess = document.querySelector('#filterProcess').value,
            data.idUser = USER
        }
    },
    columnDefs: [
        {
            data: data => {
                return data.process.customer.cust_trademark;
            },
            className: 'text-center',
            orderable: false,
            targets: 0
        },
        {
            data: data => {
                return data.process.corporate.corp_tradename;
            },
            className: 'text-center',
            orderable: false,
            targets: 1
        },
        {
            data: data => {
                return data.process.audit_processes;
            },
            className: 'text-center',
            orderable: false,
            targets: 2
        },
        {
            data: data => {
                return formatDate(data.process.audit.end_date, 'date');
            },
            className: 'text-center',
            orderable: false,
            targets: 3
        },
        {
            data: data => {
                const {id_audit_processes, audit_processes, evaluate_risk, specification_scope, customer, corporate, scope} = data.process;
                return `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Mostrar Plan de Acción" 
                            onclick="showActionPlan(${data.id_action_register}, ${id_audit_processes}, '${customer.cust_trademark}', 
                                '${corporate.corp_tradename}', '${audit_processes}', ${scope.id_scope}, '${scope.scope}', 
                                '${specification_scope}', ${evaluate_risk}, ${customer.id_customer}, ${corporate.id_corporate})">
                            <i class="fa fa-list-alt la-lg"></i>
                        </a>`;
            },
            className: 'text-center',
            orderable: false,
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
 * Reload select basis Datatables
 */
const reloadContracts = () => { contractsTable.ajax.reload() }

</script>