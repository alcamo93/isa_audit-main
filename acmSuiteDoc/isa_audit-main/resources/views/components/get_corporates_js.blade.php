<script>
/**
 * Set corporates by customer
 */
function setCorporates(idCustomer, selectorCorporate, callback) {
    const selector = document.querySelector(selectorCorporate);
    return new Promise ((resolve, reject) => {
        if (idCustomer != '' || idCustomer == 0){
            // Get all corporates
            $.get(`/corporates/customers/all/${idCustomer}`, {
                _token: document.querySelector('meta[name="csrf-token"]').content
            },
            (data) => {
                let html = '';
                if (data.length > 0) {
                    if ( callback != 'undefined' && typeof callback === 'function' ) {
                        html = `<option value="0">Todos</option>`;
                    }
                    else html = '<option value=""></option>';
                    data.forEach(e => {
                        html += `<option value="${e.id_corporate}">${e.corp_tradename}</option>`;
                    });
                }
                else {
                    if ( callback != 'undefined' && typeof callback === 'function' ) {
                        html = `<option value="0">Todos</option>`;
                    }
                    else html = `<option value=""></option>`;
                }
                selector.innerHTML = html;
                resolve(true)
            })
            .fail(e => {
                console.error(e)
                reject(false)
            })
        }
        else selector.innerHTML = `<option value=""></option>`;
        if ( callback != 'undefined' && typeof callback === 'function' ) callback();
    });
}
/**
 * Set corporates by customer
 */
function setCorporatesActive(idCustomer, selectorCorporate, callback){
    const selector = document.querySelector(selectorCorporate);
    return new Promise ((resolve, reject) => {
        if (idCustomer != '' || idCustomer == 0){
            // Get all corporates
            $.get(`/corporates/customers/active/${idCustomer}`, {
                _token: document.querySelector('meta[name="csrf-token"]').content
            },
            (data) => {
                let html = '';
                if (data.length > 0) {
                    if ( callback != 'undefined' && typeof callback === 'function' ) {
                        html = `<option value="0">Todos</option>`;
                    }
                    else html = '<option value=""></option>';
                    data.forEach(e => {
                        html += `<option value="${e.id_corporate}">${e.corp_tradename}</option>`;
                    });
                }
                else {
                    if ( callback != 'undefined' && typeof callback === 'function' ) {
                        html = `<option value="0">Todos</option>`;
                    }
                    else html = `<option value=""></option>`;
                }
                selector.innerHTML = html;
                resolve(true)
            })
            .fail(e => {
                console.error(e)
                reject(false)
            })
        }
        else selector.innerHTML = `<option value=""></option>`;
        if ( callback != 'undefined' && typeof callback === 'function' ) callback();
    });
}
/**
 * Get audit process by corporate
 */
function setAuditProcess(idCorporate, selectorAudit, callback){
    const selector = document.querySelector(selectorAudit);
    return new Promise ((resolve, reject) => {
        if (idCorporate != '' || idCorporate == 0){
            // Get all corporates
            $.get(`/processes/corporates/${idCorporate}`, {
                _token: document.querySelector('meta[name="csrf-token"]').content
            },
            (data) => {
                let html = '';
                if (data.length > 0) {
                    if ( callback != 'undefined' && typeof callback === 'function' ) {
                        html = `<option value="0">Todos</option>`;
                    }
                    else html = '<option value=""></option>';
                    data.forEach(e => {
                        html += `<option value="${e.id_audit_processes}">${e.audit_processes}</option>`;
                    });
                }
                else {
                    if ( callback != 'undefined' && typeof callback === 'function' ) {
                        html = `<option value="0">Todos</option>`;
                    }
                    else html = `<option value=""></option>`;
                }
                selector.innerHTML = html;
                resolve(true)
            })
            .fail(e => {
                console.error(e)
                reject(false)
            })
        }
        else selector.innerHTML = `<option value=""></option>`;
        if ( callback != 'undefined' && typeof callback === 'function' ) callback();
    });
}
</script>