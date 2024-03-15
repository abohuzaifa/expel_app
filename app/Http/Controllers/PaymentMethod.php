<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod as ModelsPaymentMethod;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentMethod extends Controller
{
    //
    public function index()
    {
        $data['perPage'] = 10;
        $data['payments'] = DB::table('payment_methods')->orderByDesc('id')->paginate($data['perPage']);
        return view('payment_method.index', $data);
    }
    public function edit($id)
    {
        $payment = ModelsPaymentMethod::find($id);
        // $user = User::find($wallet->id);
        return view('payment_method.edit', compact('payment'));
    }

    public function update($id, Request $request, ModelsPaymentMethod $payment)
    {
        $data = $_POST;
        // echo $id; exit;
        // check if product name already exists
        $value = DB::select("SELECT * FROM payment_methods WHERE slug=:slug AND id != :id", [':slug' => $data['slug'], ':id' => $id]);
        if(!empty($value))
        {
            return redirect()->route('payment_method.create')->with('error', trans('lang.slug_already_exist'));
        }

    
            $payment = DB::table('payment_methods')->where('id', '=', $id)->update([
                'slug' => $request->input('slug'),
                'name' => $request->input('name'),
                'public_key' => $request->input('public_key'),
                'secret_key' => $request->input('secret_key'),
            ]);
    
            // $product->save();
            return redirect()->route('payment_method.index')->with('success', trans('lang.update_message'));
    }

    public function active($id)
    {
        $ModelsPaymentMethod = ModelsPaymentMethod::find($id)->update(['status'=>1]);
        return redirect()->back()->with('success', trans('lang.status_active_success'));
    }
    public function inactive($id)
    {
        $ModelsPaymentMethod = ModelsPaymentMethod::find($id)->update(['status'=>0]);
        return redirect()->back()->with('success', trans('lang.status_deactive_success'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('payment_method.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // print_r($_POST); exit;
        $data = $_POST;

        // check if product name already exists
        $payment = DB::select("SELECT * FROM payment_methods WHERE slug=:slug", [':slug' => $data['slug']]);
        if(!empty($payment))
        {
            return redirect()->route('payment_method.create')->with('error', trans('lang.slug_already_exist'));
        }

    
            $payment = ModelsPaymentMethod::create([
                'slug' => $request->input('slug'),
                'name' => $request->input('name'),
                'public_key' => $request->input('public_key'),
                'secret_key' => $request->input('secret_key'),
                'created_by' => Auth::user()->id,
                'status' =>1,
            ]);
    
            // $product->save();
            return redirect()->route('payment_method.index')->with('success', trans('lang.create_message'));


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,ModelsPaymentMethod $payment)
    {
    //    echo "<pre>"; print_r($id); exit;
        $payment = ModelsPaymentMethod::find($id);
        $payment->delete();

        return redirect()->route('payment_method.index')->with('success', trans('lang.delete_message'));
    }

}
