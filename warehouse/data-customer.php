<?php
//data-customer.php
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
                    <h5 class="m-b-10">Data Preorder Customer</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Data Customer</li>
                </ul>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <!-- [ Main Content ] start -->
        <div class="main-content">
        <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'success') {
                echo "<div class='alert alert-success'>Preorder successfully added.</div>";
            } elseif ($_GET['status'] == 'updated') {
                echo "<div class='alert alert-success'>Preorder successfully updated.</div>";
            } elseif ($_GET['status'] == 'deleted') {
                echo "<div class='alert alert-success'>Preorder successfully deleted.</div>";
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
                                <table class="table table-hover" id="customerList">
                                    <thead>
                                        <tr>
                                            <th>Kode Preorder Customer</th>
                                            <th>Kode Stok Barang</th>
                                            <th>Tanggal Pesan</th>
                                            <th>Tanggal Dikirim</th>
                                            <th>Nama</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch preorder customer data from the database
                                        $sql = "SELECT pc.*, 
                                                       sb.nama AS nama_barang
                                                FROM preorder_customer pc
                                                LEFT JOIN stok_barang sb ON pc.kode_stok_barang = sb.kode_stok_barang";

                                        $result = $conn->query($sql);

                                        if ($result === false) {
                                            // Query failed
                                            echo "<tr><td colspan='6' class='text-center'>Error executing query: " . $conn->error . "</td></tr>";
                                        } elseif ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>
                                                    <td>{$row['kode_preorder_customer']}</td>
                                                    <td>{$row['kode_stok_barang']} - {$row['nama_barang']}</td>
                                                    <td>{$row['tanggal_pesan']}</td>
                                                    <td>{$row['tanggal_dikirim']}</td>
                                                    <td>{$row['nama']}</td>
                                                    <td>{$row['status']}</td>
                                                    <td class='text-end'>
                                                        <div class='hstack gap-2 justify-content-end'>
                                                            <a href='view-po-customer.php?id={$row['kode_preorder_customer']}' class='avatar-text avatar-md' title='View'>
                                                                <i class='feather-eye'></i>
                                                            </a>
                                                            <a href='edit-po-customer.php?kode_preorder_customer={$row['kode_preorder_customer']}' class='avatar-text avatar-md' title='Edit'>
                                                                <i class='feather-edit'></i>
                                                            </a>
                                                             <a href='#' class='avatar-text avatar-md delete-customer' title='Delete' data-kode-preorder-customer='{$row['kode_preorder_customer']}'>
                                                                <i class='feather-trash-2'></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6' class='text-center'>Tidak ada data preorder customer ditemukan.</td></tr>";
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
    const deleteButtons = document.querySelectorAll('.delete-customer');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const kode_preorder_customer = this.getAttribute('data-kode-preorder-customer');
            confirmDelete(kode_preorder_customer);
        });
    });
});

function confirmDelete(kode_preorder_customer) {
    if (confirm("Are you sure you want to delete this preorder customer?")) {
        fetch('delete-po-customer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'kode_preorder_customer=' + encodeURIComponent(kode_preorder_customer)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                window.location.href = 'data-customer.php?status=deleted';
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