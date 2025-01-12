<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Tambah Transaksi</h1>
        <form action="{{ route('transaksi.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="kelas_id">Kelas</label>
                <select name="kelas_id" id="kelas_id" class="form-control" required>
                    <option value="">Pilih Kelas</option>
                    @foreach ($kelas as $k)
                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="buku_tabungan_id">Nomor Buku Tabungan</label>
                <select name="buku_tabungan_id" id="buku_tabungan_id" class="form-control" required>
                    <option value="">Pilih Nomor Buku Tabungan</option>
                </select>
            </div>
            <div class="form-group">
                <label for="simpanan">Simpanan</label>
                <input type="number" name="simpanan" id="simpanan" class="form-control" step="0.01">
            </div>
            <div class="form-group">
                <label for="cicilan">Cicilan</label>
                <input type="number" name="cicilan" id="cicilan" class="form-control" step="0.01">
            </div>
            <div class="form-group">
                <label for="penarikan">Penarikan</label>
                <input type="number" name="penarikan" id="penarikan" class="form-control" step="0.01">
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>

    <script>
        // Script untuk filter Buku Tabungan berdasarkan Kelas
        $(document).ready(function() {
            $('#kelas_id').change(function() {
                var kelasId = $(this).val();
                $.ajax({
                    url: '/get-buku-tabungan-by-kelas/' + kelasId,
                    type: 'GET',
                    success: function(data) {
                        $('#buku_tabungan_id').empty();
                        $('#buku_tabungan_id').append('<option value="">Pilih Nomor Buku Tabungan</option>');
                        $.each(data, function(index, bukuTabungan) {
                            $('#buku_tabungan_id').append('<option value="' + bukuTabungan.id + '">' + bukuTabungan.nomor_urut + ' - ' + bukuTabungan.siswa.nama + '</option>');
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>