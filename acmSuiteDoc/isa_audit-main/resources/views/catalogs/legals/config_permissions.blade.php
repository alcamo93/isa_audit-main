<script>
/*************** Permissions ***************/
const CREATE = ("{{  Session::get('create') }}" == 1) ? true : false;
const MODIFY = ("{{  Session::get('modify') }}" == 1) ? true : false;
const DELETE = ("{{  Session::get('delete') }}" == 1) ? true : false;
document.querySelector('#buttonAddGuidelines').style.display = (CREATE) ? 'block' : 'none';
document.querySelector('#buttonAddBasis').style.display = (CREATE) ? 'block' : 'none';
/*************** State Current ***************/
let state = {
    guideline: {
        name: '',
        initials: '',
        date: '',
    }
}
</script>


