<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Models\FundType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class FundTypeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_display_fund_types_index()
    {
        FundType::factory()->count(3)->create();

        $response = $this->get(route('fund-types.index'));

        $response->assertStatus(200);
        $response->assertViewIs('fund-types.index');
        $response->assertViewHas('fundTypes');
    }

    /** @test */
    public function it_can_store_a_new_fund_type()
    {
        $fundTypeData = [
            'description' => 'Test Fund Type'
        ];

        $response = $this->post(route('fund-types.store'), $fundTypeData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Fund type created successfully!');
        
        $this->assertDatabaseHas('fund_types', [
            'description' => 'Test Fund Type'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing_fund_type()
    {
        $response = $this->post(route('fund-types.store'), []);

        $response->assertSessionHasErrors(['description']);
    }

    /** @test */
    public function it_can_show_edit_form_for_fund_type()
    {
        $fundType = FundType::factory()->create();

        $response = $this->get(route('fund-types.edit', $fundType->id));

        $response->assertStatus(200);
        $response->assertViewIs('fund-types.index');
        $response->assertViewHas(['fundTypes', 'fundType']);
    }

    /** @test */
    public function it_can_update_a_fund_type()
    {
        $fundType = FundType::factory()->create();

        $updateData = [
            'description' => 'Updated Fund Type'
        ];

        $response = $this->put(route('fund-types.update', $fundType->id), $updateData);

        $response->assertRedirect(route('fund-types.index'));
        $response->assertSessionHas('success', 'Fund type updated successfully!');
        
        $this->assertDatabaseHas('fund_types', [
            'id' => $fundType->id,
            'description' => 'Updated Fund Type'
        ]);
    }

    /** @test */
    public function it_can_delete_a_fund_type()
    {
        $fundType = FundType::factory()->create();

        $response = $this->delete(route('fund-types.destroy', $fundType->id));

        $response->assertRedirect(route('fund-types.index'));
        $response->assertSessionHas('success', 'Fund type deleted successfully!');
        
        $this->assertDatabaseMissing('fund_types', [
            'id' => $fundType->id
        ]);
    }

    /** @test */
    public function it_returns_json_response_when_requested()
    {
        FundType::factory()->count(2)->create();

        $response = $this->getJson(route('fund-types.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'description']
            ]
        ]);
    }

    /** @test */
    public function it_can_store_fund_type_via_json()
    {
        $fundTypeData = [
            'description' => 'JSON Fund Type'
        ];

        $response = $this->postJson(route('fund-types.store'), $fundTypeData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => ['id', 'description']
        ]);
    }

    /** @test */
    public function it_can_update_fund_type_via_json()
    {
        $fundType = FundType::factory()->create();

        $updateData = [
            'description' => 'Updated JSON Fund Type'
        ];

        $response = $this->putJson(route('fund-types.update', $fundType->id), $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['id', 'description']
        ]);
    }

    /** @test */
    public function it_can_delete_fund_type_via_json()
    {
        $fundType = FundType::factory()->create();

        $response = $this->deleteJson(route('fund-types.destroy', $fundType->id));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Fund type deleted successfully'
        ]);
    }
}