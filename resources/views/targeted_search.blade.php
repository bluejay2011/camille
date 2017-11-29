@extends('master')
<link href="{{ asset('/css/search.css') }}" rel="stylesheet">

@section('content')
    <br /><br />
    <div class="row col-md-12">
        {{--<h6 class="col-md-10 offset-md-1">Results for "{{ $q }}"</h6>--}}
        <p>Page {{$pageNumber}} of about {{$totalCount}} results</p>
    </div>

    @if (isset($items->get('hits')->hit))
        @foreach ($items->get('hits')->hit as $item)
            <h6> {{ isset($item->fields->title)? $item->fields->title[0] : "" }}</h6>
            <p> {{ isset($item->fields->description)? $item->fields->description[0] : "" }}</p>
            <br />
        @endforeach;
        {{ $pagination }}
    @endif
@endsection