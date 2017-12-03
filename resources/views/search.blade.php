@extends('master')
<link href="{{ asset('/css/search.css') }}" rel="stylesheet">

@section('content')
	<br /><br />
	<div class="row">
		<h6 class="col-md-10 offset-md-1">Results for "{{ $q }}"</h6>
	</div>
	@include('layouts.search.partials._mixed_search', ['mainTitle' => 'Authors','data' => $rs["resultSet"]["creator"]])
	@include('layouts.search.partials._mixed_search', ['mainTitle' => 'Titles','data' => $rs["resultSet"]["title"]])
	@include('layouts.search.partials._mixed_search', ['mainTitle' => 'Cambridge English','data' => $rs["resultSet"]["website-CambridgeEnglish"]])
	@include('layouts.search.partials._mixed_search', ['mainTitle' => 'Cambridge Core','data' => $rs["resultSet"]["website-CambridgeCore"]])
	@include('layouts.search.partials._mixed_search', ['mainTitle' => 'Academic and Professional','data' => $rs["resultSet"]["website-AcademicProfessional"]])
@endsection