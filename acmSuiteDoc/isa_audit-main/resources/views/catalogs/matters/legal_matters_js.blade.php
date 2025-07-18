<script>
$(document).ready( () => {
    setFormValidation('#setMattersForm');
    setFormValidation('#setAspectsForm');
});
/*************** Active menu ***************/
activeMenu(8, 'Materias legales');
/**
 * Matters datatables
 */
const matters = {
    data : []
}

const mattersTable = $('#mattersTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/matters',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.filterName = document.querySelector('#filterMatter').value
        }
    },
    columns: [
        { data: 'matter', className: 'text-center', orderable : true },
        { data: 'id_matter', className: 'td-actions text-center', width:200, orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                
                let index = matters.data.findIndex( o => o.id_matter === row.id_matter )
                if( index < 0 ) matters.data.push(row)

                let btnShowAspects = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Visualización de aspectos legales de esta materia" 
                                    href="javascript:showAspects(${data})">
                                    <i class="fa fa-outdent la-lg"></i>
                                </a>`;
                let btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    href="javascript:openMattersModel(${data})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>` : '';
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    href="javascript:deleteMatter(${data})">
                                    <i class="fa fa-times fa-lg"></i>
                                </a>` : '';
                return btnShowAspects+btnEdit+btnDelete;
            },
            targets: 1
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
const reloadMatters = () => { 
    matters.data = [];
    mattersTable.ajax.reload() 
}
const reloadMattersKeepPage = () => { 
    matters.data = [];
    mattersTable.ajax.reload(null, false) 
}
/**
 * Open modal add form
 */
function openMattersModel (idMatter = null)
{
    document.querySelector('#setMattersForm').reset();
    $('#setMattersForm').validate().resetForm();
    $('#setMattersForm').find(".error").removeClass("error");

    if(idMatter){
        let index = matters.data.findIndex( o => o.id_matter === idMatter )
        $('#matter').val(matters.data[index].matter)
        $('#matterDesc').val(matters.data[index].description)
        $('#matter').attr('idMatter', matters.data[index].id_matter)
        $('#matterModalTitle').html('Editar materia')
        $('#setMattersForm').attr('action', '/catalogs/matters/update')
        $('#btnModalMatter').html('Actualizar')
    }
    else{ 
        $('#matterModalTitle').html('Nueva materia')
        $('#setMattersForm').attr('action', '/catalogs/matters/set')
        $('#btnModalMatter').html('Registrar')
    }

    $('#mattersModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set Question form 
 */
$('#setMattersForm').submit( (event) => {
    event.preventDefault();
    let action = $('#setMattersForm').attr('action')
    if($('#setMattersForm').valid()) {
        showLoading('#mattersModal')
        //handler notificaction
        $.post(action, {
            '_token': '{{ csrf_token() }}',
            idMatter: document.querySelector('#matter').getAttribute('idMatter'),
            matter: document.querySelector('#matter').value,
            desc: document.querySelector('#matterDesc').value
        },
        (data) => {
            toastAlert(data.msg, data.status);
            showLoading('#mattersModal')
            if(data.status == 'success'){
                reloadMattersKeepPage();
                $('#mattersModal').modal('hide');
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#mattersModal');
        })
    }
});
/**
 * Delete question
 */
function deleteMatter(idElement)
{
    let index = matters.data.findIndex( o => o.id_matter === idElement )
    Swal.fire({
        title: `¿Estas seguro de eliminar la materia ${matters.data[index].matter} ?`,
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
            $.post('{{asset('/catalogs/matters/delete')}}',
            {
                '_token':'{{ csrf_token() }}',
                idMatter: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success'){
                    reloadMattersKeepPage();
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