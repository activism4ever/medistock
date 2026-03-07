<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAllocationRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->isAdmin(); }

    public function rules(): array
    {
        return [
            'batch_id'           => 'required|exists:batches,id',
            'department_id'      => 'required|exists:departments,id',
            'quantity_allocated' => 'required|integer|min:1',
            'notes'              => 'nullable|string|max:500',
        ];
    }
}
