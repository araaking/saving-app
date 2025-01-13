@extends('layouts.layout')

@section('title', 'Kelola Tahun Ajaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                
            </div>

            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Tahun Ajaran</h4>
                    <!-- Tambahkan Tombol di Header Card -->
                    <a href="{{ route('tahun-ajaran.create') }}" class="btn btn-success">
                        Tambah Tahun Ajaran
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Tahun Ajaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tahunAjaran as $key => $tahun)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $tahun->nama }}</td>
                                    <td>
                                        <!-- Form Aksi -->
                                        <form action="{{ route('tahun-ajaran.destroy', $tahun->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus tahun ajaran ini?')">
                                                Hapus
                                            </button>
                                        </form>
                                        
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data tahun ajaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
