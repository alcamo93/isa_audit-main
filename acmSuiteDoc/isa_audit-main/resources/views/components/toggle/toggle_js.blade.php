<script>
    /***
        Add the view in top of the toggle area with its array information
     
        /////// Example /////
        $text = array( 
            'title' => 'Área de filtrado', 
            'tooltip' => ' area de filtrado', 
            'idElement'=> '#filterAreaBasisSelection', // element that will be hide/show
            'idToggle' => 'selection-basis' //id completion for the id toggle (at the end it will have this structure #toggle-selection-basis)
        ); 
            
        //@ include ('components.toggle.toggle', $text)

        if you want to be hidden in the beggining add the clase gone and the style="display: none;" attribute to the element

        Add the js blade to the main view

        //@ include ('components.toggle.toggle_js')

        idToggle -> the id of this toggle button
        idHideElement -> the element that will show/hide
        tooltipText -> The text that will be added to the Esconder/mostrar toolip 

     ***/

    function toggleArea(idToggle, idHideElement, tooltipText)
    {
        if($(idHideElement).hasClass('gone'))
        { 
            $(idToggle).html('<i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>')
            $(idToggle).attr('data-original-title', "Esconder "+tooltipText )
            $(idHideElement).removeClass('gone')
            $(idHideElement).fadeToggle()
        }
        else 
        {
            $(idToggle).html('<i class="fa fa-chevron-down text-white" style="width: 20px;" aria-hidden="true"></i>')
            $(idToggle).attr('data-original-title', "Mostrar "+tooltipText )
            $(idHideElement).addClass('gone')
            $(idHideElement).fadeToggle()
        }
    }
</script>