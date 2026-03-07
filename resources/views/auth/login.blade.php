<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Login | MediStock POS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="h-full bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 flex items-center justify-center p-4">
  <div class="w-full max-w-md">
    <div class="text-center mb-8">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-500 mb-4 shadow-xl shadow-blue-500/30">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-white">MediStock POS</h1>
      <p class="text-blue-300 text-sm mt-1">Hospital Medicine Stock System</p>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl p-8">
      <h2 class="text-xl font-semibold text-gray-800 mb-6">Sign in to your account</h2>
      @if($errors->any())
      <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-5">
        @foreach($errors->all() as $e)<p class="text-sm text-red-700">{{ $e }}</p>@endforeach
      </div>
      @endif
      <form action="{{ route('login') }}" method="POST" class="space-y-5">
        @csrf
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
          <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="you@hospital.com">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
          <input type="password" name="password" required autocomplete="current-password"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="••••••••">
        </div>
        <div class="flex items-center gap-2">
          <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300 text-blue-600">
          <label for="remember" class="text-sm text-gray-600">Remember me</label>
        </div>
        <button type="submit"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl transition text-sm">
          Sign In
        </button>
      </form>

      <div class="mt-6 pt-5 border-t border-gray-100">
        <p class="text-xs text-gray-400 text-center mb-2">Default credentials:</p>
        <div class="bg-gray-50 rounded-xl p-3 text-xs text-gray-500 space-y-1">
          <p><span class="font-semibold text-gray-700">Admin:</span> admin@hospital.com / Admin@12345</p>
          <p><span class="font-semibold text-gray-700">Pharmacist:</span> pharmacist@hospital.com / Pass@12345</p>
          <p><span class="font-semibold text-gray-700">Lab / Theatre / Ward:</span> [dept]@hospital.com / Pass@12345</p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
