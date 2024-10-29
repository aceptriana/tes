<?php
//data-barang.php
include '../config/db.php'; // File untuk koneksi database
include '../config/session.php'; // Cek login
include 'header.php';
include 'navbar.php';
?>

<main class="nxl-container">
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Data Stok Barang</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Data Barang</li>
                </ul>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <!-- [ Main Content ] start -->
        <div class="main-content">
        <?php
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        echo "<div class='alert alert-success'>Stok successfully added.</div>";
    } elseif ($_GET['status'] == 'updated') {
        echo "<div class='alert alert-success'>Stok successfully updated.</div>";
    } elseif ($_GET['status'] == 'deleted') {
        echo "<div class='alert alert-success'>Stok successfully deleted.</div>";
    } elseif ($_GET['status'] == 'error') {
        echo "<div class='alert alert-danger'>An error occurred.</div>";
    }
}
?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="barangList">
                                    <thead>
                                        <tr>
                                            <th>Kode Stok Barang</th>
                                            <th>Kode Nota</th>
                                            <th>Nama</th>
                                            <th>Deskripsi Barang</th>
                                            <th>Tanggal Masuk Gudang</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch stok barang from the database
                                        $sql = "SELECT * FROM stok_barang";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>
                                                    <td>{$row['kode_stok_barang']}</td>
                                                    <td>{$row['kode_nota']}</td>
                                                    <td>{$row['nama']}</td>
                                                    <td>{$row['deskripsi_barang']}</td>
                                                    <td>{$row['tanggal_masuk_gudang']}</td>
                                                    <td class='text-end'>
                                                        <div class='hstack gap-2 justify-content-end'>
                                                            <a href='view-barang.php?id={$row['kode_stok_barang']}' class='avatar-text avatar-md' title='View'>
                                                                <i class='feather-eye'></i>
                                                            </a>
                                                            <a href='edit-barang.php?kode_stok_barang={$row['kode_stok_barang']}' class='avatar-text avatar-md' title='Edit'>
                                                                <i class='feather-edit'></i>
                                                            </a>
                                                             <a href='#' class='avatar-text avatar-md delete-barang' title='Delete' data-kode-stok-barang='{$row['kode_stok_barang']}'>
                                                                <i class='feather-trash-2'></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='11' class='text-center'>Tidak ada data barang ditemukan.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</main>

<?php include 'footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-barang');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const kode_stok_barang = this.getAttribute('data-kode-stok-barang');
            confirmDelete(kode_stok_barang);
        });
    });
});

function confirmDelete(kode_stok_barang) {
    if (confirm("Are you sure you want to delete this stok barang?")) {
        fetch('delete-barang.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'kode_stok_barang=' + encodeURIComponent(kode_stok_barang)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                window.location.href = 'data-barang.php?status=deleted';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('An error occurred while deleting the item.');
        });
    }
}
</script>