@extends('master')
<link href="{{ asset('/css/search.css') }}" rel="stylesheet">

@section('content')
    @if (isset($rs['resultSet']))
        <br /><br />
        <div class="row">
            <h6 class="col-md-10 offset-md-1">Results for "{{ $q }}"</h6>
        </div>
        @foreach($rs['resultSet'] as $key => $data)
            @include('layouts.search.partials._mixed_search', ['mainTitle' => $titleMap[$key]['title'], 'link' => $titleMap[$key]['link'], 'data' => $data, 'more_link' => $titleMap[$key]['more_link']])
        @endforeach
    @endif
@endsection