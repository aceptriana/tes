<?php
include '../config/db.php'; // Include database connection
include '../config/session.php'; // Check login session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $status_pembayaran = $_POST['status_pembayaran'];

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert into invoices table
        $sql_invoice = "INSERT INTO invoices (customer_id, status_pembayaran) VALUES (?, ?)";
        $stmt_invoice = $conn->prepare($sql_invoice);
        $stmt_invoice->bind_param("is", $customer_id, $status_pembayaran);
        $stmt_invoice->execute();
        $invoice_id = $conn->insert_id; // Get the last inserted ID

        // Process each item in the invoice
        for ($i = 0; $i < count($_POST['product']); $i++) {
            $product_code = $_POST['product'][$i];
            $qty_roll = $_POST['qty_roll'][$i];
            $qty_small_roll = $_POST['qty_small_roll'][$i];

            // Calculate total quantity
            $total_qty = $qty_roll + ($qty_small_roll / 6);

            // Insert into invoice_items
            $sql_items = "INSERT INTO invoice_items (invoice_id, product_code, qty_roll, qty_small_roll, total) VALUES (?, ?, ?, ?, ?)";
            $stmt_items = $conn->prepare($sql_items);
            $total = $total_qty * $_POST['price'][$i];
            $stmt_items->bind_param("isdds", $invoice_id, $product_code, $qty_roll, $qty_small_roll, $total);
            $stmt_items->execute();

            // Update item quantity in penyimpanan_barang
            $sql_update = "UPDATE penyimpanan_barang SET qty_roll = qty_roll - ?, qty_small_roll = qty_small_roll - ? WHERE kode_penerimaan = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("dds", $qty_roll, $qty_small_roll, $product_code);
            $stmt_update->execute();
        }

        // Commit transaction
        $conn->commit();
        header('Location: transaksi-barang.php?status=success');
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        header('Location: transaksi-barang.php?status=error');
    }
}
?>
