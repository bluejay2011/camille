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
use Illuminate\Http\Request as LaravelRequest;

class SearchController extends Controller
{
    private $perPage = 10;
    private $page = 1;

    public function index()
    {
        $searchQuery = \Request::get('q');
        return redirect()->action('SearchController@search', ['q' => $searchQuery])->with('message', 'State saved correctly!!!');
    }

    public function search(LaravelRequest $request)
    {
        $searchQuery = \Request::get('q');

        // TODO: prepare view for result set :D
        $rs = $this->getIntent($searchQuery, $request);
        if ($rs['intentName'] === 'none') {
            // show the generic view
            return view($rs['view'], ['q' => $searchQuery, 'rs' => $rs]);
        } else {
            // show search results
            $pageNumber = $request->get('page', 1);
            $items = collect(\GuzzleHttp\json_decode($rs['resultSet']));
            $totalCount = $items->get('hits')->found;

            $path = '';
            if ($searchQuery) {
                $path = "search?q=" . urlencode($searchQuery);
            }

            if ($totalCount > 0) {
                return view('targeted_search', [
                    'q' => $searchQuery,
                    'pageNumber' => $pageNumber,
                    'totalCount' => $totalCount,
                    'items' => $items,
                    'pagination' => \BootstrapComponents::pagination($items, $totalCount, $pageNumber, $this->perPage,
                        $path, ['arrows' => true])
                ]);
            } else {
                return view('no_match', ['q' => $searchQuery]);
            }
        }
    }

    private function getIntent($searchQuery, $request)
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
        $view = 'search';
        $lexResult = $lex->postText($input);

        $intentName = "none";
        if ($lexResult->get('dialogState') === 'ElicitIntent') {
            // We'll only get the first 5
            $rs = $this->getDataWithoutIntent($searchQuery, $request, 3);
        } elseif ($lexResult->get('dialogState') === 'ReadyForFulfillment') {
            $offset = $this->getPage($request);
            $intentName = $lexResult->get('intentName');
            $rs = $this->getDataFromEndpoint($lexResult->get('slots'), $request, $offset);
            $view = 'targeted_search';
        }

        return array('intentName' => $intentName, 'resultSet' => $rs, 'view' => $view);
    }

    private function getPage($request)
    {
        $pageNumber = $request->get('page', 1);
        if ($pageNumber == null || $pageNumber == 1) {
            $offset = 0;
        } else {
            $offset = ($pageNumber - 1) * $this->perPage;
        }

        return $offset;
    }

    private function getDataFromEndpoint($searchParams, $request, $offset = 0, $size = 10)
    {
        // Let's clean search params, should have no null data
        $cleanParams = array_filter($searchParams);
        /*$cleanParams = array();
        foreach($searchParams as $key => $value) {
            if (isset($value)) {
                $cleanParams[$key] = $value;
            }
        }*/

        $query = new \stdClass();
        $query->query = $cleanParams;
        $query->start = $offset;
        $query->size = $size;

        $url = 'https://mp1180ms5a.execute-api.us-west-2.amazonaws.com/Prod/search';
        $client = new Client();
        $response = $client->post($url, [
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
            'body' => json_encode($query),
        ]);

        $body = $response->getBody();
        return $body->getContents();
    }

    private function decode($data)
    {
        $items = \GuzzleHttp\json_decode($data);
        $totalCount = $items->hits->found;

        if ($totalCount > 0) {
            return $items;
        }

        return 0;
    }

    private function getDataWithoutIntent($searchStr, $request, $size)
    {
        $data = array();
        $data['title'] = $this->decode($this->getDataFromEndpoint(
            ['title' => $searchStr], $request, 0, $size
        ));
        $data['creator'] = $this->decode($this->getDataFromEndpoint(
            ['creator' => $searchStr], $request, 0, $size
        ));
        $data['website-CambridgeEnglish'] = $this->decode($this->getDataFromEndpoint(
            ['title' => $searchStr, 'website' => 'Cambridge English'], $request, 0, $size
        ));
        $data['website-CambridgeCore'] = $this->decode($this->getDataFromEndpoint(
            ['title' => $searchStr, 'website' => 'Cambridge Core'], $request, 0, $size
        ));
        $data['website-AcademicProfessional'] = $this->decode($this->getDataFromEndpoint(
            ['title' => $searchStr, 'website' => 'Academic and Professional'], $request, 0, $size
        ));

        return $data;
    }

    /*public function searchWithPaginate(LaravelRequest $request, $resultSet)
    {
        $items = collect(\GuzzleHttp\json_decode($resultSet));
        $totalCount = $items->get('hits')->found;

        return view('targeted_search', [
            'pageNumber' => $pageNumber,
            'totalCount' => $totalCount,
            'items' => $items,
            'pagination' => \BootstrapComponents::pagination($items, $totalCount, $pageNumber, $this->perPage, '', ['arrows' => true])
        ]);
    }*/

    /*public function searchWithPaginate(LaravelRequest $request)
    {
        $this->perPage = 10;
        $pageNumber = $request->get('page', 1);
        if ($pageNumber == null || $pageNumber == 1) {
            $offset = 0;
        } else {
            $offset = ($pageNumber - 1) * $this->perPage;
        }

        $query = new \stdClass();
        $query->query = [
            "title" => "John"
        ];
        $query->start = $offset;

        $items = collect(\GuzzleHttp\json_decode($this->getDataFromEndpoint($query)));
        $totalCount = $items->get('hits')->found;

        return view('targeted_search', [
            'pageNumber' => $pageNumber,
            'totalCount' => $totalCount,
            'items' => $items,
            'pagination' => \BootstrapComponents::pagination($items, $totalCount, $pageNumber, $this->perPage, '', ['arrows' => true])
        ]);
    }*/


}