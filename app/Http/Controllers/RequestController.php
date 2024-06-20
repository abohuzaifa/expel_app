<?php

namespace App\Http\Controllers;

use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    //
    public function index()
    {
        $data['perPage'] = 10;
        $data['requests'] = ModelsRequest::with('user', 'offer.user')->paginate($data['perPage']);
        // echo "<pre>"; print_r($data['requests']); exit;
        return view('request.index',$data);
    }
    public function show($id)
    {
        $request = ModelsRequest::with(['user', 'offer.user'])->find($id);
        return view('request.show', compact('request'));
    }
}
