<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->user()->profile);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bio'=>'nullable|string|max:1000',
            'phone'=>'nullable|string|max:20',
            'avatar'=>'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'gender'=>'nullable|in:male,female,other',
            'dob'=>'nullable|date|before:today',
            'country'=>'nullable|string|max:100',
            'city'=>'nullable|string|max:100',
        ];
    }
}
