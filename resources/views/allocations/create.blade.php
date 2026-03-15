@extends('layouts.app')
@section('title','Allocate Stock') @section('page-title','Allocate Stock to Department')
@section('content')
<div class="pt-2 max-w-xl">
  <div class="bg-white rounded-2xl border border-gray-200 p-7">
    <form action="{{ route('allocations.store') }}" method="POST" class="space-y-5"
      x-data="allocForm()">
      @csrf

      {{-- Batch Selector --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Select Batch *</label>
        <select name="batch_id" required x-model="batchId" @change="updateInfo()"
          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">-- Select a batch --</option>
          @foreach($batches as $b)
          <option value="{{ $b->id }}"
            data-medicine="{{ $b->medicine->name }}"
            data-expiry="{{ $b->expiry_date->format('d M Y') }}"
            data-remaining="{{ $b->quantity_remaining }}"
            {{ (old('batch_id') == $b->id || ($selectedBatch && $selectedBatch->id == $b->id)) ? 'selected' : '' }}>
            {{ $b->medicine->name }} — Batch {{ $b->batch_number }} ({{ $b->quantity_remaining }} available)
          </option>
          @endforeach
        </select>
        @error('batch_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Batch Info Card --}}
      <div x-show="info.medicine" x-cloak class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
        <div class="grid grid-cols-3 gap-3 text-sm">
          <div><p class="text-blue-400 text-xs">Medicine</p><p class="font-semibold text-blue-800" x-text="info.medicine"></p></div>
          <div><p class="text-blue-400 text-xs">Expiry</p><p class="font-semibold text-blue-800" x-text="info.expiry"></p></div>
          <div><p class="text-blue-400 text-xs">Available</p><p class="font-bold text-blue-900 text-lg" x-text="info.remaining"></p></div>
        </div>
      </div>

      {{-- Department Selector --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Department *</label>
        <select name="department_id" required x-model="deptId" @change="updateDept()"
          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">-- Select Department --</option>
          @foreach($departments as $d)
          <option value="{{ $d->id }}"
            data-name="{{ $d->name }}"
            {{ old('department_id')==$d->id?'selected':'' }}>
            {{ $d->name }}
          </option>
          @endforeach
        </select>
        @error('department_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Drawer Selector — only shows for Pharmacy --}}
      <div x-show="isPharmacy" x-transition>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
          Allocate to Drawer
          <span class="text-xs text-gray-400 font-normal ml-1">(Pharmacy only)</span>
        </label>
        <select name="drawer_number"
          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
          <option value="">— All Drawers / General —</option>
          <option value="1" {{ old('drawer_number')=='1'?'selected':'' }}>🗄 Drawer 1</option>
          <option value="2" {{ old('drawer_number')=='2'?'selected':'' }}>🗄 Drawer 2</option>
          <option value="3" {{ old('drawer_number')=='3'?'selected':'' }}>🗄 Drawer 3</option>
        </select>
        @error('drawer_number')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Quantity --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Quantity to Allocate *</label>
        <input type="number" name="quantity_allocated" value="{{ old('quantity_allocated') }}"
          min="1" :max="info.remaining" required
          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <p class="text-xs text-gray-400 mt-1">Max available: <strong x-text="info.remaining || '—'"></strong></p>
        @error('quantity_allocated')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Notes --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Notes (optional)</label>
        <textarea name="notes" rows="2"
          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Reason for allocation...">{{ old('notes') }}</textarea>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold">
          Allocate Stock
        </button>
        <a href="{{ route('allocations.index') }}"
          class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-medium">
          Cancel
        </a>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function allocForm() {
    return {
        batchId:    '{{ old("batch_id", $selectedBatch?->id ?? "") }}',
        deptId:     '{{ old("department_id", "") }}',
        isPharmacy: false,
        info: { medicine:'', expiry:'', remaining:'' },

        updateInfo() {
            const opt = document.querySelector(`[name="batch_id"] option[value="${this.batchId}"]`);
            this.info = opt
                ? { medicine: opt.dataset.medicine, expiry: opt.dataset.expiry, remaining: opt.dataset.remaining }
                : { medicine:'', expiry:'', remaining:'' };
        },

        updateDept() {
            const opt = document.querySelector(`[name="department_id"] option[value="${this.deptId}"]`);
            this.isPharmacy = opt ? opt.dataset.name.toLowerCase() === 'pharmacy' : false;
        },

        init() {
            if (this.batchId) this.$nextTick(() => this.updateInfo());
            if (this.deptId)  this.$nextTick(() => this.updateDept());
        }
    }
}
</script>
@endpush
@endsection