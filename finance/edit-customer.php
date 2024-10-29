<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Initialize feedback variable
$feedback = '';

if (!isset($_GET['id'])) {
    header('Location: data-customer.php?status=error'); // Redirect if no ID is provided
    exit();
}

$customer_id = $_GET['id'];

// Fetch customer data
$sql = "SELECT * FROM customers WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: data-customer.php?status=not_found'); // Redirect if customer not found
    exit();
}

$customer = $result->fetch_assoc();

// Handle form submission for updating customer details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $credit_limit = $_POST['credit_limit']; // Get the credit limit from the form

    // Update customer data including credit limit
    $update_sql = "UPDATE customers SET customer_name = ?, contact_person = ?, phone = ?, address = ?, credit_limit = ? WHERE customer_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('ssssdi', $customer_name, $contact_person, $phone, $address, $credit_limit, $customer_id); // Bind credit limit as double

    if ($update_stmt->execute()) {
        $feedback = 'update_success'; // Set feedback for success
    } else {
        $feedback = 'error'; // Set feedback for error
    }
}
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Customer</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Data Customer</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="edit-customer.php?id=<?php echo $customer_id; ?>" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_name" class="form-label">Nama Customer</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo $customer['customer_name']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contact_person" class="form-label">Kontak Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?php echo $customer['contact_person']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $customer['phone']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Alamat</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $customer['address']; ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="credit_limit" class="form-label">Limit Piutang</label>
                                <input type="number" class="form-control" id="credit_limit" name="credit_limit" value="<?php echo $customer['credit_limit']; ?>" step="0.01" min="0" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Customer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- SweetAlert script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($feedback == 'update_success') : ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Customer berhasil diperbarui!',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'data-customer.php'; // Redirect to customer list after clicking OK
            }
        });
    </script>
<?php elseif ($feedback == 'error') : ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan!',
            text: 'Kesalahan saat memperbarui customer.',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'edit-customer.php?id=<?php echo $customer_id; ?>'; // Stay on edit page after clicking OK
            }
        });
    </script>
<?php endif; ?>

<?php include 'footer.php'; ?>
