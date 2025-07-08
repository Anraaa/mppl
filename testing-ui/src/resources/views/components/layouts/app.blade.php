<!DOCTYPE html>
<html lang="en-us">
<head>
    @include('components.partials.head')
    @livewireStyles
</head>
<body>
    @include('components.partials.nav')

    {{-- Slot konten --}}
    {{ $slot }}

    @include('components.partials.bottom')

    @livewireScripts
    @stack('scripts')  {{-- Ini harus di sini, agar script snap.js dan JS payment muncul di halaman --}}

    
</body>
@include('components.partials.script')
</html>
