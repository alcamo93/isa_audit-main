<script>
/**
 * Init picker filter dates
 */
function initPickerFilter() {
    flatpickr.localize(flatpickr.l10ns.es);
    $('#filterDates').flatpickr().clear();
    flatpickr('#filterDates', {
        allowInput: false,
        clickOpens: true,
        inline: false,
        position: 'below',
        monthSelectorType: 'dropdown',
        mode: 'range',
        dateFormat: 'd/m/Y',
        onChange: (selectedDates, dateStr, instance) => {
            if (selectedDates.length == 2) {
                filtersDates.dateMin = formatDate(selectedDates[0], 'date')
                filtersDates.dateMax = formatDate(selectedDates[1], 'date')
                reloadTables();   
            }
        },
        locale: {
            firstDayOfWeek: 1 // start week on Monday
        }
    });
}
/**
 * Reset filter dates
 */
$("#btnRefreshDates").click(function() {
    flatpickr('#filterDates', {
        allowInput: false,
        clickOpens: true,
        inline: false,
        position: 'below',
        monthSelectorType: 'dropdown',
        mode: 'range',
        dateFormat: 'd/m/Y',
        onChange: (selectedDates, dateStr, instance) => {
            if (selectedDates.length == 2) {
                filtersDates.dateMin = formatDate(selectedDates[0], 'date')
                filtersDates.dateMax = formatDate(selectedDates[1], 'date')
                reloadTables();
            }
        },
        locale: {
            firstDayOfWeek: 1 // start week on Monday
        }
    });
    filtersDates.dateMin = null;
    filtersDates.dateMax = null;
    reloadTables();
})
/**
 * Set status filter
 */
function setStatus(idStatus) {
    document.querySelector('#filterIdStatus').value = idStatus;
    reloadTables();
}
</script>