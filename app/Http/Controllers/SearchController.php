<?php
/**
 * Created by PhpStorm.
 * User: sjose
 * Date: 11/14/2017
 * Time: 2:02 PM
 */

namespace App\Http\Controllers;
use App\AwsLex;
use Aws\Handler\GuzzleV6\GuzzleHandler;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Input;
use Aws\LexRuntimeService\LexRuntimeServiceClient;
use GuzzleHttp\Client;

class SearchController extends Controller
{
    public function index()
    {
        $searchQuery = \Request::get('q');
        return redirect()->action('SearchController@search', ['q' => $searchQuery])->with('message', 'State saved correctly!!!');
    }

    public function search()
    {
        $searchQuery = \Request::get('q');

        // TODO: prepare view for result set :D
        $rs = $this->getIntent($searchQuery);
        return view('search', ['q' => $searchQuery, 'rs' => $rs]);
    }

    private function getIntent($searchQuery)
    {
        $aws = new AwsLex();
        $sdk = new \Aws\Sdk($aws->getCredentials());
        $lex = $sdk->createLexRuntimeService();

        $input = [
            'botAlias' => 'camille_main', // REQUIRED
            'botName' => 'Camille', // REQUIRED
            'inputText' => $searchQuery, // REQUIRED
            'userId' => 'home', // REQUIRED
        ];

        $rs = null;
        $lexResult = $lex->postText($input);

        if ($lexResult->get('dialogState') === 'ElicitIntent') {
            $rs = $this->getDataWithoutIntent($searchQuery);
        } elseif ($lexResult->get('dialogState') === 'ReadyForFulfillment') {
            // TODO: where can we use intent name?
            $intentName = $lexResult->get('intentName');
            $rs = $this->getDataFromEndpoint($lexResult->get('slots'));
        }

        return $rs;
    }

    private function getDataFromEndpoint($searchParams)
    {
        $url = 'https://mp1180ms5a.execute-api.us-west-2.amazonaws.com/Prod/search';
        $client = new Client();
        $response = $client->post($url, [
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
            'body' => json_encode($searchParams),
        ]);

        $body = $response->getBody();
        return $body->getContents();
    }

    private function getDataWithoutIntent($searchStr)
    {
        $data = array();
        $data['title'] = $this->getDataFromEndpoint(['title' => $searchStr]);
        $data['creator'] = $this->getDataFromEndpoint(['creator' => $searchStr]);

        return $data;
    }


}