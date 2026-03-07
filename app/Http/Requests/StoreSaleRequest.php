<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->isDepartmentUser(); }

    public function rules(): array
    {
        return [
            'patient_name'         => 'required|string|max:255',
            'patient_id'           => 'nullable|string|max:100',
            'notes'                => 'nullable|string|max:1000',
            'items'                => 'required|array|min:1',
            'items.*.batch_id'     => 'required|exists:batches,id',
            'items.*.quantity'     => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'          => 'You must add at least one item.',
            'items.*.batch_id.exists' => 'Invalid medicine batch selected.',
        ];
    }
}
