<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Models\Account;
use App\Models\SubAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SubAccountControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_display_sub_accounts_index()
    {
        $account = Account::factory()->create();
        SubAccount::factory()->count(3)->create(['account_id' => $account->id]);

        $response = $this->get(route('sub-accounts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('sub-accounts.index');
        $response->assertViewHas(['subAccounts', 'accounts']);
    }

    /** @test */
    public function it_can_store_a_new_sub_account()
    {
        $account = Account::factory()->create();
        
        $subAccountData = [
            'code' => '1001',
            'description' => 'Test Sub Account',
            'account_id' => $account->id
        ];

        $response = $this->post(route('sub-accounts.store'), $subAccountData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Sub-account created successfully!');
        
        $this->assertDatabaseHas('sub_accounts', [
            'code' => '1001',
            'description' => 'Test Sub Account',
            'account_id' => $account->id
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing_sub_account()
    {
        $response = $this->post(route('sub-accounts.store'), []);

        $response->assertSessionHasErrors(['code', 'description', 'account_id']);
    }

    /** @test */
    public function it_validates_unique_code_when_storing_sub_account()
    {
        $account = Account::factory()->create();
        SubAccount::factory()->create(['code' => '1001', 'account_id' => $account->id]);

        $response = $this->post(route('sub-accounts.store'), [
            'code' => '1001',
            'description' => 'Test Sub Account',
            'account_id' => $account->id
        ]);

        $response->assertSessionHasErrors(['code']);
    }

    /** @test */
    public function it_validates_account_exists_when_storing_sub_account()
    {
        $response = $this->post(route('sub-accounts.store'), [
            'code' => '1001',
            'description' => 'Test Sub Account',
            'account_id' => 999 // Non-existent account
        ]);

        $response->assertSessionHasErrors(['account_id']);
    }

    /** @test */
    public function it_can_show_edit_form_for_sub_account()
    {
        $account = Account::factory()->create();
        $subAccount = SubAccount::factory()->create(['account_id' => $account->id]);

        $response = $this->get(route('sub-accounts.edit', $subAccount->id));

        $response->assertStatus(200);
        $response->assertViewIs('sub-accounts.index');
        $response->assertViewHas(['subAccounts', 'accounts', 'subAccount']);
    }

    /** @test */
    public function it_can_update_a_sub_account()
    {
        $account = Account::factory()->create();
        $newAccount = Account::factory()->create();
        $subAccount = SubAccount::factory()->create(['account_id' => $account->id]);

        $updateData = [
            'code' => '2001',
            'description' => 'Updated Sub Account',
            'account_id' => $newAccount->id
        ];

        $response = $this->put(route('sub-accounts.update', $subAccount->id), $updateData);

        $response->assertRedirect(route('sub-accounts.index'));
        $response->assertSessionHas('success', 'Sub-account updated successfully!');
        
        $this->assertDatabaseHas('sub_accounts', [
            'id' => $subAccount->id,
            'code' => '2001',
            'description' => 'Updated Sub Account',
            'account_id' => $newAccount->id
        ]);
    }

    /** @test */
    public function it_can_delete_a_sub_account()
    {
        $account = Account::factory()->create();
        $subAccount = SubAccount::factory()->create(['account_id' => $account->id]);

        $response = $this->delete(route('sub-accounts.destroy', $subAccount->id));

        $response->assertRedirect(route('sub-accounts.index'));
        $response->assertSessionHas('success', 'Sub-account deleted successfully!');
        
        $this->assertDatabaseMissing('sub_accounts', [
            'id' => $subAccount->id
        ]);
    }

    /** @test */
    public function it_returns_json_response_when_requested()
    {
        $account = Account::factory()->create();
        SubAccount::factory()->count(2)->create(['account_id' => $account->id]);

        $response = $this->getJson(route('sub-accounts.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'code', 'description', 'account_id']
            ]
        ]);
    }

    /** @test */
    public function it_can_store_sub_account_via_json()
    {
        $account = Account::factory()->create();
        
        $subAccountData = [
            'code' => '3001',
            'description' => 'JSON Sub Account',
            'account_id' => $account->id
        ];

        $response = $this->postJson(route('sub-accounts.store'), $subAccountData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => ['id', 'code', 'description', 'account_id']
        ]);
    }

    /** @test */
    public function it_deletes_sub_accounts_when_parent_account_is_deleted()
    {
        $account = Account::factory()->create();
        $subAccount = SubAccount::factory()->create(['account_id' => $account->id]);

        // Delete the parent account
        $account->delete();

        // Sub-account should be deleted due to cascade
        $this->assertDatabaseMissing('sub_accounts', [
            'id' => $subAccount->id
        ]);
    }
}