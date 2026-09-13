@extends('errors.layout')

@section('title', 'Sesi Berakhir — 419 Page Expired')
@section('code', '419 — Page Expired')

@section('icon')
    <i class="fa-solid fa-clock-rotate-left text-2xl sm:text-3xl text-[#79766F]"></i>
@endsection

@section('headline', 'Sesi Formulir Telah Kedaluwarsa')

@section('message')
    Token keamanan (CSRF) pada halaman ini telah kedaluwarsa karena tidak ada aktivitas dalam beberapa waktu. Silakan muat ulang halaman untuk melanjutkan.
@endsection

@section('detail')
    <div class="text-center">
        <button type="button" onclick="window.location.reload()"
                class="px-4 py-2 rounded-lg bg-[#161616] text-white text-xs font-semibold hover:bg-[#262626] transition-colors">
            <i class="fa-solid fa-rotate-right mr-1.5 text-[#B89B5E]"></i> Muat Ulang Halaman (Refresh)
        </button>
    </div>
@endsection
