@extends('layouts.app')
@section('title','Edit Batch') @section('page-title','Edit Batch: '.$batch->batch_number)
@section('content')
<div class="pt-2 max-w-2xl">
  <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-5 text-sm text-amber-800">
    <strong>Note:</strong> Batch number, medicine, and quantity cannot be changed here.
    Use allocations to distribute stock.
  </div>
  <div class="bg-white rounded-2xl border border-gray-200 p-7">
    <form action="{{ route('batches.update',$batch) }}" method="POST" class="space-y-5"
      x-data="{ price: {{ $batch->purchase_price }}, margin: {{ $batch->margin_percentage }},
                get selling() { return (parseFloat(this.price||0)*(1+parseFloat(this.margin||0)/100)).toFixed(2) } }">
      @csrf @method('PUT')
      <div class="grid grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Expiry Date *</label>
          <input type="date" name="expiry_date" value="{{ old('expiry_date',$batch->expiry_date->format('Y-m-d')) }}" required
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Purchase Price (&#x20A6;) *</label>
          <input type="number" name="purchase_price" value="{{ old('purchase_price',$batch->purchase_price) }}" step="0.01" required
            x-model="price"
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Margin % *</label>
          <input type="number" name="margin_percentage" value="{{ old('margin_percentage',$batch->margin_percentage) }}" step="0.1" min="0" max="1000" required
            x-model="margin"
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex items-end">
          <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 w-full">
            <p class="text-xs text-blue-500">Auto Selling Price</p>
            <p class="text-xl font-bold text-blue-800">&#x20A6;<span x-text="selling"></span></p>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Receipt No.</label>
          <input type="text" name="receipt_no" value="{{ old('receipt_no',$batch->receipt_no) }}"
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Invoice No.</label>
          <input type="text" name="invoice_no" value="{{ old('invoice_no',$batch->invoice_no) }}"
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold">Update Batch</button>
        <a href="{{ route('batches.show',$batch) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-medium">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
