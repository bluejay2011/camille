@if ($data)
<div class="dataSet">
	<div class="row">
		<h6 class="col-md-10 offset-md-1 gold">{{$mainTitle}}</h6>
	</div>
	<div class="row">
		@if (isset($data->hits->hit))
			@foreach ($data->hits->hit as $item)
				<div class="col-md-6 offset-md-1">
					<?php
					$title = isset($item->fields->title)? $item->fields->title[0] : "";
					if ($title) {
						$url = isset($item->fields->url)? $item->fields->url[0] : "";
					}
					?>
					<h6><a href="{{$url}}">{{$title}}</a></h6>
					<span class="author">By: {{ implode($item->fields->creator, ", ") }}</span>
					<p class="desc"> {{ isset($item->fields->description)? $item->fields->description[0] : "" }}</p>
					<br />
				</div>
			@endforeach
		@endif
	</div>
</div>
@endif