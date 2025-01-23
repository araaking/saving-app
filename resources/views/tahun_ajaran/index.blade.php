@extends('layouts.layout')

@section('title', 'Kelola Tahun Ajaran')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tahun Ajaran</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Success Message with Promotion Info -->
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                    @if(session('promotion_info'))
                        <hr>
                        <div class="mt-2">
                            <strong>Detail Kenaikan Kelas:</strong>
                            <ul class="mb-0">
                                @foreach(session('promotion_info') as $info)
                                    <li>{{ $info }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Main Card -->
            <div class="card">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Tahun Ajaran</h4>
                    <a href="{{ route('tahun-ajaran.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah Tahun Ajaran
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <!-- Tabel -->
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Tahun Ajaran</th>
                                <th>Status</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tahunAjaran as $key => $tahun)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $tahun->year_name }}</td>
                                    <td>
                                        @if($tahun->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Tombol Edit -->
                                        <a href="{{ route('tahun-ajaran.edit', $tahun->id) }}" 
                                           class="btn btn-warning btn-sm"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('tahun-ajaran.destroy', $tahun->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Hapus tahun ajaran ini akan MENGHAPUS SELURUH SISWA di tahun ini. Yakin?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-danger btn-sm"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data tahun ajaran.</td>
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