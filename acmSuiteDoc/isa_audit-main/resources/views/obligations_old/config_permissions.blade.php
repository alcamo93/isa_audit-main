<script>
/*************** Permissions ***************/
const USER = "{{  $idUser }}";
const ORIGIN = 'comments_obligation';
const CREATE = ("{{  Session::get('create') }}" == 1) ? true : false;
const MODIFY = ("{{  Session::get('modify') }}" == 1) ? true : false;
const DELETE = ("{{  Session::get('delete') }}" == 1) ? true : false;
const TODAY = "{{ $today }}";
const ID_PROFILE_TYPE = "{{ $idProfileType }}";
document.querySelector('#buttonAddObligation').style.display = (CREATE) ? 'block' : 'none';
document.querySelector('#buttonAddComment').style.display = (CREATE) ? 'block' : 'none';
</script>