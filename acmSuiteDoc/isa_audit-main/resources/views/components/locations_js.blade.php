<script>
/**
 * Get states for country
 */
function setStates(idCountry, selectorStates, selectorCities, callback){
    return new Promise((resolve, reject) => {
        if (idCountry > 0) {
            $.get(`/locations/states/${idCountry}`,{
                _token: document.querySelector('meta[name="csrf-token"]').content
            },
            (data) => {
                if ( callback != 'undefined' && typeof callback === 'function' ) {
                    let html = `<option value="0">Todos</option>`;
                }
                else {
                    let html = `<option value=""></option>`;
                }
                data.forEach(e => {
                    html = html+`<option value="${e['id_state']}">${e['state']}</option>`;
                });
                document.querySelector(selectorStates).innerHTML = html;
                if(data.length > 0){
                    let cityCurrent = document.querySelector(selectorStates).value;
                    if (selectorCities) setCities(cityCurrent, selectorCities)
                }
                resolve(true)
            })
            .fail(e => {
                reject(false)
                toastAlert(e, 'error');
            });   
        }else{
            var html = `<option value=""></option>`;
            document.querySelector(selectorStates).innerHTML = html;
            let stateCurrent = document.querySelector(selectorStates).value;
            if (stateCurrent == '') {
                if (selectorCities) setCities(0, selectorCities);
            }
            resolve(true)
        }
        if ( callback != 'undefined' && typeof callback === 'function' ) {
            callback();
            resolve(true)
        }
    })
}
/**
 * Get cities for state
 */
function setCities(idState, selectorCities, callback){
    return new Promise((resolve, reject) => {
        if (idState > 0) {
            $.get(`/locations/cities/${idState}`, {
                _token: document.querySelector('meta[name="csrf-token"]').content
            },
            (data) => {
                let html = '';
                if ( callback != 'undefined' && typeof callback === 'function' ) {
                    html += `<option value="0">Todos</option>`;
                }
                else {
                    html += `<option value=""></option>`;
                }
                data.forEach(e => {
                    html = html+`<option value="${e['id_city']}">${e['city']}</option>`;
                });
                document.querySelector(selectorCities).innerHTML = html;
                resolve(true)
            })
            .fail(e => {
                reject(false)
                toastAlert(e, 'error');
            }); 
        }else{
            var html = `<option value=""></option>`;
            document.querySelector(selectorCities).innerHTML = html;
            resolve(true)
        }
        if ( callback != 'undefined' && typeof callback === 'function' ) {
            resolve(true)
            callback();
        }
    })
}
</script>