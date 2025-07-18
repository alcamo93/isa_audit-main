<script>
/**
 * Set users by corporates
 */
function setUsers(idCorporate, selectorUser, callback){
    if (idCorporate != '' || idCorporate == 0){
        // Get all users
        $.post(`{{asset('/users-list')}}`,
        {
            '_token':'{{ csrf_token() }}',
            idCorporate
        },
        (data) => {
            if (data.length > 0) {
                if ( callback != 'undefined' && typeof callback === 'function' ) {
                    var html = `<option value="0">Todos</option>`;
                }
                else{
                    var html = '';
                }
                for (let i = 0; i < data.length; i++){
                    html += `<option value="${data[i]['id_user']}">${data[i]['complete_name']}</option>`;
                }
                document.querySelector(selectorUser).innerHTML = html;
                
            }
            else{
                if ( callback != 'undefined' && typeof callback === 'function' ) {
                    var html = `<option value="0">Todos</option>`;
                }
                else{
                    var html = `<option value=""></option>`;
                }
                document.querySelector(selectorUser).innerHTML = html;
            }
        });
    }
    else{
        var html = `<option value=""></option>`;
        document.querySelector(selectorUser).innerHTML = html;
    }
    if ( callback != 'undefined' && typeof callback === 'function' ) {
        callback();
    }
}
</script>