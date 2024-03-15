<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    //
    public function index()
    {
        return view('notifications.index');
    }

    public function edit($id)
    {
        // echo $id;
        // print_r($_GET); exit;
        if($id == 'all')
        {
            $data = DB::table('users')->where('is_read', 0)->update(['is_read' => 1]);
            if(isset($_GET['choice']))
            {
                return redirect()->back();
            }
        } else {
            $data = DB::table('users')->where('id', $id)->update(['is_read' => 1]);
            if(isset($_GET['choice']))
            {
                return redirect()->back();
            }
        }
        
        return redirect()->route('users.index');
    }
}
