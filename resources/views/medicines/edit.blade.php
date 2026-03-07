@extends('layouts.app')
@section('title','Edit Medicine') @section('page-title','Edit: '.$medicine->name)
@section('content')
<div class="pt-2 max-w-2xl">
  <div class="bg-white rounded-2xl border border-gray-200 p-7">
    <form action="{{ route('medicines.update',$medicine) }}" method="POST" class="space-y-5">
      @csrf @method('PUT')
      <div class="grid grid-cols-2 gap-5">
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Brand / Trade Name *</label>
          <input type="text" name="name" value="{{ old('name',$medicine->name) }}" required
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Generic Name</label>
          <input type="text" name="generic_name" value="{{ old('generic_name',$medicine->generic_name) }}"
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Dosage</label>
          <input type="text" name="dosage" value="{{ old('dosage',$medicine->dosage) }}"
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Unit *</label>
          <select name="unit" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @foreach(['tablets','capsules','sachets','vials','ampoules','bags','bottles','tubes','units'] as $u)
            <option value="{{ $u }}" {{ old('unit',$medicine->unit)==$u?'selected':'' }}>{{ ucfirst($u) }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Category</label>
          <input type="text" name="category" value="{{ old('category',$medicine->category) }}"
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
          <textarea name="description" rows="3"
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description',$medicine->description) }}</textarea>
        </div>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium">Update Medicine</button>
        <a href="{{ route('medicines.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-medium">Cancel</a>
        <form action="{{ route('medicines.destroy',$medicine) }}" method="POST" class="ml-auto"
          onsubmit="return confirm('Delete this medicine?')">
          @csrf @method('DELETE')
          <button class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2.5 rounded-xl text-sm font-medium">Delete</button>
        </form>
      </div>
    </form>
  </div>
</div>
@endsection
