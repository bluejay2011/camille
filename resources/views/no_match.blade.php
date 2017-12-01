@extends('master')
<link href="{{ asset('/css/search.css') }}" rel="stylesheet">

@section('content')
    <br /><br />
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <p>Your search - {{$q}} - did not match any documents.</p>
            <br />
            Suggestions:<br /><br />
            <ul>
                <li>Make sure that all words are spelled correctly.</li>
                <li>Try different keywords.</li>
                <li>Try more general keywords.</li>
            </ul>
        </div>
    </div>
@endsection