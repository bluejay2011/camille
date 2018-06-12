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
        $show = \Request::get('show'); 
        $showAll = isset($show) && $show === 'all'? true: false;

        // TODO: prepare view for result set :D
        $rs = $this->getIntent($searchQuery, $request, $showAll);

        if ($rs['resultSet'] === false) {
            // no result from endpoint
            return view('no_match', ['q' => $searchQuery]);
        } elseif ($rs['intentName'] === 'none') {
            $baseUrl = $this->getCurrentBaseUrl();
            if ($searchQuery) {
                $baseUrl .= '?q=' . $searchQuery;
            }

            // show the generic view
            $titleMap = [
                'creator' => [
                    'title' => 'Author',
                    'link' => '#',
                    'more_link' => '#'
                ],
                'website-CambridgeEnglish' => [
                    'title' => 'Cambridge English',
                    'link' => 'http://cambridge.org/cambridgeenglish',
                    'more_link' => $baseUrl.'&website=CambridgeEnglish&show=all'
                 ],
                'website-AcademicProfessional' => [
                    'title' => 'Academic and Professional',
                    'link' => 'http://cambridge.org/academic',
                    'more_link' => $baseUrl.'&website=AcademicProfessional&show=all'
                ],
                'website-CambridgeCore' => [
                    'title' => 'Cambridge Core',
                    'link' => 'http://cambridge.org/core',
                    'more_link' => $baseUrl.'&website=CambridgeCore&show=all'
                ],
                'website-Education' => [
                    'title' => 'Global Education',
                    'link' => 'http://cambridge.org/education',
                    'more_link' => $baseUrl.'&website=Education&show=all'
                ],
            ];

            if ($rs && count($rs['resultSet']) > 0) {
                return view($rs['view'], ['q' => $searchQuery, 'rs' => $rs, 'titleMap' => $titleMap]);
            } else {
                return view('no_match', ['q' => $searchQuery]);
            }
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

    private function getIntent($searchQuery, $request, $showAll = false)
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
            if ($showAll == false) {
                // We'll only get the first 3
                $rs = $this->getDataWithoutIntent($searchQuery, $request, 3);   
            } else {
                $website = $this->convertWebsite(\Request::get('website')); 
                $intentName = 'custom';
                $offset = $this->getPage($request);
                $slots = ['title' => $searchQuery, 'website' => $website];
                $rs = $this->getDataFromEndpoint($slots, $request, $offset);
                $view = 'targeted_search';
            }
        } elseif ($lexResult->get('dialogState') === 'ReadyForFulfillment' || $lexResult->get('dialogState') === 'ElicitSlot') {
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
        $query = new \stdClass();
        $query->query = new \stdClass();
        $query->start = $offset;
        $query->size = $size;

        // Let's clean search params, should have no null data
        $cleanParams = array_filter($searchParams);
        foreach($cleanParams as  $key => $param) {
            if ((in_array("journal", $cleanParams) || in_array("journals", $cleanParams)) && $key === "tags") {
                $key = "category";
            }

            $query->query = $this->inputFormatter($query->query, $key, [$param]);
        }

        if (session()->exists('logged_in')) {
            $query->query = $this->inputFormatter($query->query, 'audience', [session()->get('type')]);
        }


        $url = 'https://rjialx5odh.execute-api.us-east-1.amazonaws.com/Prod/search';
        $client = new Client();
        $response = $client->post($url, [
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
            'body' => json_encode($query),
            'http_errors' => false
        ]);
        $statusCode = $response->getStatusCode();

        if (in_array($statusCode, [200, 301])) {       
            $body = $response->getBody();
            return $body->getContents();
        } 

        return false;
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
        //$data['title'] = $this->decode($this->getDataFromEndpoint(
        //    ['title' => $searchStr], $request, 0, $size
        //));
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
        $data['website-Education'] = $this->decode($this->getDataFromEndpoint(
            ['title' => $searchStr, 'website' => 'Global Education'], $request, 0, $size
        ));

        return $this->sortByScore($data);
    }

    private function convertWebsite($website) {
        switch ($website) {
            case 'CambridgeEnglish':
                return 'Cambridge English';
            case 'CambridgeCore':
                return 'Cambridge Core';
            case 'AcademicProfessional':
                return 'Academic and Professional';
            case 'Education':
                return 'Global Education';
        }
    }

    private function sortByScore($data) {

        $forSorting = array();
        foreach($data as $key => $datum) {
            if(isset($datum->hits->hit[0]->fields->_score)) {
                $forSorting[$key] = (float) $datum->hits->hit[0]->fields->_score[0];
            }
        }
        arsort($forSorting);

        $sorted = array();
        foreach($forSorting as $key => $value) {
            $sorted[$key] = $data[$key];
        }

        return $sorted;
    }

    private function inputFormatter($query, $field, array $searchParams)
    {
        $searchArr = array();

        foreach($searchParams as $searchStr) {
            $searchInput = new \stdClass();
            $searchInput->searchInput = $searchStr;

            $searchArr[] = $searchInput;
        }

        $query->{$field} = new \stdClass();
        $query->{$field}->searchInputArr = $searchArr;

        return $query;
    }

    private function getCurrentBaseUrl() 
    {
        // output: /myproject/index.php
        $currentPath = $_SERVER['PHP_SELF']; 

        // output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index ) 
        $pathInfo = parse_url($_SERVER['REQUEST_URI']); 

        // output: localhost
        $hostName = $_SERVER['HTTP_HOST']; 

        // output: http://
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';

        // return: http://localhost/myproject/
        return $protocol.'://'.$hostName.$pathInfo['path']."/";
    }
}