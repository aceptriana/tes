<?php
//tambah-nota.php
include '../config/db.php';
include '../config/session.php';
include 'header.php';
include 'navbar.php';

// Function to generate auto-incremented kode_penerimaan
function generateKodePenerimaan($conn) {
    $sql = "SELECT MAX(kode_penerimaan) as max_kode FROM nota_penerimaan";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    if ($row['max_kode'] === null) {
        return 1; // Start with 1 if no records exist
    } else {
        return $row['max_kode'] + 1;
    }
}

$kode_penerimaan_auto = generateKodePenerimaan($conn);

// Fetch suppliers for dropdown
$sql_suppliers = "SELECT kode_pemasok, nama FROM pemasok ORDER BY nama";
$result_suppliers = $conn->query($sql_suppliers);

// Get any error messages
$error_message = isset($_GET['message']) ? urldecode($_GET['message']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
?>

<main class="nxl-container">
    <div class="nxl-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Nota Penerimaan</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Tambah Nota Penerimaan</li>
                </ul>
            </div>
        </div>

        <!-- Main Form -->
        <div class="main-content">
            <form id="notaPenerimaanForm" action="process_tambah_nota.php" method="POST" enctype="multipart/form-data">
                <!-- Master Data Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Data Master</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Penerimaan</label>
                                <input type="number" class="form-control" name="kode_penerimaan" value="<?php echo $kode_penerimaan_auto; ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select class="form-select" name="kode_pemasok" required>
                                    <option value="">Pilih Supplier</option>
                                    <?php while($supplier = $result_suppliers->fetch_assoc()): ?>
                                        <option value="<?php echo $supplier['kode_pemasok']; ?>">
                                            <?php echo htmlspecialchars($supplier['nama']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_barang" maxlength="100" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">GSM <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="gsm" min="1" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Lebar (cm) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="width_cm" min="1" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Design Image <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="design_image" accept="image/jpeg,image/png,image/gif" required>
                                <small class="text-muted">Max: 5MB. Format: JPG, PNG, GIF</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Add Buttons -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex gap-2 mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addMultipleItems(1)">+1</button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addMultipleItems(5)">+5</button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addMultipleItems(10)">+10</button>
                            
                            <div class="col-md-3 mb-3">
    <label class="form-label">Tambah Barang (Opsional)</label>
    <input type="number" id="customAmount" class="form-control" min="0" placeholder="Jumlah">
</div>
                            
                            <span class="ms-3 align-self-center" id="itemCounter">0 item</span>
                        </div>
                    </div>
                </div>

                <!-- Detail Items Container -->
                <div id="detailBarangContainer">
                    <!-- Detail forms will be added here -->
                </div>

                <!-- Submit Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Nota Penerimaan
                            </button>
                            <a href="data-nota.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<!-- Tambahkan SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let itemCount = 0;
const MAX_ITEMS = 100;

function updateItemCounter() {
    document.getElementById('itemCounter').textContent = `${itemCount} item${itemCount !== 1 ? 's' : ''}`;
    updateButtonStates();
}

function updateButtonStates() {
    const remainingItems = MAX_ITEMS - itemCount;
    const buttons = document.querySelectorAll('.btn-outline-primary');
    const customInput = document.getElementById('customAmount');
    
    buttons.forEach(button => {
        const amount = parseInt(button.textContent.replace('+', ''));
        button.disabled = amount > remainingItems;
    });
    
    if (customInput) {
        customInput.max = remainingItems;
        customInput.value = Math.min(customInput.value, remainingItems);
    }
}

function createDetailForm(index) {
    const detailDiv = document.createElement('div');
    detailDiv.className = 'card mb-3 detail-item';
    detailDiv.dataset.index = index;

    detailDiv.innerHTML = `
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Detail Barang #${index + 1}</h6>
            ${index > 0 ? `<button type="button" class="btn btn-danger btn-sm" onclick="removeDetailForm(this)">×</button>` : ''}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Foto Warna <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="photo_path[]" accept="image/jpeg,image/png,image/gif" required>
                    <small class="text-muted">Max: 5MB. Format: JPG, PNG, GIF</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Motif <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama_motif[]" maxlength="100" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Warna Motif <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="warna_motif[]" maxlength="50" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jumlah Roll Besar <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" class="form-control" name="roll[]" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Panjang per Roll (m) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" class="form-control" name="roll_length[]" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jumlah Roll Kecil</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="small_roll[]" value="0">
                </div>
            </div>
        </div>
    `;

    document.getElementById('detailBarangContainer').appendChild(detailDiv);
    detailDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function addMultipleItems(count) {
    const remainingItems = MAX_ITEMS - itemCount;
    const itemsToAdd = Math.min(count, remainingItems);
    
    if (itemsToAdd <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Batas Maksimum',
            text: `Maksimum ${MAX_ITEMS} items telah tercapai`
        });
        return;
    }

    for (let i = 0; i < itemsToAdd; i++) {
        createDetailForm(itemCount);
    }
}

function addCustomAmount() {
    const customInput = document.getElementById('customAmount');
    const customAmount = parseInt(customInput.value);
    
    if (!customAmount || customAmount <= 0) {
        // Jika input kosong atau 0, tidak perlu menampilkan peringatan
        return;
    }
    
    addMultipleItems(customAmount);
    customInput.value = '';
}

function removeDetailForm(button) {
    const detailDiv = button.closest('.detail-item');
    detailDiv.remove();
    itemCount--;
    updateItemCounter();
    renumberDetails();
}

function renumberDetails() {
    document.querySelectorAll('.detail-item').forEach((detail, index) => {
        detail.querySelector('h6').textContent = `Detail Barang #${index + 1}`;
        detail.dataset.index = index;
    });
}

// Form validation
document.getElementById('notaPenerimaanForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validasi master data terlebih dahulu
    const masterFields = {
        'kode_pemasok': 'Supplier',
        'nama_barang': 'Nama Barang',
        'gsm': 'GSM',
        'width_cm': 'Lebar',
        'design_image': 'Design Image'
    };

    let invalidFields = [];
    
    // Cek field master
    for (let [fieldName, fieldLabel] of Object.entries(masterFields)) {
        const field = this.querySelector(`[name="${fieldName}"]`);
        if (!field.value) {
            invalidFields.push(fieldLabel);
        }
    }

    if (invalidFields.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Data Master Belum Lengkap',
            text: `Mohon lengkapi: ${invalidFields.join(', ')}`
        });
        return;
    }

    // Validasi detail barang (minimal 1)
    if (itemCount === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Detail Barang Kosong',
            text: 'Mohon tambahkan minimal 1 detail barang'
        });
        return;
    }

    // Validasi field-field di dalam detail barang
    let detailValid = true;
    let detailInvalidMessage = '';

    document.querySelectorAll('.detail-item').forEach((detail, index) => {
        const requiredFields = detail.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value) {
                detailValid = false;
                detailInvalidMessage = `Ada field yang belum diisi pada Detail Barang #${index + 1}`;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });
    });

    if (!detailValid) {
        Swal.fire({
            icon: 'error',
            title: 'Detail Barang Belum Lengkap',
            text: detailInvalidMessage
        });
        return;
    }

    // Konfirmasi sebelum submit
    Swal.fire({
        title: 'Konfirmasi Penyimpanan',
        text: "Apakah Anda yakin ingin menyimpan nota penerimaan ini?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Simpan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
    });
});

// File validation dengan SweetAlert
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function() {
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (this.files[0] && this.files[0].size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: 'Ukuran file maksimal 5MB'
            });
            this.value = '';
        }
    });
});

// Initialize form
window.addEventListener('DOMContentLoaded', () => {
    createDetailForm(0);
    
    const customInput = document.getElementById('customAmount');
    customInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        const maxValue = MAX_ITEMS - itemCount;
        if (parseInt(this.value) > maxValue) {
            this.value = maxValue;
        }
    });
});
</script>

<?php include 'footer.php'; ?>