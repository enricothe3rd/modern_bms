<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Models\ExpenseType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ExpenseTypeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_display_expense_types_index()
    {
        ExpenseType::factory()->count(3)->create();

        $response = $this->get(route('expense-types.index'));

        $response->assertStatus(200);
        $response->assertViewIs('expense-types.index');
        $response->assertViewHas('expenseTypes');
    }

    /** @test */
    public function it_can_store_a_new_expense_type()
    {
        $expenseTypeData = [
            'description' => 'Test Expense Type',
            'acronym' => 'TET'
        ];

        $response = $this->post(route('expense-types.store'), $expenseTypeData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Expense type created successfully!');
        
        $this->assertDatabaseHas('expense_types', [
            'description' => 'Test Expense Type',
            'acronym' => 'TET'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing_expense_type()
    {
        $response = $this->post(route('expense-types.store'), []);

        $response->assertSessionHasErrors(['description', 'acronym']);
    }

    /** @test */
    public function it_validates_unique_acronym_when_storing_expense_type()
    {
        ExpenseType::factory()->create(['acronym' => 'EXISTING']);

        $response = $this->post(route('expense-types.store'), [
            'description' => 'Test Expense Type',
            'acronym' => 'EXISTING'
        ]);

        $response->assertSessionHasErrors(['acronym']);
    }

    /** @test */
    public function it_validates_acronym_max_length()
    {
        $response = $this->post(route('expense-types.store'), [
            'description' => 'Test Expense Type',
            'acronym' => 'TOOLONGACRONYM' // More than 10 characters
        ]);

        $response->assertSessionHasErrors(['acronym']);
    }

    /** @test */
    public function it_can_show_edit_form_for_expense_type()
    {
        $expenseType = ExpenseType::factory()->create();

        $response = $this->get(route('expense-types.edit', $expenseType->id));

        $response->assertStatus(200);
        $response->assertViewIs('expense-types.index');
        $response->assertViewHas(['expenseTypes', 'expenseType']);
    }

    /** @test */
    public function it_can_update_an_expense_type()
    {
        $expenseType = ExpenseType::factory()->create();

        $updateData = [
            'description' => 'Updated Expense Type',
            'acronym' => 'UET'
        ];

        $response = $this->put(route('expense-types.update', $expenseType->id), $updateData);

        $response->assertRedirect(route('expense-types.index'));
        $response->assertSessionHas('success', 'Expense type updated successfully!');
        
        $this->assertDatabaseHas('expense_types', [
            'id' => $expenseType->id,
            'description' => 'Updated Expense Type',
            'acronym' => 'UET'
        ]);
    }

    /** @test */
    public function it_validates_unique_acronym_when_updating_expense_type_except_itself()
    {
        $expenseType1 = ExpenseType::factory()->create(['acronym' => 'ET1']);
        $expenseType2 = ExpenseType::factory()->create(['acronym' => 'ET2']);

        // Should fail - trying to use existing acronym
        $response = $this->put(route('expense-types.update', $expenseType2->id), [
            'description' => 'Updated Expense Type',
            'acronym' => 'ET1'
        ]);
        $response->assertSessionHasErrors(['acronym']);

        // Should pass - using same acronym for same expense type
        $response = $this->put(route('expense-types.update', $expenseType1->id), [
            'description' => 'Updated Expense Type',
            'acronym' => 'ET1'
        ]);
        $response->assertSessionDoesntHaveErrors(['acronym']);
    }

    /** @test */
    public function it_can_delete_an_expense_type()
    {
        $expenseType = ExpenseType::factory()->create();

        $response = $this->delete(route('expense-types.destroy', $expenseType->id));

        $response->assertRedirect(route('expense-types.index'));
        $response->assertSessionHas('success', 'Expense type deleted successfully!');
        
        $this->assertDatabaseMissing('expense_types', [
            'id' => $expenseType->id
        ]);
    }

    /** @test */
    public function it_returns_json_response_when_requested()
    {
        ExpenseType::factory()->count(2)->create();

        $response = $this->getJson(route('expense-types.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'description', 'acronym']
            ]
        ]);
    }

    /** @test */
    public function it_can_store_expense_type_via_json()
    {
        $expenseTypeData = [
            'description' => 'JSON Expense Type',
            'acronym' => 'JET'
        ];

        $response = $this->postJson(route('expense-types.store'), $expenseTypeData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => ['id', 'description', 'acronym']
        ]);
    }

    /** @test */
    public function it_can_update_expense_type_via_json()
    {
        $expenseType = ExpenseType::factory()->create();

        $updateData = [
            'description' => 'Updated JSON Expense Type',
            'acronym' => 'UJET'
        ];

        $response = $this->putJson(route('expense-types.update', $expenseType->id), $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['id', 'description', 'acronym']
        ]);
    }

    /** @test */
    public function it_can_delete_expense_type_via_json()
    {
        $expenseType = ExpenseType::factory()->create();

        $response = $this->deleteJson(route('expense-types.destroy', $expenseType->id));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Expense type deleted successfully'
        ]);
    }
}