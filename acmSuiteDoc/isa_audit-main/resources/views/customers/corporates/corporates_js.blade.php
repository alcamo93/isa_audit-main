<script>
$(document).ready( () => {
    setFormValidation('#setCorporateForm');
    setFormValidation('#updateCorporateForm');

    setFormValidation('#addressPhysicsForm');
    setFormValidation('#addressFiscalForm');

    setFormValidation('#addressForm');
    setFormValidation('#contactForm');
});
/*************** Active menu ***************/
activeMenu(2, ' Plantas');
titleComeback('Clientes', 'customers');
/**
 * corporates datatables
 */
const corporatesTable = $('#corporatesTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/corporates',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.idCustomer = '{{ $idCustomer }}',
            data.filterTradename = document.querySelector('#filterTradename').value,
            data.filterTrademark = document.querySelector('#filterTrademark').value,
            data.filterIdStatus = document.querySelector('#filterIdStatus').value,
            data.filterIdIndustry = document.querySelector('#filterIdIndustry').value,
            data.filterRFC = document.querySelector('#filterRFC').value
        }
    },
    columns: [
        { data: 'corp_trademark', orderable : true },
        { data: 'corp_tradename', orderable : true },
        { data: 'rfc',  className: 'text-center', orderable : true },
        { data: 'status',  className: 'text-center', orderable : true },
        { data: 'industry',  className: 'text-center', orderable : true },
        { data: 'id_client', className: 'td-actions text-center',  orderable : false, visible: true}
    ],
    columnDefs: [
        {
            render:  ( data, type, row ) => {
                let color = (data == 'Activo') ? 'success' : 'danger';
                return `<span class="badge badge-${color} text-white">${data}</span>`; 
            },
            targets: 3
        },
        {
            render: function ( data, type, row ) {
                let btnContact = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Contacto" 
                                    href="javascript:openContact('${row.corp_trademark}', ${row.id_corporate})">
                                    <i class="fa fa-id-card fa-lg"></i>
                                </a>`;
                let btnAddress = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Direcciones" 
                                    href="javascript:openAddress('${row.corp_trademark}', ${row.id_corporate})">
                                    <i class="fa fa-map-marker fa-lg"></i>
                                </a>`;
                let btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    href="javascript:openEditCorporate('${row.corp_trademark}', ${row.id_corporate})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>` : '';
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    href="javascript:deleteCorporate('${row.corp_trademark}', ${row.id_corporate})">
                                    <i class="fa fa-times fa-lg"></i>
                                </a>` : '';
                return btnContact+btnAddress+btnEdit+btnDelete;
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
 * Reload Corporates Datatables
 */
const reloadCorporates = () => { corporatesTable.ajax.reload() }
const reloadCorporatesKeepPage = () => { corporatesTable.ajax.reload(null, false) }
/**
 * Open modal add form
 */
function openAddCorporate(){
    document.querySelector('#setCorporateForm').reset();
    $('#setCorporateForm').validate().resetForm();
    $('#setCorporateForm').find(".error").removeClass("error");
    $('.newIndustry').addClass('d-none');
    $('#n-ind').attr('data-rule-required', false);
    $('#addModalCorporate').modal({backdrop:'static', keyboard: false});
}
/**
 *  Back to customers
 */
function closeCorporates ()
{
    $('.loading').removeClass('d-none')
    window.location.href = `{{asset('/customers')}}`;
}

/**
 *  Add new Industrie input
 */
function showNewInd(selection){
    if(selection.value == 0){ 
        $('.newIndustry').removeClass('d-none');
        $('#n-ind').attr('data-rule-required', true);
    }
    else {
        $('.newIndustry').addClass('d-none');
        $('#n-ind').attr('data-rule-required', false);
    }
}
/**
 * Handler to submit set corporate form 
 */
$('#setCorporateForm').submit( (event) => {
    event.preventDefault();
    if($('#setCorporateForm').valid()) {
        showLoading('#addModalCorporate');
        //handler notificaction
        $.post('/corporates/set', {
            '_token': '{{ csrf_token() }}',
            idCustHidden: document.querySelector('#idCustHidden-s').value,
            sTradename: document.querySelector('#s-tradename').value,
            sTrademark: document.querySelector('#s-trademark').value,
            sRfc: document.querySelector('#s-rfc').value,
            sIdStatus: document.querySelector('#s-idStatus').value,
            sIdIndustry: document.querySelector('#s-idIndustry').value,
            sNewIndustry: document.querySelector('#n-ind').value,
            sType: document.querySelector('#s-type').value
        },
        (data) => {
            showLoading('#addModalCorporate');
            toastAlert(data.msg, data.status);
            if(data.status == 'success'){
                if( document.querySelector('#s-idIndustry').value == 0 ){ 
                    $('.newIndustry').addClass('d-none')
                    $('#n-ind').attr('data-rule-required', false);
                    $('#s-idIndustry').append('<option value="'+data.newIndustry+'">'+document.querySelector('#n-ind').value+'</option>')
                }
                reloadCorporates();
                $('#addModalCorporate').modal('hide');
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#addModalCorporate');
        });
    }
});
/**
 * Open modal edit form
 */
function openEditCorporate(corporate, idCorporate){
    $('.loading').removeClass('d-none')
    document.querySelector('#updateCorporateForm').reset();
    $('#updateCorporateForm').validate().resetForm();
    $('#updateCorporateForm').find(".error").removeClass("error");
    document.querySelector('#titleEditCorporate').innerHTML = `Detalles de: "${corporate}"`;
    // get corporate
    $.get(`/corporates/${idCorporate}`,
    {
        '_token':'{{ csrf_token() }}'
    },
    (data) => {
        if (data.length > 0) {
            let corporate = data[0];
            document.querySelector('#idCustHidden-u').value = corporate.id_customer;
            document.querySelector('#idCorpHidden').value = corporate.id_corporate;
            document.querySelector('#u-tradename').value = corporate.corp_tradename;
            document.querySelector('#u-trademark').value = corporate.corp_trademark;
            document.querySelector('#u-rfc').value = corporate.rfc;
            document.querySelector('#u-idStatus').value = corporate.id_status;
            document.querySelector('#u-idIndustry').value = corporate.id_industry;
            document.querySelector('#u-type').value = corporate.type;
            $('.loading').addClass('d-none')
            $('#editModalCorporate').modal({backdrop:'static', keyboard: false});
        }else{
            reloadCorporatesKeepPage();
        }
    })
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
        $('.loading').addClass('d-none')
    });
}
/**
 * Handler to submit update corporate form 
 */
$('#updateCorporateForm').submit( (event) => {
    event.preventDefault();
    if($('#updateCorporateForm').valid()) {
        showLoading('#editModalCorporate');
        //handler notificaction
        $.post('/corporates/update', {
            '_token': '{{ csrf_token() }}', 
            idCustHidden: document.querySelector('#idCustHidden-u').value,
            idCorpHidden: document.querySelector('#idCorpHidden').value,
            uTradename: document.querySelector('#u-tradename').value,
            uTrademark: document.querySelector('#u-trademark').value,
            uRfc: document.querySelector('#u-rfc').value,
            uIdStatus: document.querySelector('#u-idStatus').value,
            uIdIndustry: document.querySelector('#u-idIndustry').value,
            uType: document.querySelector('#u-type').value
        },
        (data) => {
            showLoading('#editModalCorporate');
            toastAlert(data.msg, data.status);
            if(data.status == 'success'){
                reloadCorporatesKeepPage();
                $('#editModalCorporate').modal('hide');
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#editModalCorporate');
        });
    }
});
/**
 * Delete corporate
 */
function deleteCorporate(corporateName, idElement) {
    Swal.fire({
        title: `¿Estas seguro de eliminar "${corporateName}"?`,
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
            $.post('/corporates/delete',
            {
                '_token':'{{ csrf_token() }}',
                idCorporate: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                if(data.status == 'success'){
                    reloadCorporates();
                }
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
            });
        }
    });
}

/******************* Address ******************/

/**
 * Open address Modal
 */
function openAddress(corporate, idCorporate){
    // reset forms
    $('.loading').removeClass('d-none')
    $('#addressForm').scrollintoview({ duration: 1, dirección : "vertical", viewPadding: { y: 100 } });
    document.querySelector('#addressForm').reset();
    $('#addressForm').validate().resetForm();
    $('#addressForm').find(".error").removeClass("error");

    //document.querySelector('#addressFiscalForm').reset();
    //$('#addressFiscalForm').validate().resetForm();
    //$('#addressFiscalForm').find(".error").removeClass("error");
    document.querySelector('#btnDeleteAddressFi').style.display = 'none';
    document.querySelector('#btnDeleteAddressPh').style.display = 'none';
    // reset values 
    document.querySelector('#idCorpAddressPh').value = idCorporate;
    document.querySelector('#idCorpAddressFi').value = idCorporate;
    document.querySelector('#idAddressPh').value = 0;
    document.querySelector('#idAddressFi').value = 0;
    document.querySelector('#addressTitle').innerHTML = `Direcciones de: "${corporate}"`;
    $.get(`/corporates/address/${idCorporate}`,
    {
        '_token':'{{ csrf_token() }}'
    },
    (data) => {
        if (data.length > 0) {
            for (let index = 0; index < data.length; index++) {
                if (data[index].type == 1) { // physical
                    document.querySelector('#btnDeleteAddressPh').style.display = 'block';
                    document.querySelector('#idCorpAddressPh').value = data[index].id_corporate;
                    document.querySelector('#idAddressPh').value = data[index].id_address;
                    document.querySelector('#streetPh').value = data[index].street;
                    document.querySelector('#suburbPh').value = data[index].suburb;
                    document.querySelector('#numExtPh').value = data[index].ext_num;
                    document.querySelector('#numIntPh').value = data[index].int_num;
                    document.querySelector('#zipPh').value = data[index].zip;
                    document.querySelector('#idCountryPh').value = data[index].id_country;
                    setStates(data[index].id_country, '#idStatePh', '#idCityPh');
                    setTimeout( () => {
                        document.querySelector('#idStatePh').value = data[index].id_state;
                        setCities(data[index].id_state, '#idCityPh');
                        setTimeout( () => {
                            document.querySelector('#idCityPh').value = data[index].id_city;
                            $('.loading').addClass('d-none')
                        }, 1000);
                    }, 1000);
                }else if (data[index].type == 0) { // fiscal
                    document.querySelector('#btnDeleteAddressFi').style.display = 'block';
                    document.querySelector('#idCorpAddressFi').value = data[index].id_corporate;
                    document.querySelector('#idAddressFi').value = data[index].id_address;
                    document.querySelector('#streetFi').value = data[index].street;
                    document.querySelector('#suburbFi').value = data[index].suburb;
                    document.querySelector('#numExtFi').value = data[index].ext_num;
                    document.querySelector('#numIntFi').value = data[index].int_num;
                    document.querySelector('#zipFi').value = data[index].zip;
                    document.querySelector('#idCountryFi').value = data[index].id_country;
                    setStates(data[index].id_country, '#idStateFi', '#idCityFi');
                    setTimeout( () => {
                        document.querySelector('#idStateFi').value = data[index].id_state;
                        setCities(data[index].id_state, '#idCityFi');
                        setTimeout( () => {
                            document.querySelector('#idCityFi').value = data[index].id_city;
                            $('.loading').addClass('d-none')
                        }, 1000);
                    }, 1000);
                }
            }
        }
        else  $('.loading').addClass('d-none')
        $('#addressModal').modal({backdrop:'static', keyboard: false});
    })
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
        $('.loading').addClass('d-none')
    });
}

/**
 *  Copy physical address
 */
document.querySelector('#btnCopyAddressPh').onclick = (event) => {
    event.preventDefault();
    $('.loading').removeClass('d-none');
    let street = document.querySelector('#streetPh').value;
    document.querySelector('#streetFi').value = street;
    let suburb = document.querySelector('#suburbPh').value;
    document.querySelector('#suburbFi').value = suburb;
    let numExt = document.querySelector('#numExtPh').value;
    document.querySelector('#numExtFi').value = numExt;
    let numInt = document.querySelector('#numIntPh').value;
    document.querySelector('#numIntFi').value = numInt;
    let zip = document.querySelector('#zipPh').value;
    document.querySelector('#zipFi').value = zip;
    let idCountry = document.querySelector('#idCountryPh').value;
    document.querySelector('#idCountryFi').value = idCountry;
    let idState = document.querySelector('#idStatePh').value;
    let idCity = document.querySelector('#idCityPh').value;

    setStates(idCountry, '#idStateFi', '#idCityFi');
    setTimeout( () => {
        document.querySelector('#idStateFi').value = idState;
        setCities(idState, '#idCityFi');
        setTimeout( () => {
            document.querySelector('#idCityFi').value = idCity;
            $('.loading').addClass('d-none')
        }, 1000);
    }, 1000);

    $('#addressForm').validate().resetForm();
    $('#addressForm').find(".error").removeClass("error");
}

/**
 * Handler to submit address form physical and fiscal
 */
$('#addressForm').submit( (event) => {
    event.preventDefault();
    if($('#addressForm').valid()) {
        showLoading('#addressModal');
        //handler notificaction
        $.post('/corporates/address/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            addresses: {
                physical : {
                    idCorporate: document.querySelector('#idCorpAddressPh').value,
                    idAddress: document.querySelector('#idAddressPh').value,
                    street: document.querySelector('#streetPh').value,
                    suburb: document.querySelector('#suburbPh').value,
                    numExt: document.querySelector('#numExtPh').value,
                    numInt: document.querySelector('#numIntPh').value,
                    zip: document.querySelector('#zipPh').value,
                    idCountry: document.querySelector('#idCountryPh').value,
                    idState: document.querySelector('#idStatePh').value,
                    idCity: document.querySelector('#idCityPh').value,
                    type: document.querySelector('#typeAddressPh').value
                },
                fiscal: {
                    idCorporate: document.querySelector('#idCorpAddressFi').value,
                    idAddress: document.querySelector('#idAddressFi').value,
                    street: document.querySelector('#streetFi').value,
                    suburb: document.querySelector('#suburbFi').value,
                    numExt: document.querySelector('#numExtFi').value,
                    numInt: document.querySelector('#numIntFi').value,
                    zip: document.querySelector('#zipFi').value,
                    idCountry: document.querySelector('#idCountryFi').value,
                    idState: document.querySelector('#idStateFi').value,
                    idCity: document.querySelector('#idCityFi').value,
                    type: document.querySelector('#typeAddressFi').value
                }
            }
        },
        (data) => {
            showLoading('#addressModal');
            toastAlert(data.msg, data.status);
            if (data.status = 'success') {
                document.querySelector('#idAddressPh').value = data.addressesResponse.physical.idAddress;
                document.querySelector('#idCorpAddressPh').value = data.addressesResponse.physical.idCorporate;
                document.querySelector('#btnDeleteAddressPh').style.display = 'block';

                document.querySelector('#idAddressFi').value = data.addressesResponse.fiscal.idAddress;
                document.querySelector('#idCorpAddressFi').value = data.addressesResponse.fiscal.idCorporate;
                document.querySelector('#btnDeleteAddressFi').style.display = 'block';
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#addressModal');
        });
    }
});

/**
 * Handler delete address fiscal
 */
document.querySelector('#btnDeleteAddressFi').onclick = () => {
    let addressType = document.querySelector('#typeAddressFi').value;
    let idAddress = document.querySelector('#idAddressFi').value;
    let idCorporate = document.querySelector('#idCorpAddressFi').value;
    if (idAddress > 0) {
        deleteAddress(addressType, idAddress, idCorporate);
        setStates(0, '#idStateFi', '#idCityFi');
    }
}

/**
 * Handler delete address physcal
 */
document.querySelector('#btnDeleteAddressPh').onclick = () => {
    let addressType = document.querySelector('#typeAddressPh').value;
    let idAddress = document.querySelector('#idAddressPh').value;
    let idCorporate = document.querySelector('#idCorpAddressPh').value;
    if (idAddress > 0) {
        deleteAddress(addressType, idAddress, idCorporate);
        setStates(0, '#idStatePh', '#idCityPh');
    }
}

/**
 * Delete address
 */
function deleteAddress(addressType, idElement, idCorporate) {
    let type = (addressType == 1) ? 'Física' : 'Fiscal';
    Swal.fire({
        title: `¿Estas seguro de eliminar la dirección "${type}"?`,
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
            $.post('/corporates/address/delete',
            {
                '_token':'{{ csrf_token() }}',
                idAddress: idElement
            },
            (data) => {
                if (data.status == 'success') {
                    if (addressType == 1){
                        document.querySelector('#idCorpAddressPh').value = idCorporate;
                        document.querySelector('#idAddressPh').value = 0;
                        $('#addressForm').validate().resetForm();
                        $('#addressForm').find(".error").removeClass("error");
                        document.querySelector('#btnDeleteAddressPh').style.display = 'none';

                        document.querySelector('#streetPh').value = "";
                        document.querySelector('#suburbPh').value = "";
                        document.querySelector('#numExtPh').value = "";
                        document.querySelector('#numIntPh').value = "";
                        document.querySelector('#zipPh').value = "";
                        document.querySelector('#idCountryPh').value = null;
                        document.querySelector('#idStatePh').value = null;
                        document.querySelector('#idCityPh').value = null;
                    }else if (addressType == 0){
                        document.querySelector('#idCorpAddressFi').value = idCorporate;
                        document.querySelector('#idAddressFi').value = 0;
                        $('#addressForm').validate().resetForm();
                        $('#addressForm').find(".error").removeClass("error");
                        document.querySelector('#btnDeleteAddressFi').style.display = 'none';

                        document.querySelector('#streetFi').value = "";
                        document.querySelector('#suburbFi').value = "";
                        document.querySelector('#numExtFi').value = "";
                        document.querySelector('#numIntFi').value = "";
                        document.querySelector('#zipFi').value = "";
                        document.querySelector('#idCountryFi').value = null;
                        document.querySelector('#idStateFi').value = null;
                        document.querySelector('#idCityFi').value = null;
                    }
                }
                toastAlert(data.msg, data.status);
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
            });
        }
    });
}

/******************* Contacts ******************/

/**
 * Open contact for corporates
 */
function openContact(corporate, idCorporate){
    // Reset form
    document.querySelector('#contactForm').reset();
    $('#contactForm').validate().resetForm();
    $('#contactForm').find(".error").removeClass("error");
    // set value 0
    document.querySelector('#idCorpCt').value = idCorporate;
    document.querySelector('#idContact').value = 0;
    // Get contact for corporate
    document.querySelector('#contactTitle').innerHTML = `Contacto de: "${corporate}"`;
    $.get(`/corporates/contact/${idCorporate}`,
    {
        '_token':'{{ csrf_token() }}'
    },
    (data) => {
        if (data.length > 0) {
            document.querySelector('#idCorpCt').value = data[0].id_corporate;
            document.querySelector('#idContact').value = data[0].id_contact;
            document.querySelector('#ctEmail').value = data[0].ct_email;
            document.querySelector('#ctFirstName').value = data[0].ct_first_name;
            document.querySelector('#ctSecondName').value = data[0].ct_second_name;
            document.querySelector('#ctLastName').value = data[0].ct_last_name;
            document.querySelector('#ctDegree').value = data[0].degree;
            document.querySelector('#ctCell').value = data[0].ct_cell;
            document.querySelector('#ctPhOffice').value = data[0].ct_phone_office;
            document.querySelector('#ctPhExtOffice').value = data[0].ct_ext;
            document.querySelector('#btnDeleteContact').style.display = 'block'; 
        }
        $('#contactModal').modal({backdrop:'static', keyboard: false});
    })
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
    });
}
/**
 * Handler to submit contact form 
 */
$('#contactForm').submit( (event) => {
    event.preventDefault();
    if($('#contactForm').valid()) {
        showLoading('#contactModal');
        //handler notificaction
        $.post('/corporates/contact/set', {
            '_token': '{{ csrf_token() }}', 
            idCorporate: document.querySelector('#idCorpCt').value,
            idContact: document.querySelector('#idContact').value,
            email: document.querySelector('#ctEmail').value,
            firstName: document.querySelector('#ctFirstName').value,
            secondName: document.querySelector('#ctSecondName').value,
            lastName: document.querySelector('#ctLastName').value,
            degree: document.querySelector('#ctDegree').value,
            cell: document.querySelector('#ctCell').value,
            phOffice: document.querySelector('#ctPhOffice').value,
            phExtOffice: document.querySelector('#ctPhExtOffice').value
        },
        (data) => {
            showLoading('#contactModal');
            toastAlert(data.msg, data.status);
            if (data.status = 'success') {
                document.querySelector('#btnDeleteContact').style.display = 'block';
                document.querySelector('#idContact').value = data.idContact;
                document.querySelector('#idCorpCt').value = data.idCorporate;
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#contactModal');
        });
    }
});
/**
 * Delete address
 */
function deleteContact(idElement, idCorporate) {
    Swal.fire({
        title: `¿Estas seguro de eliminar la información de contacto?`,
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
            $.post('/corporates/contact/delete',
            {
                '_token':'{{ csrf_token() }}',
                idContact: idElement
            },
            (data) => {
                if (data.status == 'success') {
                    // Reset form
                    document.querySelector('#idCorpCt').value = idCorporate;
                    document.querySelector('#idContact').value = 0;
                    document.querySelector('#contactForm').reset();
                    $('#contactForm').validate().resetForm();
                    $('#contactForm').find(".error").removeClass("error");
                    document.querySelector('#btnDeleteContact').style.display = 'none';
                }
                toastAlert(data.msg, data.status);
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
            });
        }
    });
}
/**
 * Handler delete address physcal
 */
document.querySelector('#btnDeleteContact').onclick = () => {
    let idContact = document.querySelector('#idContact').value;
    let idCorporate = document.querySelector('#idCorpCt').value;
    if (idContact > 0) {
        deleteContact(idContact, idCorporate);
    }
}
</script>
