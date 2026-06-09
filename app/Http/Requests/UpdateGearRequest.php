<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $gearId = $this->route('gear') ?? $this->route('id');
        return [
            'tipo' => 'sometimes|required|string|max:255|unique:gears,tipo,' . $gearId,
            'speed_count' => 'sometimes|required|integer|min:1|max:10',
        ];
    }
}
