@extends('errors.layout')

@section('title', 'Akses Ditolak — 403 Forbidden')
@section('code', '403 — Forbidden')

@section('icon')
    <i class="fa-solid fa-shield-halved text-2xl sm:text-3xl text-[#991B1B]"></i>
@endsection

@section('headline', 'Akses Wewenang Ditolak')

@section('message')
    {{ $exception->getMessage() ?: 'Akun Anda tidak memiliki hak akses atau izin operasional yang memadai untuk membuka halaman atau menjalankan tindakan ini.' }}
@endsection

@if ($exception->getMessage())
    @section('detail')
        <div class="flex items-start space-x-2 text-[#991B1B]">
            <i class="fa-solid fa-circle-exclamation mt-0.5 text-sm"></i>
            <div>
                <strong class="font-bold block text-xs">Pemberitahuan Sistem Keamanan:</strong>
                <span class="text-xs">{{ $exception->getMessage() }}</span>
            </div>
        </div>
    @endsection
@endif
