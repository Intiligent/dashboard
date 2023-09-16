<?php

namespace Dashboard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Media;

class PostUserAvatarRequest extends FormRequest
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
        // list of available download formats
        $allowMimeTypes = array_values(Media::$allowImageMimeTypes);

        return [
            'file' => ['required', 'image', 'dimensions:ratio=1/1', 'mimetypes:'.implode(',', $allowMimeTypes)],
            'id' => ['required', 'numeric', 'min:1'],
            // image|dimensions:min_width=1400,min_height=1400,max_width=3000,max_height=3000,ratio=1/1
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
            'file.dimensions' => 'The file field has invalid image dimensions. Must be square.',
        ];
    }
}
