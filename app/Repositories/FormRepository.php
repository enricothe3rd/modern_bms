<?php

namespace App\Repositories;

use App\Models\Form;

class FormRepository
{
    public function allActive()
    {
        return Form::active()->orderBy('name')->get();
    }

    public function all()
    {
        return Form::orderBy('name')->get();
    }

    public function find($id)
    {
        return Form::findOrFail($id);
    }
}
