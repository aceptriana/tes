<?php
//data-rak.php
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
                    <h5 class="m-b-10">Rak</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Data Rak</li>
                </ul>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <!-- [ Main Content ] start -->
        <div class="main-content">
        <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'success') {
                    echo "<div class='alert alert-success'>Rak successfully added.</div>";
                } elseif ($_GET['status'] == 'updated') {
                    echo "<div class='alert alert-success'>Rak successfully updated.</div>";
                } elseif ($_GET['status'] == 'deleted') {
                    echo "<div class='alert alert-success'>Rak successfully deleted.</div>";
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
                                <table class="table table-hover" id="leadList">
                                    <thead>
                                        <tr>
                                            <th>Kode Rak</th>
                                            <th>Kode Barang</th>
                                            <th>Lokasi Rak</th>
                                            <th>Nomer Rak</th>
                                            <th>Nama Barang</th>
                                            <th>Keterangan Barang</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch suppliers from the database
                                        $sql = "SELECT * FROM penyimpanan_barang";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>
        
                                                       <td>{$row['kode_penyimpanan']}</td>
                                                    <td>{$row['kode_stok']}</td>
                                                    <td>{$row['lokasi_penyimpanan']}</td>
                                                    <td>{$row['nomer_penyimpanan']}</td>
                                                    <td>{$row['nama_barang']}</td>
                                                    <td>{$row['keterangan_barang']}</td>
                                                    <td class='text-end'>
                                                        <div class='hstack gap-2 justify-content-end'>
                                                            <a href='view-rak.php?id={$row['kode_penyimpanan']}' class='avatar-text avatar-md' title='View'>
                                                                <i class='feather-eye'></i>
                                                            </a>
                                                            <a href='edit-rak.php?kode_penyimpanan={$row['kode_penyimpanan']}' class='avatar-text avatar-md' title='Edit'>
                                                                <i class='feather-edit'></i>
                                                            </a>
                                                    
                                                                 <a href='#' class='avatar-text avatar-md delete-rak' title='Delete' data-kode-penyimpanan_barang='{$row['kode_penyimpanan']}'>
                                                                <i class='feather-trash-2'></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='7' class='text-center'>No suppliers found.</td></tr>";
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
    // Select all delete buttons for rak
    const deleteButtons = document.querySelectorAll('.delete-rak');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const kode_penyimpanan = this.getAttribute('data-kode-penyimpanan_barang');
            confirmDelete(kode_penyimpanan); // Call the delete confirmation function
        });
    });
});

// Function to confirm rak deletion
function confirmDelete(kode_penyimpanan) {
    if (confirm("Apakah Anda yakin ingin menghapus rak ini?")) {
        fetch('delete-rak.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'kode_penyimpanan=' + encodeURIComponent(kode_penyimpanan)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = 'data-rak.php?status=deleted'; // Redirect if successful
            } else {
                window.location.href = 'data-rak.php?status=error'; // Redirect if failed
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            window.location.href = 'data-rak.php?status=error'; // Redirect if an error occurs
        });
    }
}
</script>
