<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicineRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->isAdmin(); }

    public function rules(): array
    {
        return [
            'name'         => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'dosage'       => 'nullable|string|max:100',
            'unit'         => 'required|string|max:50',
            'category'     => 'nullable|string|max:100',
            'description'  => 'nullable|string|max:1000',
        ];
    }
}
