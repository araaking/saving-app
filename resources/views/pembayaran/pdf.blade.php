<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #2c3e50; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table th { background-color: #f8f9fa; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #e9ecef; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Tabungan Siswa</h1>
        <p>Nama: {{ $siswa->name }}</p>
        <p>Kelas: {{ $siswa->kelas->name }}</p>
        <p>Tahun Ajaran: {{ $tahunAktif->year_name }}</p>
    </div>

    <table class="table">
        <tr>
            <th colspan="2">Total Tabungan</th>
            <td class="text-right">Rp {{ number_format($totalTabungan, 0, ',', '.') }}</td>
        </tr>
        
        <tr class="total-row">
            <th colspan="2">Biaya Admin ({{ $biayaAdminPersen * 100 }}%)</th>
            <td class="text-right">Rp {{ number_format($biayaAdmin, 0, ',', '.') }}</td>
        </tr>

        <tr>
            <th colspan="3" class="bg-light">Rincian Potongan</th>
        </tr>
        
        @foreach($potongan as $item)
        <tr>
            <td colspan="2">{{ $item['jenis'] }}</td>
            <td class="text-right">Rp {{ number_format($item['jumlah'], 0, ',', '.') }}</td>
        </tr>
        @endforeach

        <tr class="total-row">
            <th colspan="2">Total Potongan</th>
            <td class="text-right">Rp {{ number_format($potongan->sum('jumlah'), 0, ',', '.') }}</td>
        </tr>

        <tr class="total-row">
            <th colspan="2">Sisa Tabungan</th>
            <td class="text-right">Rp {{ number_format(($totalTabungan - $biayaAdmin) - $potongan->sum('jumlah'), 0, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>