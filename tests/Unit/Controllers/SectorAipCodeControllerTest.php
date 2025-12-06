<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Models\Sector;
use App\Models\SectorAipCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SectorAipCodeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $sector;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user for authentication (without middleware for testing)
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        
        // Bypass permission middleware for testing
        $this->withoutMiddleware(\App\Http\Middleware\CheckPermission::class);

        // Create a sector for testing
        $this->sector = Sector::factory()->create(['name' => 'Test Sector']);
    }

    /** @test */
    public function it_can_display_aip_codes_index_for_a_sector()
    {
        // Create test AIP codes
        SectorAipCode::factory()->count(3)->create(['sector_id' => $this->sector->id]);

        $response = $this->get(route('sector-aip-codes.index', $this->sector->id));

        $response->assertStatus(200);
        $response->assertViewIs('sector-aip-codes.index');
        $response->assertViewHas(['sector', 'aipCodes']);
        $response->assertSee($this->sector->name);
    }

    /** @test */
    public function it_can_store_a_new_aip_code()
    {
        $aipCodeData = [
            'code' => '111-A20011',
            'description' => 'Test AIP Code',
            'is_active' => true
        ];

        $response = $this->post(route('sector-aip-codes.store', $this->sector->id), $aipCodeData);

        $response->assertRedirect(route('sector-aip-codes.index', $this->sector->id));
        $response->assertSessionHas('success', 'AIP code created successfully!');
        
        $this->assertDatabaseHas('sector_aip_codes', [
            'sector_id' => $this->sector->id,
            'code' => '111-A20011',
            'description' => 'Test AIP Code',
            'is_active' => true
        ]);
    }

    /** @test */
    public function it_stores_aip_code_as_inactive_when_checkbox_not_checked()
    {
        $aipCodeData = [
            'code' => '222-B30022',
            'description' => 'Inactive AIP Code'
            // is_active not provided (checkbox unchecked)
        ];

        $response = $this->post(route('sector-aip-codes.store', $this->sector->id), $aipCodeData);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('sector_aip_codes', [
            'sector_id' => $this->sector->id,
            'code' => '222-B30022',
            'is_active' => false
        ]);
    }

    /** @test */
    public function it_validates_required_code_when_storing_aip_code()
    {
        $response = $this->post(route('sector-aip-codes.store', $this->sector->id), [
            'description' => 'Missing code'
        ]);

        $response->assertSessionHasErrors(['code']);
    }

    /** @test */
    public function it_validates_unique_code_when_storing_aip_code()
    {
        SectorAipCode::factory()->create([
            'sector_id' => $this->sector->id,
            'code' => 'EXISTING-CODE'
        ]);

        $response = $this->post(route('sector-aip-codes.store', $this->sector->id), [
            'code' => 'EXISTING-CODE',
            'description' => 'Duplicate code'
        ]);

        $response->assertSessionHasErrors(['code']);
    }

    /** @test */
    public function it_can_update_an_aip_code()
    {
        $aipCode = SectorAipCode::factory()->create([
            'sector_id' => $this->sector->id,
            'code' => 'OLD-CODE',
            'description' => 'Old Description',
            'is_active' => false
        ]);

        $updateData = [
            'code' => 'NEW-CODE',
            'description' => 'New Description',
            'is_active' => true
        ];

        $response = $this->put(
            route('sector-aip-codes.update', [$this->sector->id, $aipCode->id]), 
            $updateData
        );

        $response->assertRedirect(route('sector-aip-codes.index', $this->sector->id));
        $response->assertSessionHas('success', 'AIP code updated successfully!');
        
        $this->assertDatabaseHas('sector_aip_codes', [
            'id' => $aipCode->id,
            'code' => 'NEW-CODE',
            'description' => 'New Description',
            'is_active' => true
        ]);
    }

    /** @test */
    public function it_validates_unique_code_when_updating_except_itself()
    {
        $aipCode1 = SectorAipCode::factory()->create([
            'sector_id' => $this->sector->id,
            'code' => 'CODE-1'
        ]);
        
        $aipCode2 = SectorAipCode::factory()->create([
            'sector_id' => $this->sector->id,
            'code' => 'CODE-2'
        ]);

        // Should fail - trying to use existing code
        $response = $this->put(
            route('sector-aip-codes.update', [$this->sector->id, $aipCode2->id]),
            ['code' => 'CODE-1', 'description' => 'Test']
        );
        $response->assertSessionHasErrors(['code']);

        // Should pass - using same code for same AIP code
        $response = $this->put(
            route('sector-aip-codes.update', [$this->sector->id, $aipCode1->id]),
            ['code' => 'CODE-1', 'description' => 'Updated']
        );
        $response->assertSessionDoesntHaveErrors(['code']);
    }

    /** @test */
    public function it_can_toggle_aip_code_status()
    {
        $aipCode = SectorAipCode::factory()->create([
            'sector_id' => $this->sector->id,
            'is_active' => true
        ]);

        $response = $this->put(
            route('sector-aip-codes.toggle', [$this->sector->id, $aipCode->id])
        );

        $response->assertRedirect(route('sector-aip-codes.index', $this->sector->id));
        $response->assertSessionHas('success', 'AIP code status updated successfully!');
        
        $this->assertDatabaseHas('sector_aip_codes', [
            'id' => $aipCode->id,
            'is_active' => false
        ]);

        // Toggle again
        $response = $this->put(
            route('sector-aip-codes.toggle', [$this->sector->id, $aipCode->id])
        );

        $this->assertDatabaseHas('sector_aip_codes', [
            'id' => $aipCode->id,
            'is_active' => true
        ]);
    }

    /** @test */
    public function it_can_delete_an_aip_code()
    {
        $aipCode = SectorAipCode::factory()->create([
            'sector_id' => $this->sector->id
        ]);

        $response = $this->delete(
            route('sector-aip-codes.destroy', [$this->sector->id, $aipCode->id])
        );

        $response->assertRedirect(route('sector-aip-codes.index', $this->sector->id));
        $response->assertSessionHas('success', 'AIP code deleted successfully!');
        
        $this->assertDatabaseMissing('sector_aip_codes', [
            'id' => $aipCode->id
        ]);
    }

    /** @test */
    public function it_only_shows_aip_codes_for_the_specified_sector()
    {
        $otherSector = Sector::factory()->create(['name' => 'Other Sector']);
        
        // Create AIP codes for both sectors
        $aipCode1 = SectorAipCode::factory()->create([
            'sector_id' => $this->sector->id,
            'code' => 'SECTOR1-CODE'
        ]);
        
        $aipCode2 = SectorAipCode::factory()->create([
            'sector_id' => $otherSector->id,
            'code' => 'SECTOR2-CODE'
        ]);

        $response = $this->get(route('sector-aip-codes.index', $this->sector->id));

        $response->assertSee('SECTOR1-CODE');
        $response->assertDontSee('SECTOR2-CODE');
    }

    /** @test */
    public function it_returns_404_when_sector_not_found()
    {
        $response = $this->get(route('sector-aip-codes.index', 99999));

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_when_aip_code_not_found()
    {
        $response = $this->delete(
            route('sector-aip-codes.destroy', [$this->sector->id, 99999])
        );

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_when_aip_code_belongs_to_different_sector()
    {
        $otherSector = Sector::factory()->create();
        $aipCode = SectorAipCode::factory()->create([
            'sector_id' => $otherSector->id
        ]);

        $response = $this->delete(
            route('sector-aip-codes.destroy', [$this->sector->id, $aipCode->id])
        );

        $response->assertStatus(404);
    }

    /** @test */
    public function it_displays_empty_state_when_no_aip_codes_exist()
    {
        $response = $this->get(route('sector-aip-codes.index', $this->sector->id));

        $response->assertStatus(200);
        $response->assertSee('No AIP codes found');
        $response->assertSee('Get started by creating your first AIP code');
    }

    /** @test */
    public function it_can_store_aip_code_without_description()
    {
        $aipCodeData = [
            'code' => '333-C40033',
            'is_active' => true
        ];

        $response = $this->post(route('sector-aip-codes.store', $this->sector->id), $aipCodeData);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('sector_aip_codes', [
            'sector_id' => $this->sector->id,
            'code' => '333-C40033',
            'description' => null
        ]);
    }

    /** @test */
    public function it_validates_max_length_for_code()
    {
        $response = $this->post(route('sector-aip-codes.store', $this->sector->id), [
            'code' => str_repeat('A', 51), // 51 characters (max is 50)
            'description' => 'Test'
        ]);

        $response->assertSessionHasErrors(['code']);
    }

    /** @test */
    public function it_validates_max_length_for_description()
    {
        $response = $this->post(route('sector-aip-codes.store', $this->sector->id), [
            'code' => 'TEST-CODE',
            'description' => str_repeat('A', 256) // 256 characters (max is 255)
        ]);

        $response->assertSessionHasErrors(['description']);
    }

    /** @test */
    public function it_displays_active_and_inactive_badges_correctly()
    {
        $activeCode = SectorAipCode::factory()->create([
            'sector_id' => $this->sector->id,
            'code' => 'ACTIVE-CODE',
            'is_active' => true
        ]);

        $inactiveCode = SectorAipCode::factory()->create([
            'sector_id' => $this->sector->id,
            'code' => 'INACTIVE-CODE',
            'is_active' => false
        ]);

        $response = $this->get(route('sector-aip-codes.index', $this->sector->id));

        $response->assertSee('ACTIVE-CODE');
        $response->assertSee('INACTIVE-CODE');
        $response->assertSee('Active');
        $response->assertSee('Inactive');
    }
}
