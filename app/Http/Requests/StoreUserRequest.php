<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->isAdmin(); }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => ['required', Password::min(8)->letters()->numbers()],
            'role'          => 'required|in:admin,pharmacist,lab,theatre,ward',
            'department_id' => 'nullable|exists:departments,id',
        ];
    }
}
