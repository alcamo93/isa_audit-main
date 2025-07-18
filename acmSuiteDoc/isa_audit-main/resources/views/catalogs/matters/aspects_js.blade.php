<script>
/***************************************************************************** */
/*******************************   Aspects   ********************************* */
/***************************************************************************** */

const aspects = {
    data : [],
    idMatter : null
}

function showAspects(idMatter)
{
    aspects.idMatter = idMatter
    $('.matters').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        reloadAspects();
        $('.loading').addClass('d-none')
        $('.aspects').removeClass('d-none')
    }, 1000);
}

function closeAspects()
{
    $('.aspects').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        aspects.data = []
        aspects.idMatter = null
        reloadAspects()
        $('.loading').addClass('d-none')
        $('.matters').removeClass('d-none')
        $('#filterAspects').val('')
    }, 1000);
}

const aspectsTable = $('#aspectsTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/aspects',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.idMatter = aspects.idMatter,
            data.filterName = document.querySelector('#filterAspects').value
        }
    },
    columns: [
        { data: 'order', className: 'text-center', orderable : true },
        { data: 'aspect', className: 'text-center', orderable : true },
        { data: 'id_aspect', className: 'td-actions text-center', width:200, orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                
                let index = aspects.data.findIndex( o => o.id_aspect === row.id_aspect )
                if( index < 0 ) aspects.data.push(row)

                let btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    href="javascript:openAspectsModel(${data})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>` : '';
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    href="javascript:deleteAspect(${data})">
                                    <i class="fa fa-times fa-lg"></i>
                                </a>` : '';
                return btnEdit+btnDelete;
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
 * Reload questions Datatables
 */
const reloadAspects = () => { 
    aspects.data = [];
    aspectsTable.ajax.reload() 
}
const reloadAspectsKeepPage = () => { 
    aspects.data = [];
    aspectsTable.ajax.reload(null, false) 
}
/**
 * Open modal add form
 */
function openAspectsModel (idAspect = null)
{
    document.querySelector('#setAspectsForm').reset();
    $('#setAspectsForm').validate().resetForm();
    $('#setAspectsForm').find(".error").removeClass("error");
    $('#aspect').attr('idMatter', aspects.idMatter)
    if(idAspect){
        let index = aspects.data.findIndex( o => o.id_aspect === idAspect )
        $('#aspect').val(aspects.data[index].aspect)
        $('#order').val(aspects.data[index].order)
        $('#aspect').attr('idAspect', aspects.data[index].id_aspect)
        $('#aspectModalTitle').html('Editar Aspecto')
        $('#setAspectsForm').attr('action', '/catalogs/aspects/update')
        $('#btnModalAspects').html('Actualizar')
    }
    else{ 
        $('#aspectModalTitle').html('Nuevo aspecto')
        $('#setAspectsForm').attr('action', '/catalogs/aspects/set')
        $('#btnModalAspects').html('Registrar')
    }

    $('#aspectsModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set Question form 
 */
$('#setAspectsForm').submit( (event) => {
    event.preventDefault();
    let action = $('#setAspectsForm').attr('action')
    if($('#setAspectsForm').valid()) {
        showLoading('#aspectsModal')
        //handler notificaction
        $.post(action, {
            '_token': '{{ csrf_token() }}',
            idAspect: document.querySelector('#aspect').getAttribute('idAspect'),
            idMatter: document.querySelector('#aspect').getAttribute('idMatter'),
            aspect: document.querySelector('#aspect').value,
            order: document.querySelector('#order').value
        },
        (data) => {
            toastAlert(data.msg, data.status);
            showLoading('#aspectsModal')
            if(data.status == 'success'){
                reloadAspectsKeepPage();
                $('#aspectsModal').modal('hide');
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#aspectsModal');
        })
    }
});
/**
 * Delete question
 */
function deleteAspect(idElement)
{
    let index = aspects.data.findIndex( o => o.id_aspect === idElement )
    Swal.fire({
        title: `¿Estas seguro de eliminar la materia ${aspects.data[index].aspect} ?`,
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
            $.post('/catalogs/aspects/delete',
            {
                '_token':'{{ csrf_token() }}',
                idAspect: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success'){
                    reloadAspectsKeepPage();
                }
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
                $('.loading').addClass('d-none')
            })
        }
    });
}

</script>