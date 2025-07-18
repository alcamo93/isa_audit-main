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
            data._token = '{{ csrf_token() }}',
            data.idCustomer = document.querySelector('#filterIdCustomer').value,
            data.idCorporate = document.querySelector('#filterIdCorporate').value
        }
    },
    columns: [
        { data: 'corp_tradename', className: 'td-actions text-center', orderable : true },
        { data: 'contract', className: 'td-actions text-center', orderable : false },
        { data: 'id_action_register', className: 'td-actions text-center', orderable : false },
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                return `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Mostrar Plan de Acción" 
                            href="javascript:showActionPlan(${data}, '${row.cust_trademark}', '${row.corp_tradename}')">
                            <i class="fa fa-list-alt la-lg"></i>
                        </a>`;
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
 * Reload select basis Datatables
 */
const reloadContracts = () => { contractsTable.ajax.reload(null, false) }

</script>