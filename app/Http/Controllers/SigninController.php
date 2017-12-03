<?php
/**
 * Created by PhpStorm.
 * User: sjose
 * Date: 11/14/2017
 * Time: 5:54 PM
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SigninController extends Controller
{

    public function index()
    {
        return view('layouts.signin.index', ['message' => false]);
    }

    public function doLogin(Request $request)
    {
        $email = $request->get('username');
        switch($email) {
            case 'teacher@cambridge.org':
                $this->getTeacherAccount();
                break;
            case 'student@cambridge.org':
                $this->getStudentAccount();
                break;
            case 'professor@cambridge.org':
                $this->getProfessorAccount();
                break;
            case 'researcher@cambridge.org':
                $this->getResearcherAccount();
                break;
        }

        if (session()->exists('logged_in')) {
            return redirect('/');
        } else {
            return view('/layouts.signin.index', ['message' => true]);
        }
    }

    private function getTeacherAccount()
    {
        session([
            'logged_in' => true,
            'type' => 'teacher',
            'name' => 'Teacher Yvonne Camay',
            'email' => 'teacher@cambridge.org'
        ]);
    }

    private function getStudentAccount()
    {
        session([
            'logged_in' => true,
            'type' => 'student',
            'name' => 'Student Felix Natividad',
            'email' => 'student@cambridge.org'
        ]);
    }

    private function getProfessorAccount()
    {
        session([
            'logged_in' => true,
            'type' => 'professor',
            'name' => 'Professor Xavier',
            'email' => 'professor@cambridge.org'
        ]);
    }

    private function getResearcherAccount()
    {
        session([
            'logged_in' => true,
            'type' => 'researcher',
            'name' => 'Researcher Brene Brown',
            'email' => 'researcher@cambridge.org'
        ]);
    }

    public function logout(Request $request)
    {
        session()->flush();
        return redirect('/');
    }
}