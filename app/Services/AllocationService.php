<?php
namespace App\Services;

use App\Models\Allocation;
use App\Models\Batch;
use App\Models\Department;
use App\Models\DepartmentStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AllocationService
{
    public function __construct(private ActivityLogService $log) {}

    public function allocate(array $data): Allocation
    {
        return DB::transaction(function () use ($data) {
            /** @var Batch $batch */
            $batch      = Batch::lockForUpdate()->findOrFail($data['batch_id']);
            $department = Department::findOrFail($data['department_id']);
            $qty        = (int) $data['quantity_allocated'];

            if ($batch->isExpired()) {
                throw new InvalidArgumentException('Cannot allocate stock from an expired batch.');
            }
            if ($qty <= 0) {
                throw new InvalidArgumentException('Quantity must be greater than zero.');
            }
            if ($qty > $batch->quantity_remaining) {
                throw new InvalidArgumentException(
                    "Only {$batch->quantity_remaining} units available in batch #{$batch->batch_number}."
                );
            }

            $batch->decrement('quantity_remaining', $qty);

            $allocation = Allocation::create([
                'batch_id'           => $batch->id,
                'department_id'      => $department->id,
                'quantity_allocated' => $qty,
                'allocated_by'       => Auth::id(),
                'notes'              => $data['notes'] ?? null,
            ]);

            // Upsert department stock
            $stock = DepartmentStock::firstOrCreate(
                ['batch_id' => $batch->id, 'department_id' => $department->id],
                ['quantity_remaining' => 0]
            );
            $stock->increment('quantity_remaining', $qty);

            $this->log->log(
                'stock_allocated',
                "Allocated {$qty} units of batch #{$batch->batch_number} to {$department->name}",
                Allocation::class, $allocation->id,
                ['batch_id' => $batch->id, 'department_id' => $department->id, 'qty' => $qty]
            );

            return $allocation;
        });
    }
}
