<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Form;
use App\Models\FormSignatory;
use App\Models\User;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class FormControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_display_forms_index()
    {
        Form::factory()->count(3)->create();

        $response = $this->get(route('forms.index'));

        $response->assertStatus(200);
        $response->assertViewIs('forms.index');
        $response->assertViewHas('forms');
    }

    /** @test */
    public function it_can_store_a_new_form()
    {
        $formData = [
            'name' => 'Test Form',
            'description' => 'Test Description',
            'is_active' => true
        ];

        $response = $this->post(route('forms.store'), $formData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Form created successfully!');
        
        $this->assertDatabaseHas('forms', [
            'name' => 'Test Form',
            'description' => 'Test Description',
            'is_active' => true
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing_form()
    {
        $response = $this->post(route('forms.store'), []);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function it_validates_unique_name_when_storing_form()
    {
        Form::factory()->create(['name' => 'Existing Form']);

        $response = $this->post(route('forms.store'), [
            'name' => 'Existing Form',
            'description' => 'Test Description'
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function it_can_show_edit_form_for_form()
    {
        $form = Form::factory()->create();

        $response = $this->get(route('forms.edit', $form->id));

        $response->assertStatus(200);
        $response->assertViewIs('forms.index');
        $response->assertViewHas('form', $form);
    }

    /** @test */
    public function it_can_update_a_form()
    {
        $form = Form::factory()->create();

        $updateData = [
            'name' => 'Updated Form',
            'description' => 'Updated Description',
            'is_active' => false
        ];

        $response = $this->put(route('forms.update', $form->id), $updateData);

        $response->assertRedirect(route('forms.index'));
        $response->assertSessionHas('success', 'Form updated successfully!');
        
        $this->assertDatabaseHas('forms', [
            'id' => $form->id,
            'name' => 'Updated Form',
            'description' => 'Updated Description',
            'is_active' => false
        ]);
    }

    /** @test */
    public function it_validates_unique_name_when_updating_form_except_itself()
    {
        $form1 = Form::factory()->create(['name' => 'Form 1']);
        $form2 = Form::factory()->create(['name' => 'Form 2']);

        // Should fail - trying to use existing name
        $response = $this->put(route('forms.update', $form1->id), [
            'name' => 'Form 2',
            'description' => 'Test'
        ]);
        $response->assertSessionHasErrors(['name']);

        // Should pass - using same name
        $response = $this->put(route('forms.update', $form1->id), [
            'name' => 'Form 1',
            'description' => 'Updated Description'
        ]);
        $response->assertRedirect(route('forms.index'));
    }

    /** @test */
    public function it_can_delete_a_form_when_not_in_use()
    {
        $form = Form::factory()->create();

        $response = $this->delete(route('forms.destroy', $form->id));

        $response->assertRedirect(route('forms.index'));
        $response->assertSessionHas('success', 'Form deleted successfully!');
        
        $this->assertDatabaseMissing('forms', [
            'id' => $form->id
        ]);
    }

    /** @test */
    public function it_cannot_delete_a_form_when_in_use()
    {
        $form = Form::factory()->create();
        $department = Department::factory()->create();
        $signatory = User::factory()->create();
        
        // Create a form signatory that uses this form
        FormSignatory::factory()->create([
            'form_id' => $form->id,
            'department_id' => $department->id,
            'signatory_id' => $signatory->id
        ]);

        $response = $this->delete(route('forms.destroy', $form->id));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Cannot delete form that is being used by form signatories');
        
        $this->assertDatabaseHas('forms', [
            'id' => $form->id
        ]);
    }

    /** @test */
    public function it_returns_json_response_when_requested()
    {
        Form::factory()->count(2)->create();

        $response = $this->getJson(route('forms.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'description', 'is_active', 'created_at', 'updated_at']
            ]
        ]);
    }

    /** @test */
    public function it_can_store_form_via_json()
    {
        $formData = [
            'name' => 'JSON Form',
            'description' => 'JSON Description',
            'is_active' => true
        ];

        $response = $this->postJson(route('forms.store'), $formData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'is_active']
        ]);
    }

    /** @test */
    public function it_can_update_form_via_json()
    {
        $form = Form::factory()->create();

        $updateData = [
            'name' => 'Updated JSON Form',
            'description' => 'Updated JSON Description',
            'is_active' => false
        ];

        $response = $this->putJson(route('forms.update', $form->id), $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'is_active']
        ]);
    }

    /** @test */
    public function it_can_delete_form_via_json()
    {
        $form = Form::factory()->create();

        $response = $this->deleteJson(route('forms.destroy', $form->id));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Form deleted successfully'
        ]);
    }
}