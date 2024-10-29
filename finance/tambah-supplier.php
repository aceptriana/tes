<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<main class="nxl-container">
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Supplier</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Tambah Supplier</li>
                </ul>
           
                <div class="d-md-none d-flex align-items-center">
                    <a href="javascript:void(0)" class="page-header-right-open-toggle">
                        <i class="feather-align-right fs-20"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <div class="main-content">
            <div class="card">
                <div class="card-body">
                  
                    <form action="process_tambah_supplier.php" method="POST"> <!-- Adjust action to your processing file -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="supplier_name" class="form-label">Nama Supplier</label>
                                <input type="text" class="form-control" id="supplier_name" name="supplier_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contact_person" class="form-label">Kontak Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Alamat</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah Supplier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
