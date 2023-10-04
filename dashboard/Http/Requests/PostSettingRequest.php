<?php

namespace Dashboard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Dashboard\Models\Setting;

class PostSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $model = Setting::find($this->get('id'));
        return !$model || $this->user()->can('post', $model);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $availableComponents = ['input-text', 'textarea', 'input-number', 'file', 'switch'];
        return [
            'key' => ['required', 'min:2', Rule::unique('settings')->ignore($this->get('id'))],
            'title' => ['required', 'min:2'],
            'description' => ['sometimes'],
            'parent_id' => ['sometimes', 'nullable', 'numeric'],
            'type' => ['required', Rule::in($availableComponents)],
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
            'key.unique' => 'Key :input already exist. Input unique key.',
        ];
    }
}
