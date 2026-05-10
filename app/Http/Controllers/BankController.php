<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BankController extends Controller
{
    /**
     * Show all banks
     */
    public function index()
    {
        $data['perPage'] = 10;
        $data['banks'] = Bank::orderByDesc('id')->paginate($data['perPage']);
        return view('banks.index', $data);
    }

    /**
     * Show create bank form
     */
    public function create()
    {
        return view('banks.create');
    }

    /**
     * Store new bank
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:banks,name',
            'branch_code' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        Bank::create($data);

        return redirect()->route('banks.index')
            ->with('success', 'Bank created successfully.');
    }

    /**
     * Show edit bank form
     */
    public function edit($id)
    {
        $bank = Bank::findOrFail($id);
        return view('banks.edit', compact('bank'));
    }

    /**
     * Update bank
     */
    public function update($id, Request $request)
    {
        $bank = Bank::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('banks', 'name')->ignore($bank->id)],
            'branch_code' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        $bank->update($data);

        return redirect()->route('banks.index')
            ->with('success', 'Bank updated successfully.');
    }

    /**
     * Delete bank
     */
    public function destroy($id)
    {
        $bank = Bank::findOrFail($id);
        $bank->delete();

        return redirect()->route('banks.index')
            ->with('success', 'Bank deleted successfully.');
    }

    /**
     * Toggle bank status active
     */
    public function active($id)
    {
        $bank = Bank::findOrFail($id);
        $bank->update(['status' => 1]);

        return redirect()->route('banks.index')
            ->with('success', 'Bank activated successfully.');
    }

    /**
     * Toggle bank status inactive
     */
    public function inactive($id)
    {
        $bank = Bank::findOrFail($id);
        $bank->update(['status' => 0]);

        return redirect()->route('banks.index')
            ->with('success', 'Bank deactivated successfully.');
    }
}
