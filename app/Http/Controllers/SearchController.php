<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Conference;

class SearchController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function find(Request $request)
    {
        return Conference::search($request->get('q'))->get();

        $conf = Conference::where('confAcronym','like','%'.$request->q.'%')->select('confAcronym as acronym')->get();
        return response()->json($data);
    }
}
