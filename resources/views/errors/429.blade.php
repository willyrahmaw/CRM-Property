@extends('errors.layout')

@section('title', 'Terlalu Banyak Permintaan — 429 Too Many Requests')
@section('code', '429 — Rate Limited')

@section('icon')
    <i class="fa-solid fa-gauge-high text-2xl sm:text-3xl text-[#B89B5E]"></i>
@endsection

@section('headline', 'Batas Frekuensi Akses Tercapai')

@section('message')
    Sistem mendeteksi terlalu banyak permintaan dalam waktu singkat dari perangkat Anda. Demi keamanan data dan stabilitas performa CRM, silakan tunggu beberapa saat sebelum mencoba kembali.
@endsection
