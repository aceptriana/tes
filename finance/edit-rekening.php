<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Check login session
include 'header.php';
include 'navbar.php';

// Check if an ID is provided to edit
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM rekening_bank WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $rekening = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>Rekening tidak ditemukan.</div>";
        exit;
    }
}

// Handle form submission for updating rekening
if (isset($_POST['update'])) {
    $nama_rekening = $_POST['nama_rekening'];
    $saldo = $_POST['saldo'];

    $update_sql = "UPDATE rekening_bank SET nama_rekening = ?, saldo = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $nama_rekening, $saldo, $id);

    if ($update_stmt->execute()) {
        echo "<script>
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Rekening berhasil diperbarui.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'rekening-bank.php';
                    }
                });
              </script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal memperbarui rekening.</div>";
    }
}
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Rekening</h5>
                </div>
            </div>
        </div>

        <!-- Form untuk mengedit rekening -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="edit-rekening.php?id=<?php echo $id; ?>" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_rekening" class="form-label">Nama Rekening</label>
                                <input type="text" class="form-control" id="nama_rekening" name="nama_rekening" value="<?php echo $rekening['nama_rekening']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="saldo" class="form-label">Saldo</label>
                                <input type="number" class="form-control" id="saldo" name="saldo" value="<?php echo $rekening['saldo']; ?>" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" name="update">Update Rekening</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Include SweetAlert2 library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include 'footer.php'; ?>
