<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | KKIPP UPI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{upi:{red:'#800000',dark:'#600000',light:'#9a0000',black:'#111111',gold:'#FFCC00'}}}}}</script>
    <style>body{font-family:'Segoe UI',system-ui,sans-serif;}</style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm">
        {{-- Logo --}}
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-upi-red text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-3">U</div>
            <h1 class="text-lg font-bold text-gray-800">Panel Admin KKIPP UPI</h1>
            <p class="text-gray-500 text-sm">Sistem Permohonan Kunjungan Sekolah</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}" id="form-login">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-light"
                        value="{{ old('email') }}" placeholder="admin@upi.edu" required autofocus>
                </div>
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red"
                        placeholder="••••••••" required>
                </div>
                <div class="flex items-center gap-2 mb-5">
                    <input type="checkbox" id="remember" name="remember" class="rounded border-gray-300">
                    <label for="remember" class="text-sm text-gray-600">Ingat saya</label>
                </div>
                <button type="submit" id="btn-login" class="w-full bg-upi-red text-white py-2.5 rounded-lg font-bold hover:bg-upi-dark transition-colors">
                    Masuk
                </button>
            </form>

            <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                <a href="{{ route('home') }}" class="text-xs text-gray-400 hover:text-gray-600">← Kembali ke Halaman Publik</a>
            </div>
        </div>
    </div>
</body>
</html>
