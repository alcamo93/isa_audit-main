<script>
/*************** Permissions ***************/
const CREATE = ("{{  Session::get('create') }}" == 1) ? true : false;
const MODIFY = ("{{  Session::get('modify') }}" == 1) ? true : false;
const DELETE = ("{{  Session::get('delete') }}" == 1) ? true : false;
const VISIBLE = (MODIFY && DELETE);
const SHOW = (CREATE) ? 'block' : 'none';
document.querySelector('#buttonAddCategory').style.display = SHOW;
document.querySelector('#buttonAddHelp').style.display = SHOW;
/**
 * Element Current
 */
let element = {
    currentIdCategory: null,
    idRiskCategory: null,
    idConsequence: null,
    idExhibition: null,
    idProbability: null,
    origin: null
}
let current = {
    idConsequence: null,
    idExhibition: null,
    idProbability: null,
    origin: null,
    idSpecification: null
}
let currentInterpretation = {
    one: null,
    two: null,
    three: null
}
let currentHelp = {
    idRiskHelp: null
}
</script>