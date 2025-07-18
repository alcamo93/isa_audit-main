<script>
/*************** Permissions ***************/
const CREATE = ("{{  Session::get('create') }}" == 1) ? true : false;
const MODIFY = ("{{  Session::get('modify') }}" == 1) ? true : false;
const DELETE = ("{{  Session::get('delete') }}" == 1) ? true : false;
document.querySelector('#buttonAddMatter').style.display = (CREATE) ? 'block' : 'none';
document.querySelector('#buttonAddAspect').style.display = (CREATE) ? 'block' : 'none';
</script>