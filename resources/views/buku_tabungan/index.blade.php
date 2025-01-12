<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku Tabungan</title>
</head>
<body>
    <h1>Daftar Buku Tabungan</h1>
    <a href="{{ route('buku_tabungan.create') }}">Tambah Buku Tabungan</a>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Siswa</th>
                <th>Tahun Ajaran</th>
                <th>Nomor Urut</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bukuTabungans as $bukuTabungan)
                <tr>
                    <td>{{ $bukuTabungan->id }}</td>
                    <td>{{ $bukuTabungan->siswa->nama }}</td>
                    <td>{{ $bukuTabungan->tahunAjaran->nama }}</td>
                    <td>{{ $bukuTabungan->nomor_urut }}</td>
                    <td>
                        <a href="{{ route('buku_tabungan.edit', $bukuTabungan->id) }}">Edit</a>
                        <form action="{{ route('buku_tabungan.destroy', $bukuTabungan->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus buku tabungan ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>