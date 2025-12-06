<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\FormSignatory;
use App\Models\Form;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing form signatories to use form_id instead of form_name
        $formSignatories = FormSignatory::whereNull('form_id')->get();
        
        foreach ($formSignatories as $formSignatory) {
            if ($formSignatory->form_name) {
                $form = Form::where('name', $formSignatory->form_name)->first();
                if ($form) {
                    $formSignatory->update(['form_id' => $form->id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert form_id back to form_name
        $formSignatories = FormSignatory::whereNotNull('form_id')->get();
        
        foreach ($formSignatories as $formSignatory) {
            if ($formSignatory->form_id && $formSignatory->form) {
                $formSignatory->update(['form_name' => $formSignatory->form->name]);
            }
        }
        
        // Set form_id to null
        FormSignatory::whereNotNull('form_id')->update(['form_id' => null]);
    }
};