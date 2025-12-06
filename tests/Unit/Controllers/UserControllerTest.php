<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_display_users_index()
    {
        User::factory()->count(3)->create();

        $response = $this->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('users.index');
        $response->assertViewHas(['users', 'roles', 'departments']);
    }

    /** @test */
    public function it_can_store_a_new_user()
    {
        $role = Role::factory()->create();
        $department = Department::factory()->create();
        
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $role->id,
            'department_id' => $department->id
        ];

        $response = $this->post(route('users.store'), $userData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'User created successfully!');
        
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role_id' => $role->id,
            'department_id' => $department->id
        ]);

        // Verify password is hashed
        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /** @test */
    public function it_validates_required_fields_when_storing_user()
    {
        $response = $this->post(route('users.store'), []);

        $response->assertSessionHasErrors(['name', 'email']);
        // Password is no longer required as it has a default value
    }

    /** @test */
    public function it_uses_default_password_when_none_provided()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            // No password provided
        ];

        $response = $this->post(route('users.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        
        // Verify the default password works
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('123456', $user->password));
    }

    /** @test */
    public function it_can_reset_user_password_to_default()
    {
        $user = User::factory()->create([
            'password' => \Illuminate\Support\Facades\Hash::make('oldpassword')
        ]);

        $response = $this->post(route('users.reset-password', $user->id));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');
        
        // Refresh user from database
        $user->refresh();
        
        // Verify password was reset to default
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('123456', $user->password));
    }

    /** @test */
    public function it_can_reset_user_password_via_json()
    {
        $user = User::factory()->create([
            'password' => \Illuminate\Support\Facades\Hash::make('oldpassword')
        ]);

        $response = $this->postJson(route('users.reset-password', $user->id));

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        
        // Refresh user from database
        $user->refresh();
        
        // Verify password was reset to default
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('123456', $user->password));
    }

    /** @test */
    public function it_returns_404_when_resetting_password_for_nonexistent_user()
    {
        $response = $this->post(route('users.reset-password', 999999));

        $response->assertStatus(404);
    }

    /** @test */
    public function it_validates_unique_email_when_storing_user()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post(route('users.store'), [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function it_validates_password_confirmation_when_storing_user()
    {
        $response = $this->post(route('users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password'
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function it_validates_role_exists_when_provided()
    {
        $response = $this->post(route('users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => 999 // Non-existent role
        ]);

        $response->assertSessionHasErrors(['role_id']);
    }

    /** @test */
    public function it_validates_department_exists_when_provided()
    {
        $response = $this->post(route('users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'department_id' => 999 // Non-existent department
        ]);

        $response->assertSessionHasErrors(['department_id']);
    }

    /** @test */
    public function it_can_show_edit_form_for_user()
    {
        $testUser = User::factory()->create();

        $response = $this->get(route('users.edit', $testUser->id));

        $response->assertStatus(200);
        $response->assertViewIs('users.index');
        $response->assertViewHas(['users', 'roles', 'departments', 'user']);
    }

    /** @test */
    public function it_can_update_a_user()
    {
        $role = Role::factory()->create();
        $department = Department::factory()->create();
        $testUser = User::factory()->create();

        $updateData = [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'role_id' => $role->id,
            'department_id' => $department->id
        ];

        $response = $this->put(route('users.update', $testUser->id), $updateData);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success', 'User updated successfully!');
        
        $this->assertDatabaseHas('users', [
            'id' => $testUser->id,
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'role_id' => $role->id,
            'department_id' => $department->id
        ]);
    }

    /** @test */
    public function it_can_update_user_password_when_provided()
    {
        $testUser = User::factory()->create();
        $oldPassword = $testUser->password;

        $updateData = [
            'name' => $testUser->name,
            'email' => $testUser->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ];

        $response = $this->put(route('users.update', $testUser->id), $updateData);

        $response->assertRedirect(route('users.index'));
        
        $testUser->refresh();
        $this->assertNotEquals($oldPassword, $testUser->password);
        $this->assertTrue(Hash::check('newpassword123', $testUser->password));
    }

    /** @test */
    public function it_does_not_update_password_when_not_provided()
    {
        $testUser = User::factory()->create();
        $oldPassword = $testUser->password;

        $updateData = [
            'name' => 'Updated Name',
            'email' => $testUser->email
        ];

        $response = $this->put(route('users.update', $testUser->id), $updateData);

        $response->assertRedirect(route('users.index'));
        
        $testUser->refresh();
        $this->assertEquals($oldPassword, $testUser->password);
        $this->assertEquals('Updated Name', $testUser->name);
    }

    /** @test */
    public function it_validates_unique_email_when_updating_user_except_itself()
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);

        // Should fail - trying to use existing email
        $response = $this->put(route('users.update', $user2->id), [
            'name' => 'Updated User',
            'email' => 'user1@example.com'
        ]);
        $response->assertSessionHasErrors(['email']);

        // Should pass - using same email for same user
        $response = $this->put(route('users.update', $user1->id), [
            'name' => 'Updated User',
            'email' => 'user1@example.com'
        ]);
        $response->assertSessionDoesntHaveErrors(['email']);
    }

    /** @test */
    public function it_can_delete_a_user()
    {
        $testUser = User::factory()->create();

        $response = $this->delete(route('users.destroy', $testUser->id));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success', 'User deleted successfully!');
        
        $this->assertDatabaseMissing('users', [
            'id' => $testUser->id
        ]);
    }

    /** @test */
    public function it_returns_json_response_when_requested()
    {
        User::factory()->count(2)->create();

        $response = $this->getJson(route('users.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'name', 'email', 'role_id', 'department_id']
        ]);
    }

    /** @test */
    public function it_can_store_user_via_json()
    {
        $role = Role::factory()->create();
        $department = Department::factory()->create();
        
        $userData = [
            'name' => 'JSON User',
            'email' => 'json@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $role->id,
            'department_id' => $department->id
        ];

        $response = $this->postJson(route('users.store'), $userData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id', 'name', 'email', 'role_id', 'department_id'
        ]);
    }

    /** @test */
    public function it_can_update_user_via_json()
    {
        $testUser = User::factory()->create();
        $role = Role::factory()->create();

        $updateData = [
            'name' => 'Updated JSON User',
            'email' => 'updatedjson@example.com',
            'role_id' => $role->id
        ];

        $response = $this->putJson(route('users.update', $testUser->id), $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id', 'name', 'email', 'role_id', 'department_id'
        ]);
    }

    /** @test */
    public function it_can_delete_user_via_json()
    {
        $testUser = User::factory()->create();

        $response = $this->deleteJson(route('users.destroy', $testUser->id));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'User deleted successfully'
        ]);
    }
}