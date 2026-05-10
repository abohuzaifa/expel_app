<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod as ModelsPaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PaymentMethod extends Controller
{
    public function index()
    {
        $data['perPage'] = 10;
        $data['payments'] = ModelsPaymentMethod::orderByDesc('id')->paginate($data['perPage']);
        return view('payment_method.index', $data);
    }

    public function edit($id)
    {
        $payment = ModelsPaymentMethod::findOrFail($id);
        return view('payment_method.edit', compact('payment'));
    }

    public function update($id, Request $request, ModelsPaymentMethod $payment)
    {
        $payment = ModelsPaymentMethod::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('payment_methods', 'name')->ignore($payment->id)],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('payment_methods', 'slug')->ignore($payment->id)],
            'public_key' => ['nullable', 'string', 'max:255'],
            'secret_key' => ['nullable', 'string', 'max:255'],
        ]);

        $payment->update([
            'slug' => $data['slug'],
            'name' => $data['name'],
            'name_ar' => $data['name_ar'] ?? $data['name'],
            'public_key' => $data['public_key'] ?? null,
            'secret_key' => $data['secret_key'] ?? null,
        ]);

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
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:payment_methods,name'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:payment_methods,slug'],
            'public_key' => ['nullable', 'string', 'max:255'],
            'secret_key' => ['nullable', 'string', 'max:255'],
        ]);

        ModelsPaymentMethod::create([
            'slug' => $data['slug'],
            'name' => $data['name'],
            'name_ar' => $data['name_ar'] ?? $data['name'],
            'public_key' => $data['public_key'] ?? null,
            'secret_key' => $data['secret_key'] ?? null,
            'created_by' => Auth::id(),
            'status' => 1,
        ]);

        return redirect()->route('payment_method.index')->with('success', trans('lang.create_message'));


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('payment_method.edit', $id);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,ModelsPaymentMethod $payment)
    {
        $payment = ModelsPaymentMethod::findOrFail($id);
        $payment->delete();

        return redirect()->route('payment_method.index')->with('success', trans('lang.delete_message'));
    }

}
