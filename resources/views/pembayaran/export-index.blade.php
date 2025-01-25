@extends('layouts.layout')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Export Data Pembayaran</h5>
            <form method="GET" class="d-flex gap-2">
                <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Filter Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ $selectedKelasId == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nomor Buku</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $siswa)
                    <tr>
                        <td>{{ $siswa->bukuTabungans->first()->nomor_urut ?? '-' }}</td>
                        <td>{{ $siswa->name }}</td>
                        <td>{{ $siswa->kelas->name }}</td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('pembayaran.export.pdf', [$siswa, 'preview' => true]) }}" 
                               class="btn btn-sm btn-info">
                               <i class="mdi mdi-eye"></i> Preview
                            </a>
                            <a href="{{ route('pembayaran.export.pdf', $siswa) }}" 
                               class="btn btn-sm btn-primary">
                               <i class="mdi mdi-download"></i> Download
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data siswa</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            {{ $siswas->links() }}
        </div>
    </div>
</div>
@endsection