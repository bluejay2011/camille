@extends('master')

@section('content')
<div class="container">
	<br /><br /><br /><br />
	<div class="row justify-content-center">
		<div class="col-4 text-center">
			<h1> Camille </h1>
		</div>
	</div>
	<div class="row justify-content-center">
		<div class="col-4 text-center">

		</div>
	</div>
	<br />
    <div class="row">
    	<div class="col-12">
    		<form id="frm-search" class="form-inline" method="get" action="/search">
    			<div class="row justify-content-center col-12">
				    <input id="inp-search" class="form-control mr-sm-2 col-7" name="q" type="search" placeholder="Find Books, Journals, Exams, Authors and more..." aria-label="Search">
				    <button id="btn-search" class="btn btn-outline-success my-2 my-sm-0" type="submit"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
				</div>
			</form>
		</div>
    </div>
</div>
@endsection