<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserArticlePreferenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'attribute' => ['required', 'string', 'in:liked_authors,liked_categories,liked_sources'],
            'action' => ['required', 'string', 'in:like,dislike'],
            'value' => ['required', 'string']
        ];
    }
}
