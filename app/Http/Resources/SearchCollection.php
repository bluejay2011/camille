<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SearchCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);

        for($x=1; $x < 20; $x++) {
            $obj[] = new \stdClass(array(
                "id" => $x,
                "name" => "lili " . $x,
                "email" => "abc@gmail.com"
            ));
        }

        $request = [
            "data" => $obj,
            "links" => new \stdClass(array(
                "first" => "http://example.com/pagination?page=1",
                "last" => "http://example.com/pagination?page=1",
                "prev" => null,
                "next" => null
            )),
            "meta" => new \stdClass(array(
                "current_page" => 1,
                "from" => 1,
                "last_page"=> 1,
                "path"=> "http://example.com/pagination",
                "per_page"=>15,
                "to"=> 10,
                "total"=> 10
            ))
        ];

        return $request;
    }
}
