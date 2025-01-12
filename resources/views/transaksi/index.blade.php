<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Daftar Transaksi</h1>
        <a href="/transaksi/create" class="btn btn-primary mb-3">Tambah Transaksi</a>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Buku Tabungan</th>
                    <th>Nama Siswa</th> <!-- Kolom baru: Nama Siswa -->
                    <th>Kelas</th> <!-- Kolom baru: Kelas -->
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaksis as $transaksi)
                <tr>
                    <td>{{ $transaksi->id }}</td>
                    <td>{{ $transaksi->bukuTabungan->nomor_urut }}</td>
                    <td>{{ $transaksi->bukuTabungan->siswa->nama }}</td> <!-- Nama Siswa -->
                    <td>{{ $transaksi->bukuTabungan->siswa->kelas->nama }}</td> <!-- Kelas -->
                    <td>{{ ucfirst($transaksi->jenis) }}</td>
                    <td>{{ number_format($transaksi->jumlah, 2) }}</td>
                    <td>{{ $transaksi->tanggal->format('d/m/Y H:i:s') }}</td> <!-- Format tanggal -->
                    <td>{{ $transaksi->keterangan }}</td>
                    <td>
                        <a href="/transaksi/{{ $transaksi->id }}/edit" class="btn btn-warning">Edit</a>
                        <form action="/transaksi/{{ $transaksi->id }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>