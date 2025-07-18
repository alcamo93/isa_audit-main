<script>
/**
 * Inintialize the crop for image selecction
 */
function cropImage(obj, url, ratio, user){
    let uploadFile = obj.files[0];
    let thisInput = $('#'+obj.id);
    let sizeMaxImg = 10000000;
    if (uploadFile.type != 'image/png') {
        Swal.fire('El formato permitido es PNG', '', 'warning');
        thisInput.replaceWith(thisInput.val('').clone(true));
    }
    else if (uploadFile.size > sizeMaxImg) {
        var sizeAlert = sizeMaxImg / 1000;
        Swal.fire(`El peso de la imagen no puede exceder los ${sizeAlert}MB`, '', 'warning');
        thisInput.replaceWith(thisInput.val('').clone(true));
    }
    else { 
        $('.inputlogo').addClass('d-none');
        $('.selection').removeClass('d-none');
        let cropMe = new ICropper(
            'crop'
            ,{
                ratio: ratio
                ,image: URL.createObjectURL(uploadFile)
                ,onChange: function(info){	//onChange must be set when constructing.
                    infoMe.innerHTML = 'Left: ' + info.l + 'px, Top: '+info.t
                        + 'px, Width: ' + info.w + 'px, Height: ' + info.h+'px';
                        
                    infoMe.setAttribute("obj", obj.id);
                    infoMe.setAttribute("idUser", user);
                    infoMe.setAttribute("Left", info.l);
                    infoMe.setAttribute("Top", info.t);
                    infoMe.setAttribute("Width", info.w);
                    infoMe.setAttribute("Height", info.h);
                    infoMe.setAttribute("url", url);
                },
                preview: [
                    'cropPreview'
                ]
            }
        );
        
    }              
}
/**
 * Consumes the service set by the initilization in cropImage, return a promise
 */
function saveCrop(){
    let info = document.getElementById('infoMe');
    let id = info.getAttribute("idUser")
    let url = info.getAttribute("url");
    let obj = document.getElementById(info.getAttribute("obj"));
    let crop = document.getElementById("crop");
    let img = obj.files[0];
    let form = new FormData();
    form.append('logo', img);
    form.append('imageW', crop.offsetHeight);
    form.append('imageH', crop.offsetWidth);
    form.append('Top', info.getAttribute('Top'));
    form.append('Left', info.getAttribute('Left'));
    form.append('Width', info.getAttribute('Width'));
    form.append('Height', info.getAttribute('Height'));
    form.append('_token', '{{ csrf_token() }}');
    form.append('id', id);
    return new Promise((resolve, reject) => {
        jQuery.ajax({
            url: url,
            data: form,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            success:function(data){
                toastAlert(data.msg, data.status);
                closeCrop()
                if (data.status == 'error'){
                    $('.imgCropModal').modal('hide');
                    reject(false)
                }
                else resolve(data)
            }
        });
    });
}
/**
 * Sets the crop status to default 
 */
function closeCrop(){
    document.getElementById('crop').innerHTML = "";
    document.getElementById('cropPreview').innerHTML = "";
    let id = document.getElementById('infoMe').getAttribute('obj');
    if(id)
    {
        document.getElementById(id).value = "";
        infoMe.setAttribute("obj", '');
        infoMe.setAttribute("Left", '');
        infoMe.setAttribute("Top", '');
        infoMe.setAttribute("Width", '');
        infoMe.setAttribute("Height", '');
        infoMe.setAttribute("url", '');
    }
    $('.inputlogo').removeClass('d-none');
    $('.selection').addClass('d-none');
}
</script>