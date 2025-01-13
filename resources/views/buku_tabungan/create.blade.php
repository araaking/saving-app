@extends('layouts.layout')

@section('title', 'Tambah Buku Tabungan')

@section('content')
<div class="container-fluid">
    <div class="card mt-4">
        <div class="card-header">
            <h4 class="card-title">Tambah Buku Tabungan</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('buku-tabungan.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="siswa_id" class="form-label">Siswa:</label>
                    <select id="siswa_id" name="siswa_id" class="form-select" required>
                        @foreach($siswas as $siswa)
                            <option value="{{ $siswa->id }}">{{ $siswa->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran:</label>
                    <select id="tahun_ajaran_id" name="tahun_ajaran_id" class="form-select" required>
                        @foreach($tahunAjarans as $tahunAjaran)
                            <option value="{{ $tahunAjaran->id }}">{{ $tahunAjaran->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="nomor_urut" class="form-label">Nomor Urut:</label>
                    <input type="number" id="nomor_urut" name="nomor_urut" class="form-control" required>
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('buku-tabungan.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
