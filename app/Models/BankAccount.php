<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $table = 'user_bank_accounts';

    protected $fillable = [
        'user_id',
        'bank_id',
        'account_holder_name',
        'account_number',
        'branch_code',
        'iban',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_notes',
        'is_primary',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'is_primary' => 'boolean',
    ];

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to Bank
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Relationship to Verifier (Admin)
     */
    public function verifiedByUser()
    {
        return $this->belongsTo(User::class, 'verified_by', 'id');
    }

    /**
     * Mask account number for display
     */
    public function maskAccountNumber()
    {
        $account = $this->account_number;
        return strlen($account) > 4 
            ? str_repeat('*', strlen($account) - 4) . substr($account, -4)
            : $account;
    }
}
