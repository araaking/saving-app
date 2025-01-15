@extends('layouts.layout')

@section('title', 'Tambah Transaksi')

@section('content')

<div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tambah Transaksi</h5>
            </div>
<div class="card-body">
    
    
    <div class="row">
        <div class="col-lg-6">
        <form action="{{ route('transaksi.create') }}" method="GET">
                <div class="mb-3">
                    <label for="kelas_id" class="form-label">Filter Kelas</label>
                    <select name="kelas_id" id="kelas_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
            <form action="{{ route('transaksi.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="buku_tabungan_id" class="form-label">Nomor Buku Tabungan - Nama Siswa</label>
                                <select name="buku_tabungan_id" id="buku_tabungan_id" class="form-select" required>
                                    <option value="">Pilih Nomor Buku Tabungan - Nama Siswa</option>
                                    @foreach ($bukuTabungans as $bukuTabungan)
                                        <option value="{{ $bukuTabungan->id }}">
                                            {{ $bukuTabungan->nomor_urut }} - {{ $bukuTabungan->siswa->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="simpanan" class="form-label">Simpanan</label>
                                <input type="number" name="simpanan" id="simpanan" class="form-control" step="0.01">
                            </div>
                    </div>

                    <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="cicilan" class="form-label">Cicilan</label>
                                <input type="number" name="cicilan" id="cicilan" class="form-control" step="0.01">
                            </div>

                            <div class="mb-3">
                                <label for="penarikan" class="form-label">Penarikan</label>
                                <input type="number" name="penarikan" id="penarikan" class="form-control" step="0.01">
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="5"></textarea>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
