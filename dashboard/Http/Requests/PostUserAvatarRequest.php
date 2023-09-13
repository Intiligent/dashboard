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
            'file' => ['required', 'file', 'mimetypes:'.implode(',', $allowMimeTypes)],
            'id' => ['required', 'numeric', 'min:1'],
        ];
    }
}
