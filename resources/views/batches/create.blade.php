@extends('layouts.app')
@section('title','Record Purchase') @section('page-title','Record New Purchase Batch')
@section('content')
<div class="pt-2 max-w-2xl">
  <div class="bg-white rounded-2xl border border-gray-200 p-7">
    <form action="{{ route('batches.store') }}" method="POST" class="space-y-5"
      x-data="{ price: 0, margin: 30, get selling() { return (parseFloat(this.price||0) * (1 + parseFloat(this.margin||0)/100)).toFixed(2) } }">
      @csrf
      <div class="grid grid-cols-2 gap-5">
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Medicine *</label>
          <select name="medicine_id" required class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">-- Select Medicine --</option>
            @foreach($medicines as $m)
            <option value="{{ $m->id }}" {{ old('medicine_id')==$m->id?'selected':'' }}>{{ $m->name }}</option>
            @endforeach
          </select>
          @error('medicine_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Batch Number *</label>
          <input type="text" name="batch_number" value="{{ old('batch_number') }}" required
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="BCH-2024-001">
          @error('batch_number')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Expiry Date *</label>
          <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" required
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          @error('expiry_date')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Purchase Price (&#x20A6;) *</label>
          <input type="number" name="purchase_price" value="{{ old('purchase_price') }}" step="0.01" min="0.01" required
            x-model="price"
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          @error('purchase_price')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Margin % *</label>
          <input type="number" name="margin_percentage" value="{{ old('margin_percentage',30) }}" step="0.1" min="0" max="1000" required
            x-model="margin"
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          @error('margin_percentage')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="col-span-2">
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl px-5 py-4 flex items-center justify-between">
            <div>
              <p class="text-xs text-blue-600 font-medium uppercase tracking-wide">Auto-calculated Selling Price</p>
              <p class="text-xs text-blue-400 mt-0.5">= Purchase Price + (Purchase Price × Margin%)</p>
            </div>
            <p class="text-2xl font-bold text-blue-700">&#x20A6;<span x-text="selling">0.00</span></p>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Quantity Purchased *</label>
          <input type="number" name="quantity_purchased" value="{{ old('quantity_purchased') }}" min="1" required
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          @error('quantity_purchased')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div></div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Receipt No.</label>
          <input type="text" name="receipt_no" value="{{ old('receipt_no') }}"
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Invoice No.</label>
          <input type="text" name="invoice_no" value="{{ old('invoice_no') }}"
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold">Record Batch</button>
        <a href="{{ route('batches.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-medium">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
