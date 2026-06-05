<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    //
    public function index(Request $request)
    {
        $data['perPage'] = 10;
        
        $query = ModelsRequest::with('user', 'offer.user');
        
        // Search filter
        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('parcel_address', 'like', '%' . $search . '%')
                  ->orWhere('receiver_address', 'like', '%' . $search . '%')
                  ->orWhere('receiver_mobile', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($subQ) use ($search) {
                      $subQ->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $data['requests'] = $query->orderBy('id', 'desc')->paginate($data['perPage']);
        
        // Attach latest history to each request for tracking button
        $requestIds = $data['requests']->pluck('id');
        $latestHistories = History::whereIn('request_id', $requestIds)
            ->select('id', 'request_id', 'lat', 'long', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('request_id');
        
        foreach ($data['requests'] as $item) {
            $item->latestHistory = isset($latestHistories[$item->id]) ? $latestHistories[$item->id]->first() : null;
        }
        
        return view('request.index', $data);
    }
    public function show($id)
    {
        $request = ModelsRequest::with(['user', 'offer.user'])->find($id);
        return view('request.show', compact('request'));
    }
}
