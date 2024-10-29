<!DOCTYPE html>
<html>
<head>
    <style>
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f0f0f0; }
        .header { text-align: center; margin-bottom: 20px; }
        .company-info { text-align: center; margin-bottom: 20px; }
        .signature-table { width: 100%; margin-top: 30px; }
        .signature-cell { width: 33%; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <img src="<?php echo K_PATH_IMAGES; ?>/logo.png" width="100">
        <h1>SAHARA TEKSTIL</h1>
        <p>Jl. Adipati Agung No13, Bandung, Jawa Barat</p>
        <p>Telp: (022) 1234567 | Email: info@saharatekstil.com</p>
    </div>

    <h2 style="text-align: center;">SURAT JALAN</h2>
    <p>Kode Penerimaan: <?php echo $kode_penerimaan; ?></p>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Color Detail ID</th>
                <th>Nama Barang</th>
                <th>Nama Motif</th>
                <th>GSM</th>
                <th>Width</th>
                <th>Roll</th>
                <th>Roll Length</th>
                <th>Small Roll</th>
                <th>Total Length</th>
                <th>Total Length with Small Roll</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach ($barang_details as $row): 
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($row['color_detail_id']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_motif']); ?></td>
                <td><?php echo htmlspecialchars($row['gsm']); ?></td>
                <td><?php echo htmlspecialchars($row['width']); ?></td>
                <td><?php echo htmlspecialchars($row['roll']); ?></td>
                <td><?php echo htmlspecialchars($row['roll_length']); ?></td>
                <td><?php echo htmlspecialchars($row['small_roll'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($row['total_length']); ?></td>
                <td><?php echo htmlspecialchars($row['total_length_with_small_roll']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="signature-table" border="0">
        <tr>
            <td class="signature-cell">
                <p>Yang mengeluarkan:</p>
                <br><br><br>
                <p>(_____________)</p>
            </td>
           
            
            <td class="signature-cell">
                <p>Yang Menerima:</p>
                <br><br><br>
                <p>(_____________)</p>
            </td>
        </tr>
    </table>
</body>
</html>