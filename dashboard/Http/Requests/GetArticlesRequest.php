<?php

namespace Dashboard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetArticlesRequest extends FormRequest
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
        return [
            'filterModel' => ['sometimes'],
            'filterModel.active' => ['sometimes', 'array', 'in:0,1'],
            // 'lang_id' => ['required_without:id'], // 'sometimes', 'required',
            // 'title' => ['sometimes', 'required', 'min:2'],
            // 'slug' => [
            //     'sometimes',
            //     'required',
            //     'min:2',
            //     new Slug,
            //     Rule::unique('articles')->ignore($this->id),
            // ],
            // 'text' => ['sometimes'],
            // 'active' => ['required_with:id', 'boolean'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [];
    }
}
