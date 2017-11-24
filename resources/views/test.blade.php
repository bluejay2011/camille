<?php
/**
 * Created by PhpStorm.
 * User: pvillareal
 * Date: 11/9/2017
 * Time: 4:23 PM
 */

use Aws\LexRuntimeService\LexRuntimeServiceClient;
$sharedConfig = [
        'region'  => 'us-east-1',
        'version' => 'latest',
        'credentials' => [
                'key'    => 'AKIAJRU64634IGXS4JFQ',
                'secret' => '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',
        ]
];

$sdk = new Aws\Sdk($sharedConfig);


$lex = $sdk->createLexRuntimeService();

$input = [
        'botAlias' => 'camille_main', // REQUIRED
        'botName' => 'Camille', // REQUIRED
        'inputText' => 'find happiness', // REQUIRED
        'userId' => 'home', // REQUIRED
];

dd($lex->postText($input));


