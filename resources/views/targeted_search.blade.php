@extends('master')
<link href="{{ asset('/css/search.css') }}" rel="stylesheet">

@section('content')
    <br /><br />
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <p>Page {{$pageNumber}} of about {{$totalCount}} results</p>
        </div>
    </div>

    <div class="row">
    @if (isset($items->get('hits')->hit))
        @foreach ($items->get('hits')->hit as $item)
        <div class="col-md-6 offset-md-1">
            <?php
                $title = isset($item->fields->title)? $item->fields->title[0] : "";
                if ($title) {
                    $url = isset($item->fields->url)? $item->fields->url[0] : "";
                }
            ?>
            <h6><a href="{{$url}}">{{$title}}</a></h6>
            <p class="desc"> {{ isset($item->fields->description)? $item->fields->description[0] : "" }}</p>
            <br />
        </div>
        @endforeach

        <div class="col-md-6 offset-md-2">
        {{ $pagination }}
        </div>
    @endif
    </div>
@endsection