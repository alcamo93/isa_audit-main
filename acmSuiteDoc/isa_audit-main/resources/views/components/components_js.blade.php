<script>
    /*************** Active Global Tooltip ***************/
    $( document ).ajaxComplete(function() {
        // Required for Bootstrap tooltips in DataTables
        $('[data-toggle="tooltip"]').tooltip({
            "html": true,
            "delay": {"show": 1000, "hide": 0},
        });
    });
    /*************** Active Menu in sidebar ***************/
    function activeMenu(moduleName, name) {
        // const moduleSelect = document.querySelector('#module-'+moduleName);
        // moduleSelect.classList.add('active');
        $('#module-'+moduleName).scrollintoview({ duration: 1, direction : "vertical", viewPadding: { y: 100 } });
        document.querySelector('#titlePage').innerHTML = name;
        document.querySelector('#titleComeback').innerHTML = '';
        document.querySelector('#linkPage').href = '#';
    }
    
    function titleComeback(comeback, ruta){
        document.querySelector('#titleComeback').innerHTML = `${comeback} / `;
        document.querySelector('#linkPage').href = `{{ asset('/${ruta}')}}`;
    }
    /*******************Clean Form  ************* */
    /**
     * Clean data form
     */
    function cleanForm(form){
        document.querySelector(form).reset();
        $(form).validate().resetForm();
        $(form).find(".error").removeClass("error");
    }
    /*************** Notifications ***************/
    /**
     * Show Alert like process notification
     */
    function toastAlert(title, status, time = 3000) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top',
            showConfirmButton: false,
            timer: time,
            timerProgressBar: true,
            onOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        Toast.fire({
            icon: status,
            title: title
        });
    }
    /**
     * Show Alert with confirm button
     */
    function okAlert(title, msg, status) {
        Swal.fire({
            allowOutsideClick: false,
            title: title,
            html: msg,
            icon: status
        });
    }
    /**
     * Handle loading in actions
     */
    function showLoading(idElement){
        const element = document.querySelector(idElement);
        if(element.classList.contains("show")) {
            $(element).removeClass('show')
            $('.loading').removeClass('d-none')
        }
        else {
            $(element).addClass('show')
            $('.loading').addClass('d-none')
        }
    }
    /**
     * Detect session expired in request
     */
    function session(request){
        if (request.message == 'Unauthenticated.' && request.status == 401) {
            toastAlert('Tiempo de sesión expirado por inactividad', 'error');
            setTimeout( () => {
                window.location.href = '{{ asset('/') }}';
            }, 1000);
        }
    }
    /**
     * show filter customer only for special requirements
     */
    function filterCustomer(idRequirementType, area, selectorCustomer, selectorCorporate) {
        // show or remove filters in view
        if (idRequirementType == 11) {
            $(area).removeClass('d-none');
        }
        else {
            // reset filter 
            document.querySelector(selectorCustomer).value = 0;
            document.querySelector(selectorCorporate).value = 0;
            $(area).addClass('d-none');
        }
    }
    /**
     * Init tooltip
     */
    function initTooltip(){
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
    }

    const typewatch = function(){
        let timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
    }();
    /**
     * delay in miliseconds with callback function
     */
    function delay(callback, ms) {
        let timer = 0;
        return function() {
            let context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    }
    /**
     * Set format am pm in date
     */
    function formatDate(dateParam, type, sumDays = 0) {
        let date = dateParam;
        if (typeof dateParam != 'object') {
            date = new Date(dateParam.replace(/\s/, 'T'));
        }
        if (type == 'datetime') {
            const strDate = `${addZero(date.getDate()+sumDays)}/${addZero(date.getMonth()+1)}/${addZero(date.getFullYear())}`;
            let hours = date.getHours();
            let minutes = date.getMinutes();
            let letters = hours >= 12 ? 'p.m.' : 'a.m.';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            const strTime = `${addZero(hours)}:${addZero(minutes)} ${letters}`;
            return `${strDate} ${strTime}`;
        }
        if (type == 'date') {
            const strDate = `${addZero(date.getDate()+sumDays)}/${addZero(date.getMonth()+1)}/${addZero(date.getFullYear())}`;
            return strDate;
        }
        if (type == 'backend') {
            const strDate = `${addZero(date.getFullYear())}-${addZero(date.getMonth()+1)}-${addZero(date.getDate()+sumDays)}`;
            return strDate;
        }
    }
    // Add zero in digits
    function addZero(i, type) {
        if (i < 10) {
            i = "0" + i;
        }
        return i;
    }
</script>
