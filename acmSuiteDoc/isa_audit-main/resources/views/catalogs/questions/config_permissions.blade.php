<script>
/*************** Permissions ***************/
const CREATE = true;//("{{  Session::get('create') }}" == 1) ? true : false;
const MODIFY = true;//("{{  Session::get('modify') }}" == 1) ? true : false;
const DELETE = true;//("{{  Session::get('delete') }}" == 1) ? true : false;
const VISIBLE = (MODIFY && DELETE);
document.querySelector('#buttonAddQuestion').style.display = (CREATE) ? 'block' : 'none';
document.querySelector('#buttonAddAnswer').style.display = (CREATE) ? 'block' : 'none';
/**
 * Questions info
 */
const questions = {
    data : []
}
</script>