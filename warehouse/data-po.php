<?php
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
                    <h5 class="m-b-10">Data Pre-Order</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Data Pre-Order</li>
                </ul>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <!-- [ Main Content ] start -->
        <div class="main-content">
        <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'success') {
                    echo "<div class='alert alert-success'>Pre Order successfully added.</div>";
                } elseif ($_GET['status'] == 'updated') {
                    echo "<div class='alert alert-success'>Pre Order successfully updated.</div>";
                } elseif ($_GET['status'] == 'deleted') {
                    echo "<div class='alert alert-success'>Pre Order successfully deleted.</div>";
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
                                <table class="table table-hover" id="poList">
                                    <thead>
                                        <tr>
                                            <th>Kode Prapesan</th>
                                            <th>Kode Pemasok</th>
                                            <th>Tanggal Pesan</th>
                                            <th>Tanggal Dikirim</th>
                                            <th>Nama</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch pre-orders from the database
                                        $sql = "SELECT * FROM prapesan";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>
                                                    <td>{$row['kode_prapesan']}</td>
                                                    <td>{$row['kode_pemasok']}</td>
                                                    <td>{$row['tanggal_pesan']}</td>
                                                    <td>{$row['tanggal_dikirim']}</td>
                                                    <td>{$row['nama']}</td>
                                                     <td>{$row['status']}</td>
                                                    <td class='text-end'>
                                                        <div class='hstack gap-2 justify-content-end'>
                                                             <a href='view-po.php?id={$row['kode_prapesan']}' class='avatar-text avatar-md' title='View'>
                                                                <i class='feather-eye'></i>
                                                            </a>
                                                            <a href='edit-po.php?kode_prapesan={$row['kode_prapesan']}' class='avatar-text avatar-md' title='Edit'>
                                                                <i class='feather-edit'></i>
                                                            </a>
                                                            <a href='#' class='avatar-text avatar-md delete-po' title='Delete' data-kode-prapesan='{$row['kode_prapesan']}'>
                                                                <i class='feather-trash-2'></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='12' class='text-center'>No pre-orders found.</td></tr>";
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
    const deleteButtons = document.querySelectorAll('.delete-po');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const kode_prapesan = this.getAttribute('data-kode-prapesan');
            confirmDelete(kode_prapesan);
        });
    });
});

function confirmDelete(kode_prapesan) {
    if (confirm("Are you sure you want to delete this pre-order?")) {
        fetch('delete-po.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'kode_prapesan=' + encodeURIComponent(kode_prapesan)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = 'data-po.php?status=deleted';
            } else {
                window.location.href = 'data-po.php?status=error';
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            window.location.href = 'data-po.php?status=error';
        });
    }
}
</script>