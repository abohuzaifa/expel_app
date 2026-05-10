<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DriverVerificationController extends Controller
{
    public function index()
    {
        $perPage = 10;
        $drivers = User::where('user_type', 2)->orderByDesc('id')->paginate($perPage);

        return view('users.drivers', [
            'perPage' => $perPage,
            'drivers' => $drivers,
        ]);
    }

    public function show($id)
    {
        $driver = User::where('user_type', 2)->findOrFail($id);

        return view('users.driver_verification', [
            'driver' => $driver,
        ]);
    }

    public function update(Request $request, $id)
    {
        $driver = User::where('user_type', 2)->findOrFail($id);

        $data = $request->validate([
            'verification_status' => 'required|in:pending,verified,rejected',
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        $payload = [
            'verification_status' => $data['verification_status'],
            'verification_notes' => $data['verification_notes'] ?? null,
            'verified_by' => null,
            'verified_at' => null,
        ];

        if ($data['verification_status'] === 'verified') {
            $payload['status'] = 1;
            $payload['verified_by'] = auth()->id();
            $payload['verified_at'] = now();
        }

        if ($data['verification_status'] === 'rejected') {
            $payload['status'] = 0;
            $payload['verified_by'] = auth()->id();
            $payload['verified_at'] = now();
        }

        $driver->update($payload);

        $message = match ($data['verification_status']) {
            'verified' => 'Your driver documents have been verified successfully.',
            'rejected' => 'Your driver documents were rejected. Please review the admin notes and update them.',
            default => 'Your driver verification status was updated and is pending review.',
        };

        User::storeAppNotification($driver->id, $message, 'profile', 'account_updates');

        if (!empty($driver->device_token)) {
            User::sendNotification([
                'title' => 'Driver Verification',
                'body' => $message,
                'device_token' => $driver->device_token,
                'is_driver' => 1,
                'request_id' => '0',
                'user_id' => $driver->id,
                'setting_key' => 'account_updates',
            ]);
        }

        return redirect()
            ->route('drivers.verifications.show', $driver->id)
            ->with('success', 'Driver verification updated successfully.');
    }
}