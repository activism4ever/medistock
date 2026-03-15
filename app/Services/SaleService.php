<?php
namespace App\Services;

use App\Models\DepartmentStock;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SaleService
{
    public function __construct(private ActivityLogService $log) {}

    /**
     * Process a POS sale.
     *
     * @param array $data   ['patient_name', 'patient_id'?, 'notes'?]
     * @param array $items  [['batch_id' => int, 'quantity' => int], ...]
     */
    public function process(array $data, array $items): Sale
    {
        return DB::transaction(function () use ($data, $items) {
            $user         = Auth::user();
            $departmentId = $user->department_id;

            $totalAmount = 0;
            $totalProfit = 0;
            $lineItems   = [];

            foreach ($items as $item) {
                $stock = DepartmentStock::lockForUpdate()
                    ->with('batch.medicine')
                    ->where('batch_id', $item['batch_id'])
                    ->where('department_id', $departmentId)
                    ->firstOrFail();

                $batch = $stock->batch;
                $qty   = (int) $item['quantity'];

                if ($batch->isExpired()) {
                    throw new InvalidArgumentException(
                        "Batch #{$batch->batch_number} ({$batch->medicine->name}) is expired."
                    );
                }
                if ($qty <= 0) {
                    throw new InvalidArgumentException('Item quantity must be at least 1.');
                }
                if ($qty > $stock->quantity_remaining) {
                    throw new InvalidArgumentException(
                        "Insufficient stock for {$batch->medicine->name}. Available: {$stock->quantity_remaining}."
                    );
                }

                $profit       = ($batch->selling_price - $batch->purchase_price) * $qty;
                $totalAmount += $batch->selling_price * $qty;
                $totalProfit += $profit;

                $stock->decrement('quantity_remaining', $qty);

                $lineItems[] = [
                    'batch_id'       => $batch->id,
                    'quantity'       => $qty,
                    'selling_price'  => $batch->selling_price,
                    'purchase_price' => $batch->purchase_price,
                    'profit'         => $profit,
                ];
            }

            $sale = Sale::create([
                'receipt_number' => $this->receiptNumber(),
                'department_id'  => $departmentId,
                'sold_by'        => $user->id,
                'patient_name'   => $data['patient_name'],
                'patient_id'     => $data['patient_id'] ?? null,
                'total_amount'   => $totalAmount,
                'total_profit'   => $totalProfit,
                'notes'          => $data['notes'] ?? null,
                'status'         => 'completed',
		'drawer_number'  => $user->drawer_number,
            ]);

            foreach ($lineItems as $line) {
                $sale->items()->create($line);
            }

            $this->log->log(
                'sale_completed',
                "Sale #{$sale->receipt_number} — Patient: {$sale->patient_name} — Total: {$totalAmount}",
                Sale::class, $sale->id,
                ['items' => count($items), 'amount' => $totalAmount]
            );

            return $sale->load('items.batch.medicine', 'department', 'soldBy');
        });
    }

    private function receiptNumber(): string
    {
        $count = Sale::whereDate('created_at', today())->count() + 1;
        return 'RCP-' . now()->format('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
