@extends('layouts.app')
@section('title','Add User') @section('page-title','Add New User')
@section('content')
<div class="pt-2 max-w-lg">
  <div class="bg-white rounded-2xl border border-gray-200 p-7">
    <form action="{{ route('users.store') }}" method="POST" class="space-y-5"
      x-data="{ role: '{{ old('role', 'pharmacist') }}' }">
      @csrf
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name *</label>
        <input type="text" name="name" value="{{ old('name') }}" required
          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address *</label>
        <input type="email" name="email" value="{{ old('email') }}" required
          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password *</label>
        <input type="password" name="password" required autocomplete="new-password"
          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Min 8 chars, letters + numbers">
        @error('password')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Role *</label>
        <select name="role" x-model="role" required
          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
         @foreach(['admin','pharmacist','lab','theatre','ward','hod_pharmacy','cashier'] as $r)
<option value="{{ $r }}" {{ old('role')==$r?'selected':'' }}>
  {{ match($r) {
    'hod_pharmacy' => 'HOD Pharmacy',
    'cashier'      => 'Cashier',
    default        => ucfirst($r)
  } }}
</option>
@endforeach
        </select>
        @error('role')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Department</label>
        <select name="department_id"
          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">-- None (Admin) --</option>
          @foreach($departments as $d)
          <option value="{{ $d->id }}" {{ old('department_id')==$d->id?'selected':'' }}>{{ $d->name }}</option>
          @endforeach
        </select>
      </div>

      {{-- Drawer Number — only visible for Pharmacist --}}
      <div x-show="role === 'pharmacist'" x-transition>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
          Drawer Number
          <span class="text-xs text-gray-400 font-normal ml-1">(Pharmacy only)</span>
        </label>
        <select name="drawer_number"
          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">— Not Assigned —</option>
          <option value="1" {{ old('drawer_number')=='1' ? 'selected' : '' }}>🗄 Drawer 1</option>
          <option value="2" {{ old('drawer_number')=='2' ? 'selected' : '' }}>🗄 Drawer 2</option>
          <option value="3" {{ old('drawer_number')=='3' ? 'selected' : '' }}>🗄 Drawer 3</option>
        </select>
        @error('drawer_number')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold">
          Create User
        </button>
        <a href="{{ route('users.index') }}"
          class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-medium">
          Cancel
        </a>
      </div>
    </form>
  </div>
</div>
@endsection