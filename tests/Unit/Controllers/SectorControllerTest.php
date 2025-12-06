<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Models\Sector;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SectorControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user for authentication
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_display_sectors_index()
    {
        // Create test data
        Sector::factory()->count(3)->create();

        $response = $this->get(route('sectors.index'));

        $response->assertStatus(200);
        $response->assertViewIs('sectors.index');
        $response->assertViewHas('sectors');
    }

    /** @test */
    public function it_can_store_a_new_sector()
    {
        $sectorData = [
            'name' => 'Test Sector',
        ];

        $response = $this->post(route('sectors.store'), $sectorData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Sector created successfully!');
        
        $this->assertDatabaseHas('sectors', [
            'name' => 'Test Sector',
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing_sector()
    {
        $response = $this->post(route('sectors.store'), []);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function it_validates_unique_name_when_storing_sector()
    {
        $existingSector = Sector::factory()->create(['name' => 'Existing Sector']);

        $response = $this->post(route('sectors.store'), [
            'name' => 'Existing Sector',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function it_validates_max_length_for_name()
    {
        $response = $this->post(route('sectors.store'), [
            'name' => str_repeat('a', 256), // 256 characters, exceeds max of 255
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function it_can_show_edit_form_for_sector()
    {
        $sector = Sector::factory()->create();

        $response = $this->get(route('sectors.edit', $sector->id));

        $response->assertRedirect(route('sectors.index'));
        $response->assertSessionHas('editSector');
    }

    /** @test */
    public function it_can_update_a_sector()
    {
        $sector = Sector::factory()->create(['name' => 'Original Name']);

        $updateData = [
            'name' => 'Updated Sector Name',
        ];

        $response = $this->put(route('sectors.update', $sector->id), $updateData);

        $response->assertRedirect(route('sectors.index'));
        $response->assertSessionHas('success', 'Sector updated successfully!');
        
        $this->assertDatabaseHas('sectors', [
            'id' => $sector->id,
            'name' => 'Updated Sector Name',
        ]);
    }

    /** @test */
    public function it_validates_unique_name_when_updating_sector_except_itself()
    {
        $sector1 = Sector::factory()->create(['name' => 'Sector One']);
        $sector2 = Sector::factory()->create(['name' => 'Sector Two']);

        // Should fail - trying to use existing name
        $response = $this->put(route('sectors.update', $sector2->id), [
            'name' => 'Sector One',
        ]);
        $response->assertSessionHasErrors(['name']);

        // Should pass - using same name for same sector
        $response = $this->put(route('sectors.update', $sector1->id), [
            'name' => 'Sector One',
        ]);
        $response->assertSessionDoesntHaveErrors(['name']);
    }

    /** @test */
    public function it_can_delete_a_sector_without_departments()
    {
        $sector = Sector::factory()->create();

        $response = $this->delete(route('sectors.destroy', $sector->id));

        $response->assertRedirect(route('sectors.index'));
        $response->assertSessionHas('success', 'Sector deleted successfully!');
        
        $this->assertDatabaseMissing('sectors', [
            'id' => $sector->id
        ]);
    }

    /** @test */
    public function it_cannot_delete_a_sector_with_departments()
    {
        $sector = Sector::factory()->create();
        Department::factory()->create(['sector_id' => $sector->id]);

        $response = $this->delete(route('sectors.destroy', $sector->id));

        $response->assertRedirect(route('sectors.index'));
        $response->assertSessionHas('error', 'Cannot delete sector that has departments assigned to it.');
        
        // Sector should still exist
        $this->assertDatabaseHas('sectors', [
            'id' => $sector->id
        ]);
    }

    /** @test */
    public function it_returns_json_response_when_requested()
    {
        Sector::factory()->count(2)->create();

        $response = $this->getJson(route('sectors.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    /** @test */
    public function it_can_store_sector_via_json()
    {
        $sectorData = [
            'name' => 'JSON Sector',
        ];

        $response = $this->postJson(route('sectors.store'), $sectorData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'sector' => ['id', 'name']
        ]);
        
        $this->assertDatabaseHas('sectors', [
            'name' => 'JSON Sector',
        ]);
    }

    /** @test */
    public function it_can_update_sector_via_json()
    {
        $sector = Sector::factory()->create(['name' => 'Original']);

        $updateData = [
            'name' => 'Updated via JSON',
        ];

        $response = $this->putJson(route('sectors.update', $sector->id), $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'sector' => ['id', 'name']
        ]);
        
        $this->assertDatabaseHas('sectors', [
            'id' => $sector->id,
            'name' => 'Updated via JSON',
        ]);
    }

    /** @test */
    public function it_can_delete_sector_via_json()
    {
        $sector = Sector::factory()->create();

        $response = $this->deleteJson(route('sectors.destroy', $sector->id));

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        
        $this->assertDatabaseMissing('sectors', [
            'id' => $sector->id
        ]);
    }

    /** @test */
    public function it_cannot_delete_sector_with_departments_via_json()
    {
        $sector = Sector::factory()->create();
        Department::factory()->create(['sector_id' => $sector->id]);

        $response = $this->deleteJson(route('sectors.destroy', $sector->id));

        $response->assertStatus(422);
        $response->assertJsonStructure(['message']);
        
        // Sector should still exist
        $this->assertDatabaseHas('sectors', [
            'id' => $sector->id
        ]);
    }

    /** @test */
    public function it_returns_404_when_sector_not_found()
    {
        $response = $this->get(route('sectors.edit', 999999));

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_when_updating_nonexistent_sector()
    {
        $response = $this->put(route('sectors.update', 999999), [
            'name' => 'Test',
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_sector()
    {
        $response = $this->delete(route('sectors.destroy', 999999));

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_sectors_with_department_count()
    {
        $sector1 = Sector::factory()->create(['name' => 'Alpha Sector']);
        $sector2 = Sector::factory()->create(['name' => 'Beta Sector']);
        
        Department::factory()->count(3)->create(['sector_id' => $sector1->id]);
        Department::factory()->count(2)->create(['sector_id' => $sector2->id]);

        $response = $this->getJson(route('sectors.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        
        // Check that department counts are included (ordered by name)
        $data = $response->json();
        $this->assertEquals('Alpha Sector', $data[0]['name']);
        $this->assertEquals(3, $data[0]['departments_count']);
        $this->assertEquals('Beta Sector', $data[1]['name']);
        $this->assertEquals(2, $data[1]['departments_count']);
    }

    /** @test */
    public function it_returns_sectors_with_aip_codes_count()
    {
        $sector = Sector::factory()->create();
        
        // Create AIP codes for the sector
        \App\Models\SectorAipCode::factory()->count(5)->create(['sector_id' => $sector->id]);

        $response = $this->getJson(route('sectors.index'));

        $response->assertStatus(200);
        
        // Check that aip_codes count is included
        $data = $response->json();
        $this->assertEquals(5, $data[0]['aip_codes_count']);
    }

    /** @test */
    public function it_orders_sectors_by_name()
    {
        Sector::factory()->create(['name' => 'Zebra Sector']);
        Sector::factory()->create(['name' => 'Alpha Sector']);
        Sector::factory()->create(['name' => 'Beta Sector']);

        $response = $this->getJson(route('sectors.index'));

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertEquals('Alpha Sector', $data[0]['name']);
        $this->assertEquals('Beta Sector', $data[1]['name']);
        $this->assertEquals('Zebra Sector', $data[2]['name']);
    }

    /** @test */
    public function it_can_get_sector_via_json()
    {
        $sector = Sector::factory()->create(['name' => 'Test Sector']);

        $response = $this->getJson(route('sectors.edit', $sector->id));

        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'name']);
        $response->assertJson([
            'id' => $sector->id,
            'name' => 'Test Sector'
        ]);
    }
}
