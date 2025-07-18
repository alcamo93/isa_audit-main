<script>
/*************** Permissions ***************/
const CREATE = ("{{  Session::get('create') }}" == 1) ? true : false;
const MODIFY = ("{{  Session::get('modify') }}" == 1) ? true : false;
const DELETE = ("{{  Session::get('delete') }}" == 1) ? true : false;
document.querySelector('#buttonAddUsers').style.display = (CREATE) ? 'block' : 'none';
const ACTIONS = (MODIFY || DELETE) ? true : false;
</script>