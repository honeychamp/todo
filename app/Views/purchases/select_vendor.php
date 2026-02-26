<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<style>
    .vendor-search-container {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 40px;
        padding: 60px;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.05);
    }
    .search-box-premium {
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 25px;
        padding: 20px 30px;
        color: white;
        font-size: 1.2rem;
        width: 100%;
        transition: all 0.3s;
        backdrop-filter: blur(10px);
    }
    .search-box-premium:focus {
        outline: none;
        background: rgba(255,255,255,0.12);
        border-color: #3b82f6;
        box-shadow: 0 0 40px rgba(59, 130, 246, 0.2);
    }
    .search-box-premium::placeholder { color: rgba(255,255,255,0.4); }

    .vendor-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
    }
    .vendor-tile {
        background: white;
        border-radius: 28px;
        padding: 25px;
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
        display: block;
        position: relative;
        overflow: hidden;
    }
    .vendor-tile:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
        border-color: #3b82f6;
    }
    .vendor-tile::after {
        content: '\f061';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: -20px;
        top: 50%;
        transform: translateY(-50%);
        color: #3b82f6;
        font-size: 1.2rem;
        transition: right 0.3s;
        opacity: 0;
    }
    .vendor-tile:hover::after { right: 25px; opacity: 1; }

    .vendor-avatar {
        width: 55px; height: 55px;
        border-radius: 18px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        color: #3b82f6;
        font-size: 1.4rem;
        margin-bottom: 20px;
        transition: all 0.3s;
    }
    .vendor-tile:hover .vendor-avatar { background: #3b82f6; color: white; }

    .vendor-name { font-weight: 800; color: #1e293b; font-size: 1.15rem; margin: 0; }
    .vendor-meta { font-size: 0.82rem; color: #64748b; font-weight: 500; margin-top: 4px; display: block; }

    .empty-state {
        text-align: center;
        padding: 100px 40px;
        background: #f8fafc;
        border-radius: 40px;
        border: 2px dashed #e2e8f0;
        grid-column: 1 / -1;
    }
</style>

<div class="animate-wow">
    <!-- Header & Search -->
    <div class="vendor-search-container shadow-2xl">
        <div class="row align-items-center mb-5">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-4">
                    <div class="rounded-4 bg-primary bg-opacity-20 d-flex align-items-center justify-content-center shadow-lg" style="width: 70px; height: 70px;">
                        <i class="fas fa-truck-loading fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h1 class="fw-900 m-0 text-white tracking-tight">Stock Procurement</h1>
                        <p class="text-white-50 m-0 fs-5 mt-1">Select your primary supplier to begin data record.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-md-end mt-4 mt-md-0">
                <a href="<?= base_url('vendors') ?>" class="btn btn-outline-primary rounded-pill px-5 py-3 fw-900 border-2">
                    <i class="fas fa-plus-circle me-2"></i> REGISTER NEW VENDOR
                </a>
            </div>
        </div>

        <div class="position-relative">
            <i class="fas fa-search position-absolute text-white-50 opacity-50" style="left: 25px; top: 50%; transform: translateY(-50%); font-size: 1.2rem;"></i>
            <input type="text" id="vendorSearch" class="search-box-premium ps-5" placeholder="Search supplier by name, phone or address...">
        </div>
    </div>

    <!-- Vendor Grid -->
    <div class="vendor-grid" id="vendorGrid">
        <?php foreach($vendors as $vendor): ?>
            <a href="<?= base_url('purchases/add/'.$vendor['id']) ?>" class="vendor-tile animate-up" data-name="<?= strtolower($vendor['name'] . ' ' . $vendor['phone']) ?>">
                <div class="vendor-avatar">
                    <?= substr($vendor['name'], 0, 1) ?>
                </div>
                <h4 class="vendor-name text-truncate"><?= esc($vendor['name']) ?></h4>
                <div class="d-flex flex-column gap-1 mt-2">
                    <span class="vendor-meta"><i class="fas fa-phone-volume me-2 text-primary"></i> <?= esc($vendor['phone'] ?: 'No verified phone') ?></span>
                    <span class="vendor-meta text-truncate"><i class="fas fa-location-dot me-2 text-primary"></i> <?= esc($vendor['address'] ?: 'Global Supplier') ?></span>
                </div>
            </a>
        <?php endforeach; ?>

        <?php if(empty($vendors)): ?>
            <div class="empty-state">
                <i class="fas fa-building-circle-exclamation fa-3x text-muted mb-4"></i>
                <h4 class="fw-800 text-dark">No Suppliers Registered</h4>
                <p class="text-muted">You need to add at least one vendor before you can record purchases.</p>
                <a href="<?= base_url('vendors') ?>" class="btn btn-primary rounded-pill px-5 py-3 mt-3 fw-900 shadow-lg">Add First Vendor</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('vendorSearch');
        const tiles = document.querySelectorAll('.vendor-tile');
        
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            let found = false;
            
            tiles.forEach(tile => {
                const searchStr = tile.getAttribute('data-name');
                if (searchStr.includes(query)) {
                    tile.style.display = 'block';
                    found = true;
                } else {
                    tile.style.display = 'none';
                }
            });
            
            // Handle no results
            const existingEmpty = document.querySelector('.search-empty-state');
            if (!found) {
                if (!existingEmpty) {
                    const emptyDiv = document.createElement('div');
                    emptyDiv.className = 'empty-state search-empty-state grid-column-all';
                    emptyDiv.style.gridColumn = '1 / -1';
                    emptyDiv.innerHTML = '<i class="fas fa-search fa-3x text-muted mb-3"></i><h5 class="fw-800">No vendors match your search</h5>';
                    document.getElementById('vendorGrid').appendChild(emptyDiv);
                }
            } else if (existingEmpty) {
                existingEmpty.remove();
            }
        });
    });
</script>

<?= $this->endSection() ?>
