<?php
include '../config/db.php'; // File untuk koneksi database
include '../config/session.php'; // Cek login
include 'header.php';
include 'navbar.php';

// Ambil informasi SweetAlert dari session
$sweet_alert = isset($_SESSION['sweet_alert']) ? $_SESSION['sweet_alert'] : null;
unset($_SESSION['sweet_alert']); // Hapus session setelah digunakan
?>

<main class="nxl-container">
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Supplier</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Data Supplier</li>
                </ul>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <!-- [ Main Content ] start -->
        <div class="main-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="leadList">
                                    <thead>
                                        <tr>
                                            <th>Kode Supplier</th>
                                            <th>Name Supplier</th>
                                            <th>Contact Person</th>
                                            <th>Phone</th>
                                            <th>WeChat</th>
                                            <th>Email</th>
                                            <th>Address</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch suppliers from the database
                                        $sql = "SELECT * FROM pemasok";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $wechatDisplay = !empty($row['wechat']) ? $row['wechat'] : '<span class="text-muted"><i>Tidak mempunyai wechat</i></span>';
                                                $emailDisplay = !empty($row['email']) ? $row['email'] : '<span class="text-muted"><i>Tidak mempunyai email</i></span>';

                                                echo "<tr>
                                                    <td>{$row['kode_pemasok']}</td>
                                                    <td>{$row['nama']}</td>
                                                    <td>{$row['kontak']}</td>
                                                    <td>{$row['telepon']}</td>
                                                    <td>{$wechatDisplay}</td>
                                                    <td>{$emailDisplay}</td>
                                                    <td>{$row['alamat']}</td>
                                                    <td class='text-end'>
                                                        <div class='hstack gap-2 justify-content-end'>
                                                            <a href='edit-supplier.php?kode_pemasok={$row['kode_pemasok']}' class='avatar-text avatar-md' title='Edit'>
                                                                <i class='feather-edit'></i>
                                                            </a>
                                                            <a href='#' class='avatar-text avatar-md delete-supplier' title='Delete' data-kode-pemasok='{$row['kode_pemasok']}'>
                                                                <i class='feather-trash-2'></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='8' class='text-center'>No suppliers found.</td></tr>";
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

<!-- Tambahkan SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
<?php if ($sweet_alert): ?>
Swal.fire({
    icon: '<?php echo $sweet_alert['type']; ?>',
    title: '<?php echo $sweet_alert['title']; ?>',
    text: '<?php echo $sweet_alert['text']; ?>',
    timer: 3000,
    timerProgressBar: true
});
<?php endif; ?>

document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-supplier');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const kode_pemasok = this.getAttribute('data-kode-pemasok');
            confirmDelete(kode_pemasok);
        });
    });
});

function confirmDelete(kode_pemasok) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('delete-supplier.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'kode_pemasok=' + encodeURIComponent(kode_pemasok)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire(
                        'Deleted!',
                        'Supplier has been deleted.',
                        'success'
                    ).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire(
                        'Error!',
                        'An error occurred while deleting the supplier.',
                        'error'
                    );
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                Swal.fire(
                    'Error!',
                    'An error occurred while processing your request.',
                    'error'
                );
            });
        }
    });
}
</script>