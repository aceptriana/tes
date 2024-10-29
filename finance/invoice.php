<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Include session management
include 'header.php';
include 'navbar.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $no_faktur = $_POST['no_faktur'];
    $buyer_name = $_POST['buyer_name'];
    $buyer_address = $_POST['buyer_address'];
    $buyer_phone = $_POST['buyer_phone'];
    $buyer_wa = $_POST['buyer_wa'];
    $buyer_npwp = $_POST['buyer_npwp'];
    $item_description = $_POST['item_description'];
    $item_quantity = $_POST['item_quantity'];
    $item_price = $_POST['item_price'];
    $total_price = $_POST['total_price'];
    $payment_info = $_POST['payment_info'];
    $notes = $_POST['notes'];

    // Insert data into invoice table
    $sql = "INSERT INTO invoices (no_faktur, buyer_name, buyer_address, buyer_phone, buyer_wa, buyer_npwp, item_description, item_quantity, item_price, total_price, payment_info, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssssssss', $no_faktur, $buyer_name, $buyer_address, $buyer_phone, $buyer_wa, $buyer_npwp, $item_description, $item_quantity, $item_price, $total_price, $payment_info, $notes);

    if ($stmt->execute()) {
        echo "<script>alert('Invoice created successfully!');</script>";
    } else {
        echo "<script>alert('Error creating invoice: " . $conn->error . "');</script>";
    }
}

// Fetch all invoices
$sql_invoices = "SELECT * FROM invoices";
$result_invoices = $conn->query($sql_invoices);
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Create Invoice</h5>
                </div>
            </div>
        </div>

        <!-- Form to Create Invoice -->
        <div class="main-content">
            <div class="card">
                <div class="card-body">
                    <form action="invoice.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="no_faktur" class="form-label">No Faktur</label>
                                <input type="text" class="form-control" id="no_faktur" name="no_faktur" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="buyer_name" class="form-label">Nama Pembeli</label>
                                <input type="text" class="form-control" id="buyer_name" name="buyer_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="buyer_address" class="form-label">Alamat Pembeli</label>
                                <textarea class="form-control" id="buyer_address" name="buyer_address" rows="3" required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="buyer_phone" class="form-label">No Telepon Pembeli</label>
                                <input type="text" class="form-control" id="buyer_phone" name="buyer_phone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="buyer_wa" class="form-label">WhatsApp Pembeli</label>
                                <input type="text" class="form-control" id="buyer_wa" name="buyer_wa">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="buyer_npwp" class="form-label">NPWP Pembeli</label>
                                <input type="text" class="form-control" id="buyer_npwp" name="buyer_npwp">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="item_description" class="form-label">Deskripsi Barang</label>
                                <input type="text" class="form-control" id="item_description" name="item_description" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="item_quantity" class="form-label">Kuantitas (Meter/Roll)</label>
                                <input type="text" class="form-control" id="item_quantity" name="item_quantity" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="item_price" class="form-label">Harga per Meter</label>
                                <input type="text" class="form-control" id="item_price" name="item_price" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="total_price" class="form-label">Total Harga setelah PPn</label>
                                <input type="text" class="form-control" id="total_price" name="total_price" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="payment_info" class="form-label">Informasi Pembayaran (Nama Rekening, No. Rekening)</label>
                                <textarea class="form-control" id="payment_info" name="payment_info" rows="3" required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="notes" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Buat Invoice</button>
                        <a href="generate-invoice-pdf.php?invoice_id=<?php echo $conn->insert_id; ?>" class="btn btn-success">Print Invoice</a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Invoice List -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="m-b-10">Daftar Invoice</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Faktur</th>
                                <th>Nama Pembeli</th>
                                <th>Total Harga</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result_invoices->num_rows > 0) {
                                $no = 1;
                                while ($row = $result_invoices->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$no}</td>
                                            <td>{$row['no_faktur']}</td>
                                            <td>{$row['buyer_name']}</td>
                                            <td>" . number_format($row['total_price'], 2) . "</td>
                                            <td>" . date('d-M-Y', strtotime($row['created_at'])) . "</td>
                                            <td>
                                                <a href='generate-invoice-pdf.php?invoice_id={$row['invoice_id']}' class='btn btn-info btn-sm'>Print</a>
                                            </td>
                                          </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No invoices found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
