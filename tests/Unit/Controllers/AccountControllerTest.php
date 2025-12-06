<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_display_accounts_index()
    {
        Account::factory()->count(3)->create();

        $response = $this->get(route('accounts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('accounts.index');
        $response->assertViewHas('accounts');
    }

    /** @test */
    public function it_can_store_a_new_account()
    {
        $accountData = [
            'code' => '1000',
            'description' => 'Test Account'
        ];

        $response = $this->post(route('accounts.store'), $accountData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Account created successfully!');
        
        $this->assertDatabaseHas('accounts', [
            'code' => '1000',
            'description' => 'Test Account'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing_account()
    {
        $response = $this->post(route('accounts.store'), []);

        $response->assertSessionHasErrors(['code', 'description']);
    }

    /** @test */
    public function it_validates_unique_code_when_storing_account()
    {
        Account::factory()->create(['code' => '1000']);

        $response = $this->post(route('accounts.store'), [
            'code' => '1000',
            'description' => 'Test Account'
        ]);

        $response->assertSessionHasErrors(['code']);
    }

    /** @test */
    public function it_can_show_edit_form_for_account()
    {
        $account = Account::factory()->create();

        $response = $this->get(route('accounts.edit', $account->id));

        $response->assertStatus(200);
        $response->assertViewIs('accounts.index');
        $response->assertViewHas(['accounts', 'account']);
    }

    /** @test */
    public function it_can_update_an_account()
    {
        $account = Account::factory()->create();

        $updateData = [
            'code' => '2000',
            'description' => 'Updated Account'
        ];

        $response = $this->put(route('accounts.update', $account->id), $updateData);

        $response->assertRedirect(route('accounts.index'));
        $response->assertSessionHas('success', 'Account updated successfully!');
        
        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'code' => '2000',
            'description' => 'Updated Account'
        ]);
    }

    /** @test */
    public function it_can_delete_an_account()
    {
        $account = Account::factory()->create();

        $response = $this->delete(route('accounts.destroy', $account->id));

        $response->assertRedirect(route('accounts.index'));
        $response->assertSessionHas('success', 'Account deleted successfully!');
        
        $this->assertDatabaseMissing('accounts', [
            'id' => $account->id
        ]);
    }

    /** @test */
    public function it_returns_json_response_when_requested()
    {
        Account::factory()->count(2)->create();

        $response = $this->getJson(route('accounts.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'code', 'description']
            ]
        ]);
    }

    /** @test */
    public function it_can_store_account_via_json()
    {
        $accountData = [
            'code' => '3000',
            'description' => 'JSON Account'
        ];

        $response = $this->postJson(route('accounts.store'), $accountData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => ['id', 'code', 'description']
        ]);
    }

    /** @test */
    public function it_can_update_account_via_json()
    {
        $account = Account::factory()->create();

        $updateData = [
            'code' => '4000',
            'description' => 'Updated JSON Account'
        ];

        $response = $this->putJson(route('accounts.update', $account->id), $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['id', 'code', 'description']
        ]);
    }

    /** @test */
    public function it_can_delete_account_via_json()
    {
        $account = Account::factory()->create();

        $response = $this->deleteJson(route('accounts.destroy', $account->id));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Account deleted successfully'
        ]);
    }
}