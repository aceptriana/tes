<?php
ob_start(); // Start output buffering

include '../config/db.php'; // Include database connection
include '../config/session.php'; // Check session login
include 'header.php';
include 'navbar.php';
?>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Transaksi Barang</h5>
                </div>
            </div>
        </div>
        <form method="POST" action="proses-transaksi-barang.php">
            <div class="main-content">
                <div class="row">
                    <div class="col-xl-8">
                        <div class="card invoice-container">
                            <div class="card-header">
                                <h5>Buat Invoice</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="px-4 pt-4">
                                    <div class="d-md-flex align-items-center justify-content-between"></div>
                                </div>
                                <hr class="border-dashed">
                                <div class="px-4 row justify-content-between">
                                    <div class="col-xl-3">
                                        <div class="form-group mb-3">
                                            <label for="tanggal_transaksi" class="form-label">Tanggal Penerbitan:</label>
                                            <input type="date" class="form-control" id="tanggal_transaksi" name="tanggal_transaksi" required>
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="form-group mb-3">
                                            <label for="tanggal_tempo" class="form-label">Jatuh Tempo:</label>
                                            <input type="date" class="form-control" id="tanggal_tempo" name="tanggal_tempo" required>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group mb-3">
                                            <label for="InvoiceProduct" class="form-label">Kode Invoice</label>
                                            <input type="text" class="form-control" value="<?php echo '#INV' . date('YmdHis'); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <hr class="border-dashed">
                                <div class="row px-4 justify-content-between">
                                    <div class="col-xl-5 mb-4 mb-sm-0">
                                        <div class="mb-4">
                                            <h6 class="fw-bold">Invoice Dari:</h6>
                                            <span class="fs-12 text-muted">Kirimkan faktur dan terima pembayaran</span>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label for="InvoiceName" class="col-sm-3 col-form-label">Nama</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="InvoiceName" value="<?php echo $user_name; ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label for="InvoicePhone" class="col-sm-3 col-form-label">Telepon</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="InvoicePhone" value="<?php echo $user_phone; ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="InvoiceAddress" class="col-sm-3 col-form-label">Alamat</label>
                                            <div class="col-sm-9">
                                                <textarea rows="5" class="form-control" id="InvoiceAddress" readonly><?php echo $user_address; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-5">
                                        <div class="mb-4">
                                            <h6 class="fw-bold">Invoice Kepada:</h6>
                                            <span class="fs-12 text-muted">Kirimkan faktur dan terima pembayaran</span>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <label for="ClientName" class="col-sm-3 col-form-label">Nama</label>
                                            <div class="col-sm-9">
                                            <select id="customer-select" class="form-control" onchange="updateCustomerDetails(this);">
        <option value="">Pilih Pelanggan</option>
        <?php
        // Fetch and display available customers dynamically
        $query = "SELECT customer_id, customer_name, address FROM customers";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['customer_id']}' data-address='{$row['address']}'>{$row['customer_name']}</option>";
        }
        ?>
    </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="alamat_kirim" class="col-sm-3 col-form-label">Alamat</label>
                                            <div class="col-sm-9">
                                                <textarea rows="5" class="form-control" id="alamat_kirim" name="alamat_kirim"><?php echo isset($customer_data['address']) ? $customer_data['address'] : ''; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="border-dashed">
                                <div class="px-4 clearfix">
                                    <div class="mb-4 d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="fw-bold">Tambahkan Barang:</h6>
                                            <span class="fs-12 text-muted">Tambahkan barang ke faktur</span>
                                        </div>
                                        <div class="avatar-text avatar-sm" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Informasi">
                                            <i class="feather feather-info"></i>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered overflow-hidden" id="tab_logic">
                                            <thead>
                                                <tr class="single-item">
                                                    <th class="text-center">No</th>
                                                    <th class="text-center wd-450">Nama Barang</th>
                                                    <th class="text-center wd-100">Qty Roll</th>
                                                    <th class="text-center wd-100">Qty Small Roll</th>
                                                    <th class="text-center wd-150">Harga</th>
                                                    <th class="text-center wd-150">Total</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr id="addr0">
                                                    <td>1</td>
                                                    <td>
                                                        <select name="product[]" class="form-control product-select" onchange="updateProductDetails(this);">
                                                            <option value="">Pilih Barang</option>
                                                            <?php
                                                            $query = "SELECT kode_penerimaan, nama_barang, warna_motif, harga_jual FROM penyimpanan_barang";
                                                            $result = $conn->query($query);
                                                            while ($row = $result->fetch_assoc()) {
                                                                echo "<option value='{$row['kode_penerimaan']}' data-harga='{$row['harga_jual']}'>{$row['nama_barang']} - {$row['warna_motif']}</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="qty_roll[]" placeholder="Qty Roll" class="form-control qty_roll" step="1" min="0" onchange="calculateTotal(this);"></td>
                                                    <td><input type="number" name="qty_small_roll[]" placeholder="Qty Small Roll" class="form-control qty_small_roll" step="1" min="0" onchange="calculateTotal(this);"></td>
                                                    <td><input type="text" name="price[]" placeholder="Harga Satuan" class="form-control price" readonly></td>
                                                    <td><input type="text" name="total[]" placeholder="0" class="form-control total" readonly></td>
                                                    <td>
                                                        <button class="btn btn-sm bg-soft-danger text-danger" onclick="deleteRow(this);">Hapus</button>
                                                    </td>
                                                </tr>
                                                <tr id="addr1"></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <button id="add_row" class="btn btn-sm btn-primary" onclick="addRow();">Tambahkan Barang</button>
                                    </div>
                                </div>
                                <hr class="border-dashed">
                                <div class="px-4 pb-4">
                                    <div class="form-group">
                                        <label for="catatan" class="form-label">Catatan Invoice:</label>
                                        <textarea rows="6" class="form-control" id="catatan" name="catatan">A/N : Dedy Junaedi
No. Rek :</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <div class="mb-4 d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="fw-bold">Total Akhir:</h6>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Subtotal:</label>
                                    <input type="text" class="form-control" id="subtotal" name="subtotal" value="0" readonly>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Pajak:</label>
                                    <input type="text" class="form-control" id="pajak" name="pajak" value="0" readonly>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="form-label">Total:</label>
                                    <input type="text" class="form-control" id="total_akhir" name="total_akhir" value="0" readonly>
                                </div>
                                <div class="mb-4">
                                    <h6 class="fw-bold">Metode Pembayaran:</h6>
                                </div>
                                <div class="form-group mb-4">
                                    <select class="form-control" name="payment_method" required>
                                        <option value="">Pilih Metode Pembayaran</option>
                                        <option value="Cash">Tunai</option>
                                        <option value="Transfer">Transfer Bank</option>
                                    </select>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="form-label">Status Pembayaran:</label>
                                    <select class="form-control" name="payment_status" required>
                                        <option value="">Pilih Status Pembayaran</option>
                                        <option value="tempo">Tempo</option>
                                        <option value="lunas">Lunas</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success">Simpan Invoice</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<?php include 'footer.php'; ?>
<script>

document.addEventListener('DOMContentLoaded', function () {
    // Function to add a new product row
    window.addRow = function () {
        const table = document.getElementById('tab_logic');
        const rowCount = table.rows.length - 1; // Adjust for header row
        const newRow = table.insertRow(rowCount);
        newRow.innerHTML = `
            <td>${rowCount + 1}</td>
            <td>
                <select name="product[]" class="form-control product-select" onchange="updateProductDetails(this);">
                    <option value="">Pilih Barang</option>
                    <?php
                    // Fetch and display available products dynamically in the new row
                    $query = "SELECT kode_penerimaan, nama_barang, warna_motif, harga_jual FROM penyimpanan_barang";
                    $result = $conn->query($query);
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['kode_penerimaan']}' data-harga='{$row['harga_jual']}'>{$row['nama_barang']} - {$row['warna_motif']}</option>";
                    }
                    ?>
                </select>
            </td>
            <td><input type="number" name="qty_roll[]" placeholder="Qty Roll" class="form-control qty_roll" step="1" min="0" onchange="calculateTotal(this);"></td>
            <td><input type="number" name="qty_small_roll[]" placeholder="Qty Small Roll" class="form-control qty_small_roll" step="1" min="0" onchange="calculateTotal(this);"></td>
            <td><input type="text" name="price[]" placeholder="Harga Satuan" class="form-control price" readonly></td>
            <td><input type="text" name="total[]" placeholder="0" class="form-control total" readonly></td>
            <td><button class="btn btn-sm bg-soft-danger text-danger" onclick="deleteRow(this);">Hapus</button></td>
        `;
    };

    // Function to delete a row
    window.deleteRow = function (btn) {
        const row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        updateRowNumbers();
        calculateGrandTotal(); // Recalculate total after row removal
    };

    // Function to update product details
    window.updateProductDetails = function (select) {
        const row = select.closest('tr');
        const priceInput = row.querySelector('.price');
        const totalInput = row.querySelector('.total');
        const qtyRollInput = row.querySelector('.qty_roll');
        const qtySmallRollInput = row.querySelector('.qty_small_roll');

        // Get the selected product's price
        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption.dataset.harga || 0;
        priceInput.value = price;

        // Calculate total for the row
        calculateTotal(qtyRollInput);
    };

    // Function to calculate total for a row
    window.calculateTotal = function (input) {
        const row = input.closest('tr');
        const qtyRoll = row.querySelector('.qty_roll').value || 0;
        const qtySmallRoll = row.querySelector('.qty_small_roll').value || 0;
        const price = row.querySelector('.price').value || 0;

        const total = (qtyRoll * price) + (qtySmallRoll * price); // Adjust if small rolls have different pricing
        row.querySelector('.total').value = total.toFixed(2);
        calculateGrandTotal(); // Recalculate the grand total
    };

    // Function to calculate grand total
    function calculateGrandTotal() {
        const totalInputs = document.querySelectorAll('.total');
        let grandTotal = 0;

        totalInputs.forEach(input => {
            grandTotal += parseFloat(input.value) || 0;
        });

        document.getElementById('subtotal').value = grandTotal.toFixed(2);
        // Assuming you have a tax percentage to calculate the total after tax
        const tax = grandTotal * 0.1; // Example: 10% tax
        document.getElementById('pajak').value = tax.toFixed(2);
        document.getElementById('total_akhir').value = (grandTotal + tax).toFixed(2);
    }

    // Function to update row numbers
    function updateRowNumbers() {
        const rows = document.querySelectorAll('#tab_logic tbody tr');
        rows.forEach((row, index) => {
            row.cells[0].innerText = index + 1; // Update row number
        });
    }
});
// Function to update customer details based on selection
function updateCustomerDetails(select) {
    const selectedOption = select.options[select.selectedIndex];
    const addressInput = document.getElementById('customer-address');
    
    // Set the address field based on selected customer
    const address = selectedOption.dataset.address || '';
    addressInput.value = address;
}

</script>
