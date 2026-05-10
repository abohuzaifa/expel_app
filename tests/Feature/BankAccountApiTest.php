<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Bank;
use App\Models\BankAccount;

class BankAccountApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $bank;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create a test user (driver)
        $this->user = User::factory()->create([
            'user_type' => 2, // driver
            'email' => 'driver' . rand(1000, 9999) . '@test.com',
            'mobile' => '555' . rand(1000000, 9999999),
        ]);

        // Create a test bank
        $this->bank = Bank::create([
            'name' => 'Test Bank',
            'account_number' => '123456789',
            'branch_code' => 'MAIN',
            'status' => 1,
        ]);
    }

    public function test_user_can_list_bank_accounts()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/bank-accounts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'bank_accounts',
            ]);
    }

    public function test_user_can_add_bank_account()
    {
        $data = [
            'bank_id' => $this->bank->id,
            'account_holder_name' => 'Test Driver',
            'account_number' => '9876543210',
            'branch_code' => 'BRANCH1',
            'iban' => 'AE123456789012345',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/bank-accounts', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'bank_account' => [
                    'id',
                    'bank_id',
                    'account_holder_name',
                    'account_number',
                    'branch_code',
                    'iban',
                    'verification_status',
                ]
            ])
            ->assertJson(['status' => 1]);

        $this->assertDatabaseHas('user_bank_accounts', [
            'user_id' => $this->user->id,
            'bank_id' => $this->bank->id,
            'account_holder_name' => 'Test Driver',
            'verification_status' => 'pending',
        ]);
    }

    public function test_user_can_view_bank_account()
    {
        $bankAccount = BankAccount::create([
            'user_id' => $this->user->id,
            'bank_id' => $this->bank->id,
            'account_holder_name' => 'Test Driver',
            'account_number' => '9876543210',
            'branch_code' => 'BRANCH1',
            'iban' => 'AE123456789012345',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/bank-accounts/{$bankAccount->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'bank_account' => [
                    'id',
                    'bank_id',
                    'bank_name',
                    'account_holder_name',
                    'account_number',
                    'verification_status',
                ]
            ])
            ->assertJson(['status' => 1]);
    }

    public function test_user_can_update_pending_bank_account()
    {
        $bankAccount = BankAccount::create([
            'user_id' => $this->user->id,
            'bank_id' => $this->bank->id,
            'account_holder_name' => 'Test Driver',
            'account_number' => '9876543210',
            'verification_status' => 'pending',
        ]);

        $updateData = [
            'account_holder_name' => 'Updated Name',
            'account_number' => '1111111111',
            'branch_code' => 'BRANCH2',
            'iban' => 'AE987654321098765',
        ];

        $response = $this->actingAs($this->user)
            ->patchJson("/api/bank-accounts/{$bankAccount->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);

        $this->assertDatabaseHas('user_bank_accounts', [
            'id' => $bankAccount->id,
            'account_holder_name' => 'Updated Name',
            'account_number' => '1111111111',
        ]);
    }

    public function test_user_cannot_update_verified_bank_account()
    {
        $bankAccount = BankAccount::create([
            'user_id' => $this->user->id,
            'bank_id' => $this->bank->id,
            'account_holder_name' => 'Test Driver',
            'account_number' => '9876543210',
            'verification_status' => 'verified',
        ]);

        $updateData = [
            'account_holder_name' => 'Updated Name',
            'account_number' => '1111111111',
        ];

        $response = $this->actingAs($this->user)
            ->patchJson("/api/bank-accounts/{$bankAccount->id}", $updateData);

        $response->assertStatus(422)
            ->assertJson(['status' => 0]);
    }

    public function test_user_can_delete_pending_bank_account()
    {
        $bankAccount = BankAccount::create([
            'user_id' => $this->user->id,
            'bank_id' => $this->bank->id,
            'account_holder_name' => 'Test Driver',
            'account_number' => '9876543210',
            'verification_status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/bank-accounts/{$bankAccount->id}");

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);

        $this->assertDatabaseMissing('user_bank_accounts', [
            'id' => $bankAccount->id,
        ]);
    }

    public function test_user_cannot_delete_verified_bank_account()
    {
        $bankAccount = BankAccount::create([
            'user_id' => $this->user->id,
            'bank_id' => $this->bank->id,
            'account_holder_name' => 'Test Driver',
            'account_number' => '9876543210',
            'verification_status' => 'verified',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/bank-accounts/{$bankAccount->id}");

        $response->assertStatus(422)
            ->assertJson(['status' => 0]);

        $this->assertDatabaseHas('user_bank_accounts', [
            'id' => $bankAccount->id,
        ]);
    }

    public function test_user_can_set_primary_verified_bank_account()
    {
        // Create multiple bank accounts
        $bankAccount1 = BankAccount::create([
            'user_id' => $this->user->id,
            'bank_id' => $this->bank->id,
            'account_holder_name' => 'Test Driver 1',
            'account_number' => '9876543210',
            'verification_status' => 'verified',
            'is_primary' => true,
        ]);

        $bankAccount2 = BankAccount::create([
            'user_id' => $this->user->id,
            'bank_id' => $this->bank->id,
            'account_holder_name' => 'Test Driver 2',
            'account_number' => '1111111111',
            'verification_status' => 'verified',
            'is_primary' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/bank-accounts/{$bankAccount2->id}/set-primary");

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);

        // Check that only the second account is primary
        $this->assertDatabaseHas('user_bank_accounts', [
            'id' => $bankAccount2->id,
            'is_primary' => true,
        ]);

        $this->assertDatabaseHas('user_bank_accounts', [
            'id' => $bankAccount1->id,
            'is_primary' => false,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_bank_accounts()
    {
        $response = $this->getJson('/api/bank-accounts');
        $response->assertStatus(401);
    }

    public function test_guest_cannot_add_bank_account()
    {
        $data = [
            'bank_id' => $this->bank->id,
            'account_holder_name' => 'Test Driver',
            'account_number' => '9876543210',
        ];

        $response = $this->postJson('/api/bank-accounts', $data);
        $response->assertStatus(401);
    }
}
