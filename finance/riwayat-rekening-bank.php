<?php
include '../config/db.php'; // Koneksi ke database

// Query untuk menampilkan semua rekening dan riwayat saldo mingguan
$sql = "SELECT nama_rekening, minggu_ke, bulan, tahun, saldo_awal_perminggu, saldo_akhir_perminggu
        FROM saldo_mingguan
        ORDER BY nama_rekening, tahun, bulan, minggu_ke";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nama Rekening</th>
            <th>Minggu Ke</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Saldo Awal</th>
            <th>Saldo Akhir</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['nama_rekening']) . "</td>
                    <td>{$row['minggu_ke']}</td>
                    <td>{$row['bulan']}</td>
                    <td>{$row['tahun']}</td>
                    <td>" . number_format($row['saldo_awal_perminggu'], 2, ',', '.') . "</td>
                    <td>" . number_format($row['saldo_akhir_perminggu'], 2, ',', '.') . "</td>
                  </tr>";
        }
        ?>
    </tbody>
</table>
