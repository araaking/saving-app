<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kelas</title>
</head>
<body>
    <h1>Daftar Kelas</h1>
    <a href="{{ route('kelas.create') }}">Tambah Kelas</a>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Kelas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kelas as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->nama }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>