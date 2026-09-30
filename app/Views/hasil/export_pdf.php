<!DOCTYPE html>
<html>
<head>
    <title>Laporan Apriori Asiafarma</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .meta { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN HASIL ANALISIS APRIORI</h2>
        <h3>Toko Asiafarma</h3>
    </div>

    <div class="meta">
        <p>ID Hasil: #<?= $header['id'] ?></p>
        <p>Tanggal Proses: <?= $header['tanggal_proses'] ?></p>
        <p>Min. Support: <?= $header['min_support'] ?>% | Min. Confidence: <?= $header['min_confidence'] ?>%</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Jika Beli (Antecedent)</th>
                <th>Maka Beli (Consequent)</th>
                <th>Support</th>
                <th>Confidence</th>
                <th>Lift</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; foreach($rules as $rule): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= implode(', ', json_decode($rule['antecedent'])) ?></td>
                <td><?= implode(', ', json_decode($rule['consequent'])) ?></td>
                <td><?= number_format($rule['support'], 2) ?>%</td>
                <td><?= number_format($rule['confidence'], 2) ?>%</td>
                <td><?= number_format($rule['lift'], 3) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>