<script>
/*************** Permissions ***************/
const moduleActive = '{{ $moduleActive }}'
const CREATE = true;//("{{  Session::get('create') }}" == 1) ? true : false;
const MODIFY = true;//("{{  Session::get('modify') }}" == 1) ? true : false;
const DELETE = true;//("{{  Session::get('delete') }}" == 1) ? true : false;
document.querySelector('#buttonAddRequirement').style.display = (CREATE) ? 'block' : 'none';
if (moduleActive == 8) {
    document.querySelector('#buttonAddSubrequirement').style.display = (CREATE) ? 'block' : 'none';   
}
</script>