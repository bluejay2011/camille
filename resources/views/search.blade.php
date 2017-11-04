@extends('master')

@section('content')
<style>
	.gold {
		color: #A58500;
	}
</style>
<div class="row">
	<h6 class="col-md-10 offset-md-1">Results for "test"</h6>
</div>
@include('layouts.search.partials._authors')
@include('layouts.search.partials._titles')
@endsection