<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentMethod;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    protected function validatedPayload(Request $request, ?int $paymentMethodId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('payment_methods', 'name')->ignore($paymentMethodId)],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('payment_methods', 'slug')->ignore($paymentMethodId)],
            'public_key' => ['nullable', 'string', 'max:255'],
            'secret_key' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'boolean'],
        ]);
    }

    public function create(Request $request)
    {
        $attrs = $this->validatedPayload($request);

        $paymetMethod = PaymentMethod::create([
            'name' => $attrs['name'],
            'name_ar' => $attrs['name_ar'] ?? $attrs['name'],
            'status' => array_key_exists('status', $attrs) ? (int) $attrs['status'] : 1,
            'public_key' => $attrs['public_key'] ?? null,
            'secret_key' => $attrs['secret_key'] ?? null,
            'created_by' => auth()->id(),
            'slug' => $attrs['slug'],
        ]);

        if($paymetMethod)
        {
            return response([
                'status' => '1',
                'payment_method' => $paymetMethod,
            ]);
        } else {
            return response([
                'status'=> '0',
                'message' => 'Something went wrong'
            ]);
        }
    }

    public function list(Request $request)
    {
        $query = PaymentMethod::query()->orderByDesc('id');

        if (!$request->boolean('include_inactive')) {
            $query->where('status', 1);
        }

        $list = $query->get();

        if($list->isNotEmpty())
        {
            return response([
                'status'=> '1',
                'list'=> $list,
            ]);
        } else {
            return response([
                'status'=> '0',
                'message'=> 'Payment list not found',
                'list' => [],
            ]);
        }
    }

    public function show($id)
    {
        $paymentMethod = PaymentMethod::find($id);

        if (!$paymentMethod) {
            return response([
                'status' => '0',
                'message' => 'Payment method not found',
            ], 404);
        }

        return response([
            'status' => '1',
            'payment_method' => $paymentMethod,
        ]);
    }

    public function update(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::find($id);

        if (!$paymentMethod) {
            return response([
                'status' => '0',
                'message' => 'Payment method not found',
            ], 404);
        }

        $attrs = $this->validatedPayload($request, $paymentMethod->id);

        $paymentMethod->update([
            'name' => $attrs['name'],
            'name_ar' => $attrs['name_ar'] ?? $attrs['name'],
            'slug' => $attrs['slug'],
            'public_key' => $attrs['public_key'] ?? null,
            'secret_key' => $attrs['secret_key'] ?? null,
            'status' => array_key_exists('status', $attrs) ? (int) $attrs['status'] : $paymentMethod->status,
        ]);

        return response([
            'status' => '1',
            'message' => 'Payment method updated successfully',
            'payment_method' => $paymentMethod->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::find($id);

        if (!$paymentMethod) {
            return response([
                'status' => '0',
                'message' => 'Payment method not found',
            ], 404);
        }

        $paymentMethod->delete();

        return response([
            'status' => '1',
            'message' => 'Payment method deleted successfully',
        ]);
    }
}
