<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->isAdmin(); }

    public function rules(): array
    {
        return [
            'medicine_id'        => 'required|exists:medicines,id',
            'batch_number'       => 'required|string|max:100|unique:batches,batch_number',
            'expiry_date'        => 'required|date|after:today',
            'purchase_price'     => 'required|numeric|min:0.01',
            'margin_percentage'  => 'required|numeric|min:0|max:1000',
            'receipt_no'         => 'nullable|string|max:100',
            'invoice_no'         => 'nullable|string|max:100',
            'quantity_purchased' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'expiry_date.after'      => 'Expiry date must be in the future.',
            'margin_percentage.max'  => 'Margin cannot exceed 1000%.',
        ];
    }
}
