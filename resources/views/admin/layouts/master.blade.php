<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&family=Dancing+Script:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin.css') . '?v=2' }}">
@stack('styles')
</head>
<body>
<div id="admin-app">
    @include('admin.layouts.sidebar')
    <div class="admin-main">
        @include('admin.layouts.header')
        <div class="admin-content">
            @if(session('success'))
            <div class="toast toast-success" id="toast">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()">&times;</button>
            </div>
            @endif
            @if(session('error'))
            <div class="toast toast-error" id="toast">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()">&times;</button>
            </div>
            @endif
            @if($errors->any())
            <div class="toast toast-error">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ $errors->first() }}</span>
                <button onclick="this.parentElement.remove()">&times;</button>
            </div>
            @endif
            @yield('content')
        </div>
    </div>
</div>
<div id="modal-overlay" class="modal-overlay" onclick="closeAllModals()"></div>
<script src="{{ asset('js/admin.js') }}"></script>
@stack('scripts')
<script>
setTimeout(() => document.querySelectorAll('.toast').forEach(t => { t.classList.add('fade-out'); setTimeout(() => t.remove(), 400); }), 4000);
</script>
</body>
</html>
