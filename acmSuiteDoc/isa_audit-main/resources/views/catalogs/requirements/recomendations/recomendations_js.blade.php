<script>

const recomendations = {
    id : null,
    origin : null,
    single: null,
    idRecomendation: null
}
/**
* Open recomendations modal
*/
function openRecomendations(id, origin)
{
    $('.loading').removeClass('d-none')
    resetRecomendation()
    recomendations.id = id
    let index = null
    switch(origin)
    {
        case 1:
            recomendations.origin = 'requirements'
            recomendations.single = 'requirement'
            index = requirements.data.findIndex( o => o.id_requirement === id )
            $('#recomendationModalTitle').html('Recomendaciones para el requerimiento '+requirements.data[index].no_requirement)
        break;
        case 2:
            recomendations.origin = 'subrequirements'
            recomendations.single = 'subrequirement'
            index = subrequirements.data.findIndex( o => o.id_subrequirement === id )
            $('#recomendationModalTitle').html('Recomendaciones para el subrequerimiento ('+subrequirements.data[index].no_subrequirement+')')
        break;
    }
    document.querySelector('#recomendationForm').reset();
    $('#recomendationForm').validate().resetForm();
    $('#recomendationForm').find(".error").removeClass("error");
    getRequirementsRecomendation()
    .then(()=>{
        $('.loading').addClass('d-none')
        $('#recomendationModal').modal({backdrop:'static', keyboard: false});   
    })
    .catch((e)=>{
        $('.loading').addClass('d-none')
    })
}
/**
* Show the form for add a new recomendation
*/
$('#newRecomendation').click((e)=>{
    e.preventDefault()
    $('.addNew').addClass('d-none')
    $('.showSubmit').removeClass('d-none')
});
/**
* Get the recomendations list for requirement selected 
*/
function getRequirementsRecomendation()
{
    return new Promise ((resolve, reject) => {
        $.post(`/catalogs/${recomendations.origin}/${recomendations.single}-recomendations`,
            {
                '_token': '{{ csrf_token() }}',
                id : recomendations.id
            }, 
            (data) => {
                let html = ''
                data.forEach((element, index, array)=>{
                    html += `<div class="card">
                                <div class="card-body">
                                    <p class="card-text">${element.recomendation}</p>
                                    <a  href="javascript:deleteRecomendation(${element.id_recomendation})" data-toggle="tooltip" title="Eliminar"
                                        class="btn btn-danger float-right btn-sm mr-1" tabindex="-1" role="button" aria-disabled="true">
                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                    </a>
                                    <a  href="javascript:editRecomendation(${element.id_recomendation})" data-toggle="tooltip" title="Editar"
                                        class="btn btn-warning float-right btn-sm mr-1" tabindex="-1" role="button" aria-disabled="true">
                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>`
                });
                $('.recomendations').html(html)
                resolve(true)
            }
        )
        .fail((e)=>{
            reject(false)
            toastAlert(e.statusText, 'error');
        })
    })    
}
/**
* Reset recomendation form 
*/
function resetRecomendation()
{
    $('.addNew').removeClass('d-none')
    $('.showSubmit').addClass('d-none')
    $('#addRecomendation').val('')
}
/**
* Send new recomendation
*/
$('#btnSubmitRecomendation').click((e)=>{
    e.preventDefault()
    showLoading('#recomendationModal')
    $.post(`/catalogs/${recomendations.origin}/recomendations/set`,
        {
            '_token': '{{ csrf_token() }}',
            id : recomendations.id, 
            recommendation : document.querySelector('#addRecomendation').value
        }, 
        (data) => {
            toastAlert(data.msg, data.status);
            if(data.status == 'success'){ 
                resetRecomendation()
                getRequirementsRecomendation()
                .then(()=>{
                    setTimeout(() => {
                        showLoading('#recomendationModal')
                    }, 1000);
                })  
            }
        }
    )
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
        showLoading('#recomendationModal')
    })
});
/**
* Delete recomendation
*/
function deleteRecomendation(idElement)
{
    Swal.fire({
        title: '¿Estas seguro de eliminar la recomendacion?',
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
            showLoading('#recomendationModal')
            $.post(`/catalogs/${recomendations.origin}/recomendations/delete`,
            {
                '_token':'{{ csrf_token() }}',
                idRecomendation: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                if(data.status == 'success') resetRecomendation()
                getRequirementsRecomendation()
                .then(()=>{
                    setTimeout(() => {
                        showLoading('#recomendationModal')
                    }, 1000);
                })
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
                showLoading('#recomendationModal')
            })
        }
    });
}
/**
 * Edit recomendation
 */
function editRecomendation(idElement) {
    $('#recomendationModal').modal('hide');
    $('.loading').removeClass('d-none');
    $('#editRecomendationModal').modal({backdrop:'static', keyboard: false});
    return new Promise ((resolve, reject) => {
        $.post(`/catalogs/${recomendations.origin}/${recomendations.single}-recomendation`,
            {
                '_token': '{{ csrf_token() }}',
                id : idElement
            },
            (data) => {
                let recomendation = data[0].recomendation;
                recomendations.idRecomendation = data[0].id_recomendation;
                document.querySelector('#editRecomendation').value = recomendation;
                $('.loading').addClass('d-none');
                resolve(true);
            }
        )
        .fail((e)=>{
            reject(false)
            $('.loading').addClass('d-none');
            toastAlert(e.statusText, 'error');
        })
    })
}
/**
 * cancel edit
 */
function closeEdit() {
    $('#editRecomendationModal').modal('hide');
    $('.loading').removeClass('d-none');
    $('#recomendationModal').modal({backdrop:'static', keyboard: false});
    $('.loading').addClass('d-none');
}
/**
 * Sen edit recomendation
 */
$('#btnEditRecomendation').click((e)=>{
    e.preventDefault()
    showLoading('#editRecomendationModal')
    $.post(`/catalogs/${recomendations.origin}/recomendations/update`,
        {
            '_token': '{{ csrf_token() }}',
            id : recomendations.idRecomendation,
            recommendation : document.querySelector('#editRecomendation').value
        }, 
        (data) => {
            toastAlert(data.msg, data.status);
            if(data.status == 'success'){ 
                resetRecomendation()
                getRequirementsRecomendation()
                .then(()=>{
                    setTimeout(() => {
                        showLoading('#editRecomendationModal')
                        closeEdit()
                    }, 1000);
                })  
            }
        }
    )
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
        showLoading('#editRecomendationModal')
    })
});
</script>