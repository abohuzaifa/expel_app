<?php
namespace App\Http\Controllers;

use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function showMap(Request $request)
    {
        $data = $request->query('data'); // Retrieve the query parameter 'data'
        return view('address', compact('data'));
    }

    public function saveAddress(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'request_id' => 'required',
        ]);

        $request_id = base64_decode($request->request_id);
        $update = ModelsRequest::where('id', $request_id)->update([
            'receiver_address' => $request->address,
            'receiver_lat' => $request->latitude,
            'receiver_long' => $request->longitude,
        ]);

        if ($update) {
            return redirect()->back()->with('success', 'Address saved successfully!');
        }

        return redirect()->back()->with('error', 'Failed to save the address. Please try again.');
    }
}
