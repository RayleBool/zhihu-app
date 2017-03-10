<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class QuestionFollowController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function follow($quesiton)
    {
        Auth::user()->followThis($quesiton);

        return back();
    }
 
}
