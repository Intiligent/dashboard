<?php

namespace Dashboard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Menu;

class PostMenuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $types = implode(',', Menu::TYPES);
        return [
            'name' => ['required_without:id', 'min:2'],
            'parent_id' => ['sometimes', 'nullable', 'numeric'],
            'type' => ['sometimes', 'required', "in:$types"],
            'value' => ['sometimes', 'required_with:type'],
            'active' => ['sometimes', 'required_with:id', 'boolean'],
            'attribute' => ['sometimes'],
            'icon' => ['sometimes'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'parent_id.numeric' => 'Select menu group',
            'value.required_with' => 'Input link value',
        ];
    }
}
