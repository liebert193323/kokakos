<!DOCTYPE html>
<html>
<head>
    <title>Data Pendapatan</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Data Pendapatan</h1>
    <table>
        <thead>
            <tr>
                <th>Penyewa</th>
                <th>ID Pembayaran</th>
                <th>Jumlah</th>
                <th>Tipe</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incomes as $income)
                <tr>
                    <td>{{ $income->tenant->name }}</td>
                    <td>{{ $income->payment_id }}</td>
                    <td>Rp{{ number_format($income->amount, 0, ',', '.') }}</td>
                    <td>{{ $income->type }}</td>
                    <td>{{ $income->date->format('d/m/Y') }}</td>
                    <td>{{ $income->description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
