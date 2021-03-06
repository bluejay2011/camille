<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AwsLex extends Model
{
    public function getCredentials()
    {
        return [
            'region'  => 'us-east-1',
            'version' => 'latest',
            'credentials' => [
                'key'    => '',
                'secret' => '',
            ]
        ];
    }

}
