@extends('layouts.app')
@section('title','Edit User') @section('page-title','Edit User: '.$user->name)
@section('content')
<div class="pt-2 max-w-lg">
  <div class="bg-white rounded-2xl border border-gray-200 p-7">
    <form action="{{ route('users.update',$user) }}" method="POST" class="space-y-5">
      @csrf @method('PUT')
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name *</label>
        <input type="text" name="name" value="{{ old('name',$user->name) }}" required
          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address *</label>
        <input type="email" name="email" value="{{ old('email',$user->email) }}" required
          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">New Password <span class="text-gray-400 font-normal">(leave blank to keep current)</span></label>
        <input type="password" name="password" autocomplete="new-password"
          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Role *</label>
        <select name="role" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          @foreach(['admin','pharmacist','lab','theatre','ward','hod_pharmacy','cashier'] as $r)
	<option value="{{ $r }}" {{ old('role',$user->role)==$r?'selected':'' }}>
  {{ match($r) {
    'hod_pharmacy' => 'HOD Pharmacy',
    'cashier'      => 'Cashier',
    default        => ucfirst($r)
  } }}
</option>
@endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Department</label>
        <select name="department_id" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">-- None --</option>
          @foreach($departments as $d)
          <option value="{{ $d->id }}" {{ old('department_id',$user->department_id)==$d->id?'selected':'' }}>{{ $d->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active',$user->is_active)?'checked':'' }} class="rounded border-gray-300 text-blue-600">
        <label for="is_active" class="text-sm text-gray-700">Account is active</label>
      </div>
      <div class="flex gap-3 pt-2">
{{-- Drawer Number — only visible for Pharmacist --}}
<div x-data="{ role: '{{ old('role', $user->role) }}' }">
  <select name="role" x-model="role"
    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    @foreach(['admin','pharmacist','lab','theatre','ward','hod_pharmacy','cashier'] as $r)
    <option value="{{ $r }}" {{ old('role',$user->role)==$r?'selected':'' }}>
      {{ match($r) { 'hod_pharmacy' => 'HOD Pharmacy', 'cashier' => 'Cashier', default => ucfirst($r) } }}
    </option>
    @endforeach
  </select>

  <div x-show="role === 'pharmacist'" x-transition class="mt-4">
    <label class="block text-sm font-medium text-gray-700 mb-1.5">Drawer Number</label>
    <select name="drawer_number"
      class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
      <option value="">— Not Assigned —</option>
      <option value="1" {{ old('drawer_number',$user->drawer_number)=='1'?'selected':'' }}>🗄 Drawer 1</option>
      <option value="2" {{ old('drawer_number',$user->drawer_number)=='2'?'selected':'' }}>🗄 Drawer 2</option>
      <option value="3" {{ old('drawer_number',$user->drawer_number)=='3'?'selected':'' }}>🗄 Drawer 3</option>
    </select>
  </div>
</div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold">Save Changes</button>
        <a href="{{ route('users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-medium">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
