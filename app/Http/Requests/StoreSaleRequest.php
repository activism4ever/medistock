<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'patient_name'        => 'required|string|max:255',
            'patient_id'          => 'nullable|string|max:100',
            'notes'               => 'nullable|string|max:500',
            'sale_type'           => 'required|in:normal,insurance',
            'insurance_scheme_id' => 'required_if:sale_type,insurance|nullable|exists:insurance_schemes,id',
            'enrolee_name'        => 'required_if:sale_type,insurance|nullable|string|max:255',
            'enrolee_id'          => 'required_if:sale_type,insurance|nullable|string|max:100',
            'items'               => 'required|array|min:1',
            'items.*.batch_id'    => 'required|exists:department_stock,batch_id',
            'items.*.quantity'    => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'insurance_scheme_id.required_if' => 'Please select an insurance scheme.',
            'enrolee_name.required_if'        => 'Enrolee name is required for insurance sales.',
            'enrolee_id.required_if'          => 'Enrolee ID is required for insurance sales.',
        ];
    }
}