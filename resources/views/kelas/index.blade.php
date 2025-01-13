@extends('layouts.layout')

@section('title', 'Daftar Kelas')

@section('content')
<div class="container-fluid">
    <!-- Card Wrapper -->
    <div class="card overflow-hidden mt-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h5 class="card-title mb-0">Daftar Kelas</h5>
                <a href="{{ route('kelas.create') }}" class="btn btn-success ms-auto">Tambah Kelas</a>
            </div>
        </div>
        <div class="card-body mt-0">
            <div class="table-responsive table-card mt-0">
                <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                    <thead class="text-muted table-light">
                        <tr>
                            <th scope="col" class="cursor-pointer">ID</th>
                            <th scope="col" class="cursor-pointer">Nama Kelas</th>
                            <th scope="col" class="cursor-pointer">Total Murid</th>
                            
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kelas as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->nama }}</td>
                                <!-- Jika relasi siswa belum ter-load atau bernilai null, tampilkan 0 -->
                                <td>{{ $item->siswa ? $item->siswa->count() : 0 }}</td>
                                <!-- Menjumlahkan total_tabungan dari seluruh siswa; gunakan number_format() untuk format 2 desimal -->
                                
                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- end table-responsive -->
        </div><!-- end card-body -->
    </div><!-- end card -->
</div><!-- end container-fluid -->
@endsection
