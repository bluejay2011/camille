<?php
/**
 * Created by PhpStorm.
 * User: sjose
 * Date: 11/14/2017
 * Time: 5:54 PM
 */

namespace App\Http\Controllers;


class SigninController extends Controller
{

    public function index()
    {
        return view('signin.index');
    }
}