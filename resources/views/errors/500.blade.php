@extends('errors.layout')

@section('title', 'Kendala Server — 500 Server Error')
@section('code', '500 — Server Error')

@section('icon')
    <i class="fa-solid fa-triangle-exclamation text-2xl sm:text-3xl text-[#991B1B]"></i>
@endsection

@section('headline', 'Terjadi Kendala pada Sistem')

@section('message')
    Sistem mengalami kendala saat memproses permintaan Anda. Tim teknis kami telah mencatat peristiwa ini untuk penanganan lebih lanjut.
@endsection

@if (app()->environment('local', 'testing') && !empty($exception?->getMessage()))
    @section('detail')
        <div class="text-[#991B1B]">
            <strong class="font-bold block text-xs mb-1">Pesan Diagnosa Teknis (Mode Pengembang):</strong>
            <span class="text-xs">{{ $exception->getMessage() }}</span>
        </div>
    @endsection
@endif
