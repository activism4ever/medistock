<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBatchRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->isAdmin(); }

    public function rules(): array
    {
        return [
            'expiry_date'       => 'required|date|after:today',
            'purchase_price'    => 'required|numeric|min:0.01',
            'margin_percentage' => 'required|numeric|min:0|max:1000',
            'receipt_no'        => 'nullable|string|max:100',
            'invoice_no'        => 'nullable|string|max:100',
        ];
    }
}
