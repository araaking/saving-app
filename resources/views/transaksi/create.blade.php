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
                        <form action="{{ route('transaksi.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="kelas_id" class="form-label">Kelas</label>
                                <select name="kelas_id" id="kelas_id" class="form-select" required>
                                    <option value="">Pilih Kelas</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="buku_tabungan_id" class="form-label">Nomor Buku Tabungan</label>
                                <select name="buku_tabungan_id" id="buku_tabungan_id" class="form-select" required>
                                    <option value="">Pilih Nomor Buku Tabungan</option>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#kelas_id').change(function() {
            var kelasId = $(this).val();
            console.log('Kelas ID:', kelasId); // Debugging input kelas_id
            $.ajax({
                url: '/get-buku-tabungan-by-kelas/' + kelasId,
                type: 'GET',
                success: function(data) {
                    console.log('AJAX Response:', data); // Debugging response
                    $('#buku_tabungan_id').empty();
                    $('#buku_tabungan_id').append('<option value="">Pilih Nomor Buku Tabungan</option>');
                    $.each(data, function(index, bukuTabungan) {
                        $('#buku_tabungan_id').append('<option value="' + bukuTabungan.id + '">' + bukuTabungan.nomor_urut + ' - ' + bukuTabungan.siswa.nama + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText); // Debugging error
                }
            });
        });
    });
</script>
@endsection