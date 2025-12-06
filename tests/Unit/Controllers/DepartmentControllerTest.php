<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Models\Department;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class DepartmentControllerTest extends TestCase
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
    public function it_can_display_departments_index()
    {
        // Create test data
        $sector = Sector::factory()->create();
        Department::factory()->count(3)->create(['sector_id' => $sector->id]);

        $response = $this->get(route('departments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('departments.index');
        $response->assertViewHas(['departments', 'sectors']);
    }

    /** @test */
    public function it_can_store_a_new_department()
    {
        $sector = Sector::factory()->create();
        
        $departmentData = [
            'code' => 'TEST',
            'name' => 'Test Department',
            'sector_id' => $sector->id
        ];

        $response = $this->post(route('departments.store'), $departmentData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Department created successfully!');
        
        $this->assertDatabaseHas('departments', [
            'code' => 'TEST',
            'name' => 'Test Department',
            'sector_id' => $sector->id
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing_department()
    {
        $response = $this->post(route('departments.store'), []);

        $response->assertSessionHasErrors(['code', 'name']);
    }

    /** @test */
    public function it_validates_unique_code_when_storing_department()
    {
        $existingDepartment = Department::factory()->create(['code' => 'EXISTING']);

        $response = $this->post(route('departments.store'), [
            'code' => 'EXISTING',
            'name' => 'Test Department'
        ]);

        $response->assertSessionHasErrors(['code']);
    }

    /** @test */
    public function it_can_show_edit_form_for_department()
    {
        $department = Department::factory()->create();

        $response = $this->get(route('departments.edit', $department->id));

        $response->assertStatus(200);
        $response->assertViewIs('departments.index');
        $response->assertViewHas(['departments', 'sectors', 'department']);
    }

    /** @test */
    public function it_can_update_a_department()
    {
        $sector = Sector::factory()->create();
        $department = Department::factory()->create();

        $updateData = [
            'code' => 'UPDATED',
            'name' => 'Updated Department',
            'sector_id' => $sector->id
        ];

        $response = $this->put(route('departments.update', $department->id), $updateData);

        $response->assertRedirect(route('departments.index'));
        $response->assertSessionHas('success', 'Department updated successfully!');
        
        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'code' => 'UPDATED',
            'name' => 'Updated Department',
            'sector_id' => $sector->id
        ]);
    }

    /** @test */
    public function it_validates_unique_code_when_updating_department_except_itself()
    {
        $department1 = Department::factory()->create(['code' => 'DEPT1']);
        $department2 = Department::factory()->create(['code' => 'DEPT2']);

        // Should fail - trying to use existing code
        $response = $this->put(route('departments.update', $department2->id), [
            'code' => 'DEPT1',
            'name' => 'Updated Department'
        ]);
        $response->assertSessionHasErrors(['code']);

        // Should pass - using same code for same department
        $response = $this->put(route('departments.update', $department1->id), [
            'code' => 'DEPT1',
            'name' => 'Updated Department'
        ]);
        $response->assertSessionDoesntHaveErrors(['code']);
    }

    /** @test */
    public function it_can_delete_a_department()
    {
        $department = Department::factory()->create();

        $response = $this->delete(route('departments.destroy', $department->id));

        $response->assertRedirect(route('departments.index'));
        $response->assertSessionHas('success', 'Department deleted successfully!');
        
        $this->assertDatabaseMissing('departments', [
            'id' => $department->id
        ]);
    }

    /** @test */
    public function it_returns_json_response_when_requested()
    {
        Department::factory()->count(2)->create();

        $response = $this->getJson(route('departments.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'code', 'name']
            ]
        ]);
    }

    /** @test */
    public function it_can_store_department_via_json()
    {
        $sector = Sector::factory()->create();
        
        $departmentData = [
            'code' => 'JSON',
            'name' => 'JSON Department',
            'sector_id' => $sector->id
        ];

        $response = $this->postJson(route('departments.store'), $departmentData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => ['id', 'code', 'name']
        ]);
    }
}