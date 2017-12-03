@if ($data)
<div class="dataSet">
    <div class="row">
        <div class="boxed col-md-6 offset-md-1 ">
            <h5 class="gold"><a href="{{$link}}">{{$mainTitle}}</a></h5>
            @if (isset($data->hits->hit))
                @foreach ($data->hits->hit as $item)
                    <div class="resultSet">
                        <?php
                        $title = isset($item->fields->title)? $item->fields->title[0] : "";
                        if ($title) {
                            $url = isset($item->fields->url)? $item->fields->url[0] : "";
                        }
                        ?>
                        <h6><a href="{{$url}}">{{$title}}</a></h6>
                        <?php if(isset($item->fields->creator)): ?>
                        <span class="author">By: {{ implode($item->fields->creator, ", ") }}</span>
                        <?php endif ?>
                        <p class="desc"> {{ isset($item->fields->description)? $item->fields->description[0] : "" }}</p>
                        <br />
                    </div>
                @endforeach
                    <div class="resultSet-more">
                        Show: <a href="#">All Results</a>
                    </div>
            @endif
        </div>
    </div>
</div>
@endif