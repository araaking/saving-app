@extends('layouts.layout')

@section('title', 'Daftar Buku Tabungan')

@section('content')
<div class="container-fluid">

    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0">Daftar Buku Tabungan</h5>
                        <a href="{{ route('buku-tabungan.create') }}" class="btn btn-sm btn-success ms-auto">Tambah Buku Tabungan</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    
                                    <th>Siswa</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Nomor Urut</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bukuTabungans as $bukuTabungan)
                                    <tr>
                                        
                                        <td>{{ $bukuTabungan->siswa->nama }}</td>
                                        <td>{{ $bukuTabungan->tahunAjaran->nama }}</td>
                                        <td>{{ $bukuTabungan->nomor_urut }}</td>
                                        <td>
                                            <a href="{{ route('buku-tabungan.edit', $bukuTabungan->id) }}" 
                                               class="btn btn-icon btn-sm bg-warning-subtle me-1" 
                                               data-bs-toggle="tooltip" title="Edit">
                                                <i class="mdi mdi-pencil-outline fs-14 text-warning"></i>
                                            </a>
                                            <form action="{{ route('buku-tabungan.destroy', $bukuTabungan->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-icon btn-sm bg-danger-subtle" 
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus buku tabungan ini?')" 
                                                        data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="mdi mdi-delete fs-14 text-danger"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-0 border-top">
                    <div class="row align-items-center">
                        <div class="col-sm">
                            <div class="text-block text-center text-sm-start">
                                @if ($bukuTabungans->count() > 0)
                                <span class="fw-medium">
                                    Menampilkan {{ $bukuTabungans->count() }} data
                                </span>
                                @else
                                <span class="text-muted">Tidak ada data tersedia</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-auto mt-3 mt-sm-0">
                            {{ $bukuTabungans->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
