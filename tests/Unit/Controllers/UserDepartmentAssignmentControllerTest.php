<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use App\Models\UserDepartmentAssignment;
use App\Models\Sector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class UserDepartmentAssignmentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $department;
    protected $role;
    protected $sector;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a sector first (required for department)
        $this->sector = Sector::factory()->create();
        
        // Create test data
        $this->role = Role::factory()->create();
        $this->user = User::factory()->create(['role_id' => $this->role->id]);
        $this->department = Department::factory()->create(['sector_id' => $this->sector->id]);
    }

    public function test_index_displays_user_department_assignments()
    {
        // Create some user department assignments
        $userDepartmentAssignment1 = UserDepartmentAssignment::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id
        ]);
        
        $user2 = User::factory()->create(['role_id' => $this->role->id]);
        $userDepartmentAssignment2 = UserDepartmentAssignment::factory()->create([
            'user_id' => $user2->id,
            'department_id' => $this->department->id
        ]);

        $response = $this->get(route('user-department-assignments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('user-department-assignments.index');
        $response->assertViewHas('userDepartmentAssignments');
        $response->assertViewHas('users');
        $response->assertViewHas('departments');
        $response->assertSee($this->user->name);
        $response->assertSee($user2->name);
    }

    public function test_index_returns_json_when_requested()
    {
        $userDepartmentAssignment = UserDepartmentAssignment::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id
        ]);

        $response = $this->getJson(route('user-department-assignments.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_store_creates_new_user_department_assignment()
    {
        $data = [
            'user_id' => $this->user->id,
            'department_ids' => [$this->department->id],
            'is_active' => true,
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'notes' => 'Test assignment'
        ];

        $response = $this->post(route('user-department-assignments.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('user_department_assignments', [
            'user_id' => $this->user->id,
            'department_id' => $this->department->id,
            'is_active' => true,
            'notes' => 'Test assignment'
        ]);
    }

    public function test_store_returns_json_when_requested()
    {
        $data = [
            'user_id' => $this->user->id,
            'department_ids' => [$this->department->id],
            'is_active' => true
        ];

        $response = $this->postJson(route('user-department-assignments.store'), $data);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data']);
    }

    public function test_store_prevents_duplicate_assignments()
    {
        // Create existing assignment
        UserDepartmentAssignment::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id
        ]);

        $data = [
            'user_id' => $this->user->id,
            'department_ids' => [$this->department->id],
            'is_active' => true
        ];

        $response = $this->post(route('user-department-assignments.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['error']);
    }

    public function test_store_prevents_duplicate_assignments_json()
    {
        // Create existing assignment
        UserDepartmentAssignment::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id
        ]);

        $data = [
            'user_id' => $this->user->id,
            'department_ids' => [$this->department->id],
            'is_active' => true
        ];

        $response = $this->postJson(route('user-department-assignments.store'), $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['error']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('user-department-assignments.store'), []);

        $response->assertSessionHasErrors(['user_id', 'department_ids']);
    }

    public function test_store_validates_foreign_keys()
    {
        $data = [
            'user_id' => 999999,
            'department_ids' => [999999],
        ];

        $response = $this->post(route('user-department-assignments.store'), $data);

        $response->assertSessionHasErrors(['user_id', 'department_ids.0']);
    }

    public function test_store_validates_date_fields()
    {
        $data = [
            'user_id' => $this->user->id,
            'department_ids' => [$this->department->id],
            'start_date' => 'invalid-date',
            'end_date' => '2024-01-01', // Before start date
        ];

        $response = $this->post(route('user-department-assignments.store'), $data);

        $response->assertSessionHasErrors(['start_date']);
    }

    public function test_store_validates_end_date_after_start_date()
    {
        $data = [
            'user_id' => $this->user->id,
            'department_ids' => [$this->department->id],
            'start_date' => '2024-12-31',
            'end_date' => '2024-01-01', // Before start date
        ];

        $response = $this->post(route('user-department-assignments.store'), $data);

        $response->assertSessionHasErrors(['end_date']);
    }

    public function test_store_creates_multiple_department_assignments()
    {
        $department2 = Department::factory()->create(['sector_id' => $this->sector->id]);
        $department3 = Department::factory()->create(['sector_id' => $this->sector->id]);

        $data = [
            'user_id' => $this->user->id,
            'department_ids' => [$this->department->id, $department2->id, $department3->id],
            'is_active' => true,
            'notes' => 'Multiple assignments'
        ];

        $response = $this->post(route('user-department-assignments.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Check that all three assignments were created
        $this->assertDatabaseHas('user_department_assignments', [
            'user_id' => $this->user->id,
            'department_id' => $this->department->id,
            'notes' => 'Multiple assignments'
        ]);
        
        $this->assertDatabaseHas('user_department_assignments', [
            'user_id' => $this->user->id,
            'department_id' => $department2->id,
            'notes' => 'Multiple assignments'
        ]);
        
        $this->assertDatabaseHas('user_department_assignments', [
            'user_id' => $this->user->id,
            'department_id' => $department3->id,
            'notes' => 'Multiple assignments'
        ]);
    }

    public function test_store_handles_partial_duplicates()
    {
        // Create existing assignment for one department
        UserDepartmentAssignment::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id
        ]);

        $department2 = Department::factory()->create(['sector_id' => $this->sector->id]);

        $data = [
            'user_id' => $this->user->id,
            'department_ids' => [$this->department->id, $department2->id], // One exists, one new
            'is_active' => true
        ];

        $response = $this->post(route('user-department-assignments.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Check that the new assignment was created
        $this->assertDatabaseHas('user_department_assignments', [
            'user_id' => $this->user->id,
            'department_id' => $department2->id
        ]);
        
        // Check that we only have 2 assignments total (1 existing + 1 new)
        $this->assertEquals(2, UserDepartmentAssignment::where('user_id', $this->user->id)->count());
    }

    public function test_edit_displays_user_department_assignment_for_editing()
    {
        $userDepartmentAssignment = UserDepartmentAssignment::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id
        ]);

        $response = $this->get(route('user-department-assignments.edit', $userDepartmentAssignment));

        $response->assertStatus(200);
        $response->assertViewIs('user-department-assignments.index');
        $response->assertViewHas('userDepartmentAssignment');
        $response->assertViewHas('users');
        $response->assertViewHas('departments');
    }

    public function test_update_modifies_existing_user_department_assignment()
    {
        $userDepartmentAssignment = UserDepartmentAssignment::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id,
            'notes' => 'Original notes'
        ]);

        $newDepartment = Department::factory()->create(['sector_id' => $this->sector->id]);
        
        $data = [
            'user_id' => $this->user->id,
            'department_id' => $newDepartment->id,
            'is_active' => false,
            'notes' => 'Updated notes'
        ];

        $response = $this->put(route('user-department-assignments.update', $userDepartmentAssignment), $data);

        $response->assertRedirect(route('user-department-assignments.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('user_department_assignments', [
            'id' => $userDepartmentAssignment->id,
            'department_id' => $newDepartment->id,
            'is_active' => false,
            'notes' => 'Updated notes'
        ]);
    }

    public function test_update_returns_json_when_requested()
    {
        $userDepartmentAssignment = UserDepartmentAssignment::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id
        ]);

        $data = [
            'user_id' => $this->user->id,
            'department_id' => $this->department->id,
            'is_active' => false
        ];

        $response = $this->putJson(route('user-department-assignments.update', $userDepartmentAssignment), $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_update_prevents_duplicate_assignments()
    {
        $userDepartmentAssignment1 = UserDepartmentAssignment::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id
        ]);

        $newDepartment = Department::factory()->create(['sector_id' => $this->sector->id]);
        $userDepartmentAssignment2 = UserDepartmentAssignment::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $newDepartment->id
        ]);

        // Try to update userDepartmentAssignment2 to have the same combination as userDepartmentAssignment1
        $data = [
            'user_id' => $this->user->id,
            'department_id' => $this->department->id, // This would create a duplicate
            'is_active' => true
        ];

        $response = $this->put(route('user-department-assignments.update', $userDepartmentAssignment2), $data);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['error']);
    }

    public function test_update_validates_required_fields()
    {
        $userDepartmentAssignment = UserDepartmentAssignment::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id
        ]);

        $response = $this->put(route('user-department-assignments.update', $userDepartmentAssignment), []);

        $response->assertSessionHasErrors(['user_id']);
    }

    public function test_update_with_multiple_departments_requires_exactly_one()
    {
        $userDepartmentAssignment = UserDepartmentAssignment::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id
        ]);

        $department2 = Department::factory()->create(['sector_id' => $this->sector->id]);

        $data = [
            'user_id' => $this->user->id,
            'department_ids' => [$this->department->id, $department2->id], // Multiple departments not allowed for update
            'is_active' => true
        ];

        $response = $this->put(route('user-department-assignments.update', $userDepartmentAssignment), $data);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['department_ids']);
    }

    public function test_destroy_deletes_user_department_assignment()
    {
        $userDepartmentAssignment = UserDepartmentAssignment::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id
        ]);

        $response = $this->delete(route('user-department-assignments.destroy', $userDepartmentAssignment));

        $response->assertRedirect(route('user-department-assignments.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('user_department_assignments', [
            'id' => $userDepartmentAssignment->id
        ]);
    }

    public function test_destroy_returns_json_when_requested()
    {
        $userDepartmentAssignment = UserDepartmentAssignment::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id
        ]);

        $response = $this->deleteJson(route('user-department-assignments.destroy', $userDepartmentAssignment));

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        
        $this->assertDatabaseMissing('user_department_assignments', [
            'id' => $userDepartmentAssignment->id
        ]);
    }

    public function test_destroy_returns_404_for_nonexistent_user_department_assignment()
    {
        $response = $this->delete(route('user-department-assignments.destroy', 999999));

        $response->assertStatus(404);
    }

    public function test_model_relationships_work_correctly()
    {
        $userDepartmentAssignment = UserDepartmentAssignment::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id
        ]);

        // Test relationships
        $this->assertEquals($this->user->id, $userDepartmentAssignment->user->id);
        $this->assertEquals($this->department->id, $userDepartmentAssignment->department->id);
    }

    public function test_model_scopes_work_correctly()
    {
        $activeAssignment = UserDepartmentAssignment::factory()->active()->create([
            'user_id' => $this->user->id,
            'department_id' => $this->department->id
        ]);

        $newDepartment = Department::factory()->create(['sector_id' => $this->sector->id]);
        $inactiveAssignment = UserDepartmentAssignment::factory()->inactive()->create([
            'user_id' => $this->user->id,
            'department_id' => $newDepartment->id
        ]);

        // Test active scope
        $activeAssignments = UserDepartmentAssignment::active()->get();
        $this->assertTrue($activeAssignments->contains($activeAssignment));
        $this->assertFalse($activeAssignments->contains($inactiveAssignment));

        // Test forUser scope
        $userAssignments = UserDepartmentAssignment::forUser($this->user->id)->get();
        $this->assertTrue($userAssignments->contains($activeAssignment));
        $this->assertTrue($userAssignments->contains($inactiveAssignment));

        // Test forDepartment scope
        $departmentAssignments = UserDepartmentAssignment::forDepartment($this->department->id)->get();
        $this->assertTrue($departmentAssignments->contains($activeAssignment));
        $this->assertFalse($departmentAssignments->contains($inactiveAssignment));
    }
}