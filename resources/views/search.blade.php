@extends('master')
<link href="{{ asset('/css/search.css') }}" rel="stylesheet">

@section('content')
	<br /><br />
	<div class="row">
		<h6 class="col-md-10 offset-md-1">Results for "{{ $q }}"</h6>
	</div>
	@include('layouts.search.partials._authors')
	@include('layouts.search.partials._titles')
@endsection