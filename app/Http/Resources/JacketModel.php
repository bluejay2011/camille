<?php
/**
 * Created by PhpStorm.
 * User: sjose
 * Date: 12/1/2017
 * Time: 9:32 PM
 */

namespace App\Http\Resources;


class JacketModel
{
    public function getJacketUrl($isbn, $size = 'small') {
        $assetsBaseImageLink = "http://assets.cambridge.org";
        $isbnFolder1 = substr($isbn, 0, 8);
        $isbnFolder2 = substr($isbn, 8, 5);
        $jacketSize = self::sizeCheck($size);
        if('large_cover' === $jacketSize) $isbn = $isbn . 'i';
        $assetsImageLink = "$assetsBaseImageLink/$isbnFolder1/$isbnFolder2/$jacketSize/$isbn.jpg";
        return $assetsImageLink;
    }

    private function sizeCheck($size) {
        if ($size == 'small') {
            return "cover";
        } elseif($size == 'large_cover') {
            return 'large_cover';
        }

        return "cover";
    }
}