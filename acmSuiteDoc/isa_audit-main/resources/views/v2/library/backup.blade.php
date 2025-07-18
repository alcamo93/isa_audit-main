@extends('theme.master')
@section('view')
  <div id="view">
    <backup-download :id-backup="{{ intval($idBackup) }}"/>
  </div>
@endsection
