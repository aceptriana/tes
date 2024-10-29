<?php
include '../config/db.php'; // Koneksi database
include '../config/session.php'; // Cek login

if (isset($_GET['kode_penyimpanan'])) {
    $kode_penyimpanan = $_GET['kode_penyimpanan'];
    
    // Ambil detail barang dari rak
    $sql = "SELECT * FROM penyimpanan_barang WHERE kode_penyimpanan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kode_penyimpanan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $barang = $result->fetch_assoc();
    } else {
        echo "Barang tidak ditemukan.";
        exit;
    }
} else {
    echo "Kode penyimpanan tidak diberikan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jumlah_keluar = $_POST['jumlah_keluar'];
    $keterangan_keluar = $_POST['keterangan_keluar'];

    // Pastikan jumlah yang akan dikeluarkan tidak melebihi stok
    if ($jumlah_keluar > $barang['jumlah']) {
        echo "Jumlah keluar melebihi stok yang ada.";
        exit;
    }

    // Proses pengurangan stok
    $jumlah_baru = $barang['jumlah'] - $jumlah_keluar;
    $update_sql = "UPDATE penyimpanan_barang SET jumlah = ? WHERE kode_penyimpanan = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("is", $jumlah_baru, $kode_penyimpanan);

    if ($update_stmt->execute()) {
        // Catat pengeluaran ke dalam tabel pengeluaran_barang
        $insert_sql = "INSERT INTO pengeluaran_barang (kode_penyimpanan, jumlah_keluar, keterangan) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sis", $kode_penyimpanan, $jumlah_keluar, $keterangan_keluar);
        $insert_stmt->execute();

        echo "Barang berhasil dikeluarkan.";
        header("Location: keluar-rak.php?status=success");
    } else {
        echo "Gagal mengeluarkan barang.";
    }
}
?>

<form method="POST" action="">
    <label for="nama_barang">Nama Barang:</label>
    <input type="text" name="nama_barang" value="<?php echo $barang['nama_barang']; ?>" disabled>
    
    <label for="jumlah_keluar">Jumlah Keluar:</label>
    <input type="number" name="jumlah_keluar" min="1" max="<?php echo $barang['jumlah']; ?>" required>

    <label for="keterangan_keluar">Keterangan Pengeluaran:</label>
    <textarea name="keterangan_keluar" required></textarea>

    <button type="submit">Keluar Barang</button>
</form>
