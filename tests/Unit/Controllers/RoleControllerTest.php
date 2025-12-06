<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_display_roles_index()
    {
        Role::factory()->count(3)->create();

        $response = $this->get(route('roles.index'));

        $response->assertStatus(200);
        $response->assertViewIs('roles.index');
        $response->assertViewHas('roles');
    }

    /** @test */
    public function it_can_store_a_new_role()
    {
        $roleData = [
            'name' => 'Test Role',
            'description' => 'Test Role Description'
        ];

        $response = $this->post(route('roles.store'), $roleData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Role created successfully!');
        
        $this->assertDatabaseHas('roles', [
            'name' => 'Test Role',
            'description' => 'Test Role Description'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing_role()
    {
        $response = $this->post(route('roles.store'), []);

        $response->assertSessionHasErrors(['name', 'description']);
    }

    /** @test */
    public function it_validates_unique_name_when_storing_role()
    {
        Role::factory()->create(['name' => 'Existing Role']);

        $response = $this->post(route('roles.store'), [
            'name' => 'Existing Role',
            'description' => 'Test Description'
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function it_can_show_edit_form_for_role()
    {
        $role = Role::factory()->create();

        $response = $this->get(route('roles.edit', $role->id));

        $response->assertStatus(200);
        $response->assertViewIs('roles.index');
        $response->assertViewHas(['roles', 'role']);
    }

    /** @test */
    public function it_can_update_a_role()
    {
        $role = Role::factory()->create();

        $updateData = [
            'name' => 'Updated Role',
            'description' => 'Updated Role Description'
        ];

        $response = $this->put(route('roles.update', $role->id), $updateData);

        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('success', 'Role updated successfully!');
        
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Updated Role',
            'description' => 'Updated Role Description'
        ]);
    }

    /** @test */
    public function it_validates_unique_name_when_updating_role_except_itself()
    {
        $role1 = Role::factory()->create(['name' => 'Role 1']);
        $role2 = Role::factory()->create(['name' => 'Role 2']);

        // Should fail - trying to use existing name
        $response = $this->put(route('roles.update', $role2->id), [
            'name' => 'Role 1',
            'description' => 'Updated Description'
        ]);
        $response->assertSessionHasErrors(['name']);

        // Should pass - using same name for same role
        $response = $this->put(route('roles.update', $role1->id), [
            'name' => 'Role 1',
            'description' => 'Updated Description'
        ]);
        $response->assertSessionDoesntHaveErrors(['name']);
    }

    /** @test */
    public function it_can_delete_a_role()
    {
        $role = Role::factory()->create();

        $response = $this->delete(route('roles.destroy', $role->id));

        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('success', 'Role deleted successfully!');
        
        $this->assertDatabaseMissing('roles', [
            'id' => $role->id
        ]);
    }

    /** @test */
    public function it_returns_json_response_when_requested()
    {
        Role::factory()->count(2)->create();

        $response = $this->getJson(route('roles.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'description']
            ]
        ]);
    }

    /** @test */
    public function it_can_store_role_via_json()
    {
        $roleData = [
            'name' => 'JSON Role',
            'description' => 'JSON Role Description'
        ];

        $response = $this->postJson(route('roles.store'), $roleData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'description']
        ]);
    }

    /** @test */
    public function it_can_update_role_via_json()
    {
        $role = Role::factory()->create();

        $updateData = [
            'name' => 'Updated JSON Role',
            'description' => 'Updated JSON Description'
        ];

        $response = $this->putJson(route('roles.update', $role->id), $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'description']
        ]);
    }

    /** @test */
    public function it_can_delete_role_via_json()
    {
        $role = Role::factory()->create();

        $response = $this->deleteJson(route('roles.destroy', $role->id));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Role deleted successfully'
        ]);
    }

    /** @test */
    public function it_shows_users_count_for_roles()
    {
        $role = Role::factory()->create();
        User::factory()->count(3)->create(['role_id' => $role->id]);

        $response = $this->get(route('roles.index'));

        $response->assertStatus(200);
        // The view should show the users count for the role
        $response->assertSee('3 users');
    }
}