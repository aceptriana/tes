<?php
// data-po-customer.php
include '../config/db.php';
include '../config/session.php';
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
                                            <th>ID</th>
                                            <th>Kode Penerimaan</th>
                                            <th>Color Detail</th>
                                            <th>Nama Barang</th>
                                            <th>Nama Motif</th>
                                            <th>GSM</th>
                                            <th>Width</th>
                                            <th>Roll</th>
                                            <th>Roll Length</th>
                                            <th>Small Roll</th>
                                            <th>Total Length</th>
                                            <th>Total Length with Small Roll</th>
                                            <th>Keterangan Barang</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch PO customer data from the database
                                        $sql = "SELECT pc.*, cd.name as color_detail_name 
                                                FROM po_customer pc 
                                                LEFT JOIN color_details cd ON pc.color_detail_id = cd.id";

                                        $result = $conn->query($sql);

                                        if ($result === false) {
                                            echo "<tr><td colspan='15' class='text-center'>Error executing query: " . $conn->error . "</td></tr>";
                                        } elseif ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>
                                                    <td>{$row['id']}</td>
                                                    <td>{$row['kode_penerimaan']}</td>
                                                    <td>{$row['color_detail_name']}</td>
                                                    <td>{$row['nama_barang']}</td>
                                                    <td>{$row['nama_motif']}</td>
                                                    <td>{$row['gsm']}</td>
                                                    <td>{$row['width_cm']}</td>
                                                    <td>{$row['roll']}</td>
                                                    <td>{$row['roll_length']}</td>
                                                    <td>{$row['small_roll']}</td>
                                                    <td>{$row['total_length']}</td>
                                                    <td>{$row['total_length_with_small_roll']}</td>
                                                    <td>{$row['keterangan_barang']}</td>
                                                    <td>{$row['status']}</td>
                                                    <td class='text-end'>
                                                        <div class='hstack gap-2 justify-content-end'>
                                                            <a href='view-po-customer.php?id={$row['id']}' class='avatar-text avatar-md' title='View'>
                                                                <i class='feather-eye'></i>
                                                            </a>
                                                            <a href='edit-po-customer.php?id={$row['id']}' class='avatar-text avatar-md' title='Edit'>
                                                                <i class='feather-edit'></i>
                                                            </a>
                                                            <a href='#' class='avatar-text avatar-md delete-customer' title='Delete' data-id='{$row['id']}'>
                                                                <i class='feather-trash-2'></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='15' class='text-center'>Tidak ada data preorder customer ditemukan.</td></tr>";
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
            const id = this.getAttribute('data-id');
            confirmDelete(id);
        });
    });
});

function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this preorder customer?")) {
        fetch('delete-po-customer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + encodeURIComponent(id)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                window.location.href = 'data-po-customer.php?status=deleted';
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