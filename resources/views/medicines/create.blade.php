@extends('layouts.app')
@section('title','Add Medicine') @section('page-title','Add New Medicine')
@section('content')
<div class="pt-2 max-w-2xl">
  <div class="bg-white rounded-2xl border border-gray-200 p-7">
    <form action="{{ route('medicines.store') }}" method="POST" class="space-y-5">
      @csrf
      <div class="grid grid-cols-2 gap-5">
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Brand / Trade Name *</label>
          <input type="text" name="name" value="{{ old('name') }}" required
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="e.g. Paracetamol 500mg">
          @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Generic Name</label>
          <input type="text" name="generic_name" value="{{ old('generic_name') }}"
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="e.g. Acetaminophen">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Dosage</label>
          <input type="text" name="dosage" value="{{ old('dosage') }}"
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="e.g. 500mg">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Unit *</label>
          <select name="unit" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @foreach(['tablets','capsules','sachets','vials','ampoules','bags','bottles','tubes','units'] as $u)
            <option value="{{ $u }}" {{ old('unit')==$u?'selected':'' }}>{{ ucfirst($u) }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Category</label>
          <input type="text" name="category" value="{{ old('category') }}"
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="e.g. Antibiotic, NSAID">
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
          <textarea name="description" rows="3"
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
        </div>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium">Save Medicine</button>
        <a href="{{ route('medicines.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-medium">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
