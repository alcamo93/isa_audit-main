<script>
/**
 * Open requirements table 
 */

function openObRequirementsModal(idObligation) {
    $('.loading').removeClass('d-none')
    $.post('/obligations/requeriments/get', {
        _token: document.querySelector('meta[name="csrf-token"]').content,
        idObligation: idObligation
    },
    (data) => {
        const { data_requirements } = data;
        data.forEach((item, index, array) => {
            requirementsTable.row.add(item, index).draw(false);
        });
        $('#requirementsListModal').modal({backdrop:'static', keyboard: false});
        $('.loading').addClass('d-none');
    })
    .fail(e => {
        $('.loading').addClass('d-none');
        toastAlert(e.statusText, 'error');
    });
}

const requirementsTable = $('#requirementsTable').DataTable({
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    processing: true,
    serverSide: false,
    paging: true,
    columns: [
        {data: 'no_requirement', orderable: true},
        {data: 'requirement', orderable: true},
        {data: 'description', orderable: true},
    ]
});

$('#requirementsListModal').on('hidden.bs.modal', function (e) {
    $('#requirementsTable').dataTable().fnClearTable();
});
</script>