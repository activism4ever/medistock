<?php
namespace App\Services;

use App\Models\Batch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BatchService
{
    public function __construct(private ActivityLogService $log) {}

    public function create(array $data): Batch
    {
        return DB::transaction(function () use ($data) {
            $selling = Batch::calculateSellingPrice(
                (float) $data['purchase_price'],
                (float) $data['margin_percentage']
            );

            $batch = Batch::create([
                'medicine_id'        => $data['medicine_id'],
                'batch_number'       => $data['batch_number'],
                'expiry_date'        => $data['expiry_date'],
                'purchase_price'     => $data['purchase_price'],
                'margin_percentage'  => $data['margin_percentage'],
                'selling_price'      => $selling,
                'receipt_no'         => $data['receipt_no'] ?? null,
                'invoice_no'         => $data['invoice_no'] ?? null,
                'quantity_purchased' => $data['quantity_purchased'],
                'quantity_remaining' => $data['quantity_purchased'],
                'created_by'         => Auth::id(),
            ]);

            $this->log->log(
                'batch_created',
                "Batch #{$batch->batch_number} created for {$batch->medicine->name}. Qty: {$batch->quantity_purchased}",
                Batch::class, $batch->id,
                ['qty' => $batch->quantity_purchased, 'selling_price' => $selling]
            );

            return $batch;
        });
    }

    public function update(Batch $batch, array $data): Batch
    {
        return DB::transaction(function () use ($batch, $data) {
            $selling = Batch::calculateSellingPrice(
                (float) $data['purchase_price'],
                (float) $data['margin_percentage']
            );

            $batch->update([
                'expiry_date'       => $data['expiry_date'],
                'purchase_price'    => $data['purchase_price'],
                'margin_percentage' => $data['margin_percentage'],
                'selling_price'     => $selling,
                'receipt_no'        => $data['receipt_no'] ?? null,
                'invoice_no'        => $data['invoice_no'] ?? null,
            ]);

            $this->log->log('batch_updated', "Batch #{$batch->batch_number} updated", Batch::class, $batch->id);
            return $batch;
        });
    }
}
