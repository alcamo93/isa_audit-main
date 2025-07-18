@php
    $create = (Session::pull('create') == 1) ? true : false;
    $modify = (Session::pull('modify') == 1) ? true : false;
@endphp 
<script>
/**
 * Help on main view
 */
function helpMain(main, buttonAdd = null, actions, back = null, word = null, selection = false, selected = false, more = null ) {
    let intro = introJs($('#view')[0]);
    $(main).scrollintoview({ duration: 1, dirección : "vertical", viewPadding: { y: 100 } });

    let options  = {
        steps: [],
        'tooltipPosition': 'bottom',
        'exitOnOverlayClick': true,
        'showStepNumbers': false,
        'showButtons': true,
        'showBullets': false,
    }
    
    options.steps.push({
                element: main,
                intro: `En el área de filtrado encontrarás todas las herramientas necesarias para obtener los registros que buscas, cada campo en este área agrega un nivel de filtro a tu búsqueda `,
                position: 'bottom'
            })        
    @if ($create)
        if(buttonAdd) options.steps.push({
            element: buttonAdd,
            intro: 'Da clic sobre este botón para generar un nuevo registro',
            position: 'left'
        })
    @endif

    if(back) options.steps.push({
        element: back,
        intro: `Da clic en el botón de regresar para volver al listado anterior`,
        position: 'left'
    })

    if(word && selection)
    {
        options.steps.push({
            element: actions,
            intro: `En esta columna se encuentra el estado de asignación de cada uno de los ${word}s`,
            position: 'left'
        })
        options.steps.push({
            element: actions,
            intro: `Da clic en el botón de acción para asignar o quitar un ${word} al registro seleccionado`,
            position: 'left'
        })
    }

    if(word && selected)
    {
        options.steps.push({
                element: actions,
                intro: `En esta columna podrás visualizar a detalle los ${word}s seleccionados`,
                position: 'left'
        })
    }

    if (more) options.steps.push({
        element: more,
        intro: `Da clic en este botón para visualizar sus subregistros `,
        position: 'left'
    })

    if(!word) options.steps.push({
        element: actions,
        intro: `En esta columna se encuentran las diferentes acciones para interactuar con los registros existentes `,
        position: 'left'
    })
        
    options.steps.push({
                element: actions,
                intro: `Haz clic sobre cada una de las acciones, dentro de cada ventana emergente encontrarás Ayuda`,
                position: 'left'
            })
    
    disableScrolling()
    intro.setOptions(options).start();
}
/**
 * Help on add modal
 */
function helpAdd(main){
    var intro = introJs($(main)[0]);
    disableScrolling()
    intro.setOptions({
        steps: [
            {
                intro: `Utiliza las flechas <br/> Izquiera o Derecha <br/> (Retroceder o Avanzar)`,
            },
            {
                intro: 'Los campos marcados con un asterisco son obligatorios'
            },
            {
                intro: 'Los demás campos son opcionales'
            },
            {
                intro: 'Para guardar la información haz clic en el botón Registrar/Actualizar'
            },
        ],
        'tooltipPosition': 'bottom',
        'exitOnOverlayClick': true,
        'showStepNumbers': false,
        'showButtons': true,
        'showBullets': false,
        'scrollToElement':false
    }).start();
}

/**
 * Help add recomendations
 */
function helpRecomendations(main) {
    var intro = introJs($(main)[0]);
    disableScrolling()
    intro.setOptions({
        steps: [
            {
                intro: `Cada requerimiento o subrequerimiento puede tener una o varias recomendaciones, aquí puedes agregar nuevas o eliminarlas`
            }
        ],
        'tooltipPosition': 'bottom',
        'exitOnOverlayClick': true,
        'showStepNumbers': false,
        'showButtons': true,
        'showBullets': false,
    }).start();
}
/**
 * Help user asign in obligations
 */
function helpUserAsing(main, word) {
    var intro = introJs($(main)[0]);

    let options = {
        steps: [],
        'tooltipPosition': 'bottom',
        'exitOnOverlayClick': true,
        'showStepNumbers': false,
        'showButtons': true,
        'showBullets': false,
    }

    options.steps.push(
        {
            intro: `En esta pantalla podrás ver el usuario asignado a esta ${word}`
        }
    )
    @if ($modify)
        options.steps.push({
                intro: `Al dar clic en asignar usuario, podrás cambiar al usuario a quien se asignó la ${word}`
        })
    @endif  


    disableScrolling()
    intro.setOptions(options).start();
}
/**
 * Disable scroll
 */
function disableScrolling(){
    var x=window.scrollX;
    var y=window.scrollY;
    window.onscroll=function(){window.scrollTo(x, y);};
}
</script>