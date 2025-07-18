<script>
/**
 * Open requirements table 
 */

function openObRequirementsModal(obligation)
{
    $('.loading').removeClass('d-none')

    $.post('{{asset('/obligations/requeriments/get')}}', {
                '_token': '{{ csrf_token() }}',
                idObligation : obligation
            },
            function (response) {
                var data = response.data_requirements;
                data.forEach(function (item, index, array) {
                    requirementsTable.row.add(item, index).draw(false);
                });
               
            });

    $('#requirementsListModal').modal({backdrop:'static', keyboard: false});
    $('.loading').addClass('d-none')

}

const requirementsTable = $('#requirementsTable').DataTable({
        dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
        language: {
            url: '/assets/lenguage.json'
        },
        "processing": true,
        "serverSide": false,
        "paging": true,
        "columns": [
            {"data": "no_requirement", "orderable": true},
            {"data": "requirement", "orderable": true},
            {"data": "description", "orderable": true},
        ]
    });

    $('#requirementsListModal').on('hidden.bs.modal', function (e) {
            $('#requirementsTable').dataTable().fnClearTable();
        });

</script>