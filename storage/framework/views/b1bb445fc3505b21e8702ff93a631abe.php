<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - ADIKASN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .toast { animation: slideIn .3s ease; }
        @keyframes slideIn { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in > * { animation: fadeIn 0.3s ease-out forwards; }
        .animate-fade-in > *:nth-child(1) { animation-delay: 0ms; }
        .animate-fade-in > *:nth-child(2) { animation-delay: 50ms; }
        .animate-fade-in > *:nth-child(3) { animation-delay: 100ms; }
        .animate-fade-in > *:nth-child(4) { animation-delay: 150ms; }
        .animate-fade-in > *:nth-child(n+5) { animation-delay: 200ms; }
        .file-card { transition: all 0.2s; }
        .file-card:hover { transform: translateY(-2px); }
        .file-tag { display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .tag-pdf { background: #fee2e2; color: #ef4444; }
        .tag-doc { background: #dbeafe; color: #3b82f6; }
        .tag-xls { background: #d1fae5; color: #10b981; }
        .tag-img { background: #fef3c7; color: #f59e0b; }
        .tag-other { background: #f3f4f6; color: #6b7280; }
        select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%236b7280'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px; padding-right: 36px !important; }
    </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-100 px-4 sm:px-8 h-16 flex justify-between items-center sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="bg-blue-600 p-2 rounded-xl text-white shadow-md">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="font-extrabold text-slate-800 text-sm sm:text-base">BKPSDM</span>
                <p class="text-[10px] sm:text-xs text-slate-600">Badan Kepegawaian dan Pengembangan Sumber Daya Manusia</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden sm:flex items-center gap-2 bg-slate-50 text-slate-600 px-3 py-1.5 rounded-full text-xs font-bold border border-slate-100">
                <i data-lucide="user-circle" class="w-3 h-3"></i>
                <span id="nav-name">User</span>
            </div>
            <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all" title="Keluar">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </button>
            </form>
        </div>
    </nav>

    <div id="toast" class="hidden fixed bottom-6 right-6 z-50 px-5 py-3.5 rounded-2xl shadow-xl text-sm font-bold flex items-center gap-2 toast max-w-xs"></div>

    <!-- Main -->
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 max-w-7xl mx-auto w-full">

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mb-2">ADIKASN</h1>
            <p class="text-slate-600 text-sm font-medium">Anjungan Data Informasi Kepegawaian Aparatur Sipil Negara</p>
        </div>

        <!-- Filter Card -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sm:p-8 mb-8">
            <div class="mb-5">
                <h2 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4 text-blue-500"></i> Filter Data File
                </h2>
                <p class="text-xs text-slate-400 mt-1">Pilih kategori untuk mencari file yang diupload oleh admin</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Kabupaten</label>
                    <select id="f-kabupaten" onchange="applyFilter()" class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-100 cursor-pointer">
                        <option value="">Semua Kabupaten</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Satuan Unit Kerja</label>
                    <select id="f-skpd" onchange="applyFilter()" class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-100 cursor-pointer">
                        <option value="">Semua Unit Kerja</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Jenis Data</label>
                    <select id="f-jenis-data" onchange="applyFilter()" class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-100 cursor-pointer">
                        <option value="">Semua Jenis</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Berdasarkan Periode</label>
                    <select id="f-periode" onchange="applyFilter()" class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-100 cursor-pointer">
                        <option value="">Semua Periode</option>
                    </select>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="mt-6 pt-5 border-t border-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <p id="result-count" class="text-xs text-slate-500 font-semibold hidden">Ditemukan: <span class="text-blue-600">0</span> file</p>
                    <p id="selected-file-name" class="text-xs text-blue-600 font-semibold mt-1 hidden"></p>
                </div>
                <div class="flex gap-3 flex-wrap">
                    <button onclick="resetFilter()" class="px-5 py-3 rounded-2xl font-bold text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all flex items-center gap-2">
                        <i data-lucide="refresh-ccw" class="w-4 h-4"></i> Reset
                    </button>
                    <button onclick="downloadSelectedFile()" id="btn-download-file" class="hidden px-6 py-3 rounded-2xl font-bold text-sm bg-emerald-600 text-white hover:bg-emerald-700 transition-all flex items-center gap-2 shadow-lg shadow-emerald-100">
                        <i data-lucide="download" class="w-4 h-4"></i> Unduh File Asli
                    </button>
                    <button onclick="exportPDF()" id="btn-export-pdf" class="hidden px-6 py-3 rounded-2xl font-bold text-sm bg-slate-900 text-white hover:bg-black transition-all flex items-center gap-2 shadow-lg shadow-slate-200">
                        <i data-lucide="file-down" class="w-4 h-4"></i> Unduh PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- File Preview Area -->
        <div id="preview-area" class="hidden mb-8">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm">
                        <i data-lucide="eye" class="w-4 h-4 text-blue-500"></i>
                        <span id="preview-title">Preview File</span>
                    </h3>
                    <button onclick="closePreview()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-50">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <div id="preview-content" class="rounded-2xl overflow-hidden bg-slate-50 min-h-48"></div>
            </div>
        </div>

        <!-- File Grid -->
        <div id="card-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 animate-fade-in"></div>

        <!-- Loading State -->
        <div id="loading-state" class="hidden text-center py-20">
            <div class="inline-block">
                <div class="w-12 h-12 border-4 border-slate-200 border-t-blue-500 rounded-full animate-spin mb-4"></div>
            </div>
            <p class="text-slate-500 font-bold">Memproses kategori...</p>
            <p class="text-slate-400 text-sm mt-1">Tunggu sebentar.</p>
        </div>

        <!-- Instructions State -->
        <div id="instructions-state" class="hidden text-center py-20">
            <div class="bg-blue-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="filter" class="w-8 h-8 text-blue-400"></i>
            </div>
            <p class="text-slate-600 font-bold text-lg">Pilih Minimal 3 Kategori</p>
            <p class="text-slate-500 text-sm mt-3 max-w-md mx-auto">
                <strong>Kombinasi 1:</strong> Kabupaten + SKPD + Jenis Data + Periode
            </p>
            <p class="text-slate-500 text-sm mt-2 max-w-md mx-auto">
                <strong>Kombinasi 2:</strong> SKPD + Jenis Data + Periode (tanpa Kabupaten)
            </p>
            <p class="text-slate-500 text-sm mt-2 max-w-md mx-auto">
                <strong>Kombinasi 3:</strong> Kabupaten + Jenis Data + Periode (tanpa SKPD)
            </p>
        </div>

        <!-- Invalid Combination State -->
        <div id="invalid-combination-state" class="hidden text-center py-20">
            <div class="bg-amber-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="alert-triangle" class="w-8 h-8 text-amber-500"></i>
            </div>
            <p class="text-slate-600 font-bold text-lg">Kombinasi Kategori Tidak Valid</p>
            <p class="text-slate-500 text-sm mt-3 max-w-md mx-auto">
                Silakan pilih salah satu kombinasi berikut:
            </p>
            <p class="text-slate-500 text-sm mt-2 max-w-md mx-auto font-semibold">
                ✓ Kabupaten + SKPD + Jenis Data + Periode
            </p>
            <p class="text-slate-500 text-sm mt-2 max-w-md mx-auto font-semibold">
                ✓ SKPD + Jenis Data + Periode (tanpa Kabupaten)
            </p>
            <p class="text-slate-500 text-sm mt-2 max-w-md mx-auto font-semibold">
                ✓ Kabupaten + Jenis Data + Periode (tanpa SKPD)
            </p>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden text-center py-20">
            <div class="bg-slate-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="search-x" class="w-8 h-8 text-slate-300"></i>
            </div>
            <p class="text-slate-500 font-bold">Tidak ada file yang sesuai filter.</p>
            <p class="text-slate-400 text-sm mt-1">Coba ubah filter atau hubungi admin.</p>
        </div>

        <!-- No Data State -->
        <div id="no-data-state" class="hidden text-center py-20">
            <div class="bg-blue-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="database" class="w-8 h-8 text-blue-300"></i>
            </div>
            <p class="text-slate-500 font-bold">Belum ada data dari admin.</p>
            <p class="text-slate-400 text-sm mt-1">Admin belum mengunggah file apapun.</p>
        </div>
    </main>

    <script>
    window.onload = () => {
        try {
            const nameEl = document.getElementById('nav-name');
            if (nameEl) {
                nameEl.innerText = '<?php echo e(Auth::user()->nip); ?>' || 'User';
            }
            
            loadData().catch(err => {
                console.error('Failed to load data:', err);
                showToast('Gagal memuat data!', 'error');
            });
            
            if (window.lucide) {
                lucide.createIcons();
            }
        } catch (error) {
            console.error('Error initializing dashboard:', error);
            showToast('Terjadi kesalahan saat memuat dashboard!', 'error');
        }
    };

    let allFiles = [];
    let filteredFiles = [];
    let displayedFiles = []; // Track which files are currently displayed
    let selectedFileData = null;

    async function loadData() {
        try {
            console.log('Loading files...');
            
            // Add cache-busting timestamp
            const res = await fetch('<?php echo e(route("user.files")); ?>?t=' + new Date().getTime());
            if (!res.ok) {
                throw new Error('Failed to fetch files');
            }
            
            const data = await res.json();
            console.log('Files response:', data);
            
            if (!data.success) {
                showToast('Gagal memuat file!', 'error');
                return;
            }
            
            allFiles = data.files || [];
            console.log('Total files loaded:', allFiles.length);
            
            await populateDropdowns();
            
            // Show appropriate state based on data availability
            const cardGrid = document.getElementById('card-grid');
            const noDataState = document.getElementById('no-data-state');
            const emptyState = document.getElementById('empty-state');
            const instructionsState = document.getElementById('instructions-state');
            
            if (allFiles.length === 0) {
                console.log('No files available from admin');
                cardGrid.classList.add('hidden');
                emptyState.classList.add('hidden');
                instructionsState.classList.add('hidden');
                noDataState.classList.remove('hidden');
            } else {
                console.log('Files loaded - show instructions to user');
                cardGrid.classList.add('hidden');
                emptyState.classList.add('hidden');
                noDataState.classList.add('hidden');
                instructionsState.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error in loadData:', error);
            showToast('Terjadi kesalahan saat memuat data!', 'error');
        }
    }

    async function populateDropdowns() {
        try {
            console.log('Fetching categories from:', '<?php echo e(route("user.categories")); ?>');
            
            // Add timestamp to prevent caching
            const res = await fetch('<?php echo e(route("user.categories")); ?>?t=' + new Date().getTime());
            if (!res.ok) {
                throw new Error(`HTTP ${res.status}: Failed to fetch categories`);
            }
            
            const data = await res.json();
            console.log('Categories response:', data);
            
            if (!data.success) {
                console.error('API returned success: false', data);
                showToast('Gagal memuat kategori!', 'error');
                return;
            }

            const categories = data.categories;
            console.log('Categories data:', categories);
            
            // Validate structure
            if (!categories) {
                throw new Error('No categories in response');
            }

            const kabupatenEl = document.getElementById('f-kabupaten');
            const skpdEl = document.getElementById('f-skpd');
            const jenisEl = document.getElementById('f-jenis-data');
            const periodeEl = document.getElementById('f-periode');

            if (!kabupatenEl || !skpdEl || !jenisEl || !periodeEl) {
                console.error('Filter elements not found in DOM');
                return;
            }

            // Reset dropdowns
            kabupatenEl.innerHTML = '<option value="">Semua Kabupaten</option>';
            skpdEl.innerHTML = '<option value="">Semua Unit Kerja</option>';
            jenisEl.innerHTML = '<option value="">Semua Jenis</option>';
            periodeEl.innerHTML = '<option value="">Semua Periode</option>';

            // Populate kabupaten
            if (categories.kabupaten && Array.isArray(categories.kabupaten) && categories.kabupaten.length > 0) {
                console.log('Adding kabupaten options:', categories.kabupaten.length);
                categories.kabupaten.forEach(k => {
                    kabupatenEl.innerHTML += `<option value="${k.name}">${k.name}</option>`;
                });
            } else {
                console.log('No kabupaten data available');
            }

            // Populate SKPD
            if (categories.skpd && Array.isArray(categories.skpd) && categories.skpd.length > 0) {
                console.log('Adding skpd options:', categories.skpd.length);
                categories.skpd.forEach(s => skpdEl.innerHTML += `<option value="${s.name}">${s.name}</option>`);
            }

            // Populate Jenis Data
            if (categories.jenisData && Array.isArray(categories.jenisData) && categories.jenisData.length > 0) {
                console.log('Adding jenisData options:', categories.jenisData.length);
                categories.jenisData.forEach(j => jenisEl.innerHTML += `<option value="${j.name}">${j.name}</option>`);
            }

            // Populate Periode
            if (categories.periode && Array.isArray(categories.periode) && categories.periode.length > 0) {
                console.log('Adding periode options:', categories.periode.length);
                categories.periode.forEach(p => periodeEl.innerHTML += `<option value="${p.name}">${p.name}</option>`);
            }

            console.log('Dropdowns populated successfully');
        } catch (error) {
            console.error('Error in populateDropdowns:', error);
            console.error('Stack:', error.stack);
            showToast('Terjadi kesalahan saat memuat kategori: ' + error.message, 'error');
            
            // Set default empty options for graceful fallback
            const kabupatenEl = document.getElementById('f-kabupaten');
            const skpdEl = document.getElementById('f-skpd');
            const jenisEl = document.getElementById('f-jenis-data');
            const periodeEl = document.getElementById('f-periode');
            
            if (kabupatenEl) kabupatenEl.innerHTML = '<option value="">Semua Kabupaten</option>';
            if (skpdEl) skpdEl.innerHTML = '<option value="">Semua Unit Kerja</option>';
            if (jenisEl) jenisEl.innerHTML = '<option value="">Semua Jenis</option>';
            if (periodeEl) periodeEl.innerHTML = '<option value="">Semua Periode</option>';
        }
    }

    async function applyFilter() {
        const kabupatenVal = document.getElementById('f-kabupaten').value.toLowerCase().trim();
        const skpdVal = document.getElementById('f-skpd').value.toLowerCase().trim();
        const jenisVal = document.getElementById('f-jenis-data').value.toLowerCase().trim();
        const periodeVal = document.getElementById('f-periode').value.toLowerCase().trim();

        // Count how many categories are selected
        const selectedCount = [kabupatenVal, skpdVal, jenisVal, periodeVal].filter(v => v).length;

        console.log('=== FILTER DEBUG ===');
        console.log('Selected categories:', { kabupatenVal, skpdVal, jenisVal, periodeVal, selectedCount });

        // Get all state elements
        const cardGrid = document.getElementById('card-grid');
        const emptyState = document.getElementById('empty-state');
        const noDataState = document.getElementById('no-data-state');
        const instructionsState = document.getElementById('instructions-state');
        const loadingState = document.getElementById('loading-state');
        const invalidCombinationState = document.getElementById('invalid-combination-state');
        const resultCountEl = document.getElementById('result-count');

        // Reset all states to hidden initially
        cardGrid.classList.add('hidden');
        emptyState.classList.add('hidden');
        noDataState.classList.add('hidden');
        instructionsState.classList.add('hidden');
        loadingState.classList.add('hidden');
        invalidCombinationState.classList.add('hidden');

        // Reset selection
        selectedFileData = null;
        document.getElementById('btn-download-file').classList.add('hidden');
        document.getElementById('btn-export-pdf').classList.add('hidden');
        document.getElementById('selected-file-name').classList.add('hidden');
        resultCountEl.classList.add('hidden');
        closePreview();

        // RULE 1: Require minimal 3 categories
        if (selectedCount < 3) {
            console.log('Less than 3 categories selected - show instructions');
            instructionsState.classList.remove('hidden');
            return;
        }

        // RULE 2: Check if combination is valid
        // Set 1: Kabupaten + SKPD + Jenis Data + Periode (all 4)
        // Set 2: SKPD + Jenis Data + Periode (no Kabupaten, 3 required)
        // Set 3: Kabupaten + Jenis Data + Periode (no SKPD, 3 required)
        const isSet1 = kabupatenVal && skpdVal && jenisVal && periodeVal;
        const isSet2 = !kabupatenVal && skpdVal && jenisVal && periodeVal;
        const isSet3 = kabupatenVal && !skpdVal && jenisVal && periodeVal;

        console.log('Combination check:', { isSet1, isSet2, isSet3, selectedCount });

        if (!isSet1 && !isSet2 && !isSet3) {
            console.log('Invalid combination - show error');
            invalidCombinationState.classList.remove('hidden');
            return;
        }

        // Show loading state briefly
        loadingState.classList.remove('hidden');
        await new Promise(resolve => setTimeout(resolve, 300));

        // Filter files based on valid combination (case-insensitive)
        filteredFiles = allFiles.filter(f => {
            const fKab = (f.kabupaten || '').toLowerCase().trim();
            const fSkpd = (f.skpd || '').toLowerCase().trim();
            const fJenis = (f.jenis || '').toLowerCase().trim();
            const fPeriode = (f.periode || '').toLowerCase().trim();

            // For Set 1: all 4 must match
            if (isSet1) {
                return fKab === kabupatenVal && 
                       fSkpd === skpdVal && 
                       fJenis === jenisVal && 
                       fPeriode === periodeVal;
            }

            // For Set 2: 3 must match (no check for kabupaten)
            if (isSet2) {
                return fSkpd === skpdVal && 
                       fJenis === jenisVal && 
                       fPeriode === periodeVal;
            }

            // For Set 3: 3 must match (no check for skpd)
            if (isSet3) {
                return fKab === kabupatenVal && 
                       fJenis === jenisVal && 
                       fPeriode === periodeVal;
            }

            return false;
        });

        console.log('Filtered results:', { totalFiles: allFiles.length, filteredCount: filteredFiles.length });

        // Show result count
        resultCountEl.innerHTML = `Ditemukan: <span class="text-blue-600 font-bold">${filteredFiles.length}</span> file`;
        resultCountEl.classList.remove('hidden');

        // Hide loading state
        loadingState.classList.add('hidden');

        // Show appropriate content state
        if (filteredFiles.length === 0) {
            // Filter applied but no matches
            emptyState.classList.remove('hidden');
            renderFileGrid([]);
        } else {
            // Filter applied and files match - show them
            cardGrid.classList.remove('hidden');
            renderFileGrid(filteredFiles);
        }
    }

    function showImagePreview(f) {
        document.getElementById('preview-title').innerText = f.filename;
        document.getElementById('preview-content').innerHTML = `<img src="${f.dataUrl}" alt="${f.filename}" class="max-w-full max-h-96 mx-auto rounded-xl object-contain p-4">`;
        document.getElementById('preview-area').classList.remove('hidden');
        lucide.createIcons();
    }

    function showPdfPreview(f) {
        document.getElementById('preview-title').innerText = f.filename;
        document.getElementById('preview-content').innerHTML = `<iframe src="${f.dataUrl}" class="w-full h-96 rounded-xl border-0"></iframe>`;
        document.getElementById('preview-area').classList.remove('hidden');
        lucide.createIcons();
    }

    function closePreview() {
        document.getElementById('preview-area').classList.add('hidden');
        document.getElementById('preview-content').innerHTML = '';
    }

    function downloadSelectedFile() {
        if (!selectedFileData) {
            showToast('File tidak tersedia untuk diunduh!', 'error');
            return;
        }
        window.location.href = `<?php echo e(route('user.file.download', ':id')); ?>`.replace(':id', selectedFileData.id);
        showToast(`File "${selectedFileData.filename}" sedang diunduh!`, 'success');
    }

    function getFileIcon(ext) {
        if (ext === 'pdf') return 'file-text';
        if (['doc','docx'].includes(ext)) return 'file-type-2';
        if (['xls','xlsx'].includes(ext)) return 'table-2';
        if (['jpg','jpeg','png','gif','webp'].includes(ext)) return 'image';
        return 'file';
    }

    function getTagClass(ext) {
        if (ext === 'pdf') return 'tag-pdf';
        if (['doc','docx'].includes(ext)) return 'tag-doc';
        if (['xls','xlsx'].includes(ext)) return 'tag-xls';
        if (['jpg','jpeg','png','gif','webp'].includes(ext)) return 'tag-img';
        return 'tag-other';
    }

    function renderFileGrid(files) {
        displayedFiles = files; // Track displayed files for selection
        const grid = document.getElementById('card-grid');
        if (files.length === 0) { grid.innerHTML = ''; return; }
        
        grid.innerHTML = files.map((f) => {
            const ext = (f.ext || f.filename.split('.').pop()).toLowerCase();
            const sz = f.size ? (f.size/1024).toFixed(1)+' KB' : '-';
            const isImage = ['jpg','jpeg','png','gif','webp'].includes(ext);
            const thumbHtml = isImage && f.dataUrl
                ? `<div class="w-full h-32 rounded-2xl mb-4 overflow-hidden bg-slate-50"><img src="${f.dataUrl}" alt="${f.filename}" class="w-full h-full object-cover"></div>`
                : `<div class="bg-slate-50 p-4 rounded-2xl mb-4 text-slate-300 group-hover:text-blue-500 group-hover:bg-blue-50 transition-colors inline-block"><i data-lucide="${getFileIcon(ext)}" class="w-8 h-8"></i></div>`;
            
            return `<div class="file-card bg-white rounded-3xl border border-slate-100 shadow-sm p-6 hover:shadow-xl hover:border-blue-200 group cursor-pointer" onclick="selectFileById(${f.id})">
                ${thumbHtml}
                <div class="flex items-start justify-between gap-2 mb-2">
                    <h3 class="font-bold text-slate-800 text-sm leading-tight line-clamp-2 flex-1">${f.filename}</h3>
                    <span class="file-tag ${getTagClass(ext)} shrink-0">${ext.toUpperCase()}</span>
                </div>
                ${f.desc ? `<p class="text-xs text-slate-500 mb-3 line-clamp-2">${f.desc}</p>` : ''}
                <div class="space-y-1.5 mb-3">
                    ${f.kabupaten ? `<div class="flex items-center gap-2"><span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">${f.kabupaten}</span></div>` : ''}
                    ${f.skpd ? `<div class="flex items-center gap-2"><span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">${f.skpd}</span></div>` : ''}
                    ${f.jenis ? `<div class="flex items-center gap-2"><span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">${f.jenis}</span></div>` : ''}
                    ${f.periode ? `<div class="flex items-center gap-2"><span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-2 py-1 rounded-full">${f.periode}</span></div>` : ''}
                </div>
                <div class="pt-3 border-t border-slate-50 flex items-center justify-between">
                    <span class="text-[10px] font-medium text-slate-400">${f.date || '-'}</span>
                    <span class="text-[10px] font-semibold text-slate-500 bg-slate-50 px-2 py-1 rounded-full">${sz}</span>
                </div>
            </div>`;
        }).join('');
        lucide.createIcons();
    }

    function selectFileById(fileId) {
        const nameEl = document.getElementById('selected-file-name');
        const btnDownload = document.getElementById('btn-download-file');
        const btnPdf = document.getElementById('btn-export-pdf');
        
        // Find file by ID from displayed files
        selectedFileData = displayedFiles.find(f => f.id === fileId);
        
        if (!selectedFileData) {
            console.error('File not found:', fileId);
            return;
        }

        nameEl.innerText = `📄 ${selectedFileData.filename}`;
        nameEl.classList.remove('hidden');
        btnDownload.classList.remove('hidden');
        btnPdf.classList.remove('hidden');

        const ext = (selectedFileData.ext || '').toLowerCase();
        if (['jpg','jpeg','png','gif','webp'].includes(ext) && selectedFileData.dataUrl) {
            showImagePreview(selectedFileData);
        } else if (ext === 'pdf' && selectedFileData.dataUrl) {
            showPdfPreview(selectedFileData);
        } else {
            closePreview();
        }
    }

    function selectFile(i) {
        // Legacy function - kept for compatibility
        if (i >= 0 && i < displayedFiles.length) {
            selectFileById(displayedFiles[i].id);
        }
    }

    function resetFilter() {
        document.getElementById('f-kabupaten').value = '';
        document.getElementById('f-skpd').value = '';
        document.getElementById('f-jenis-data').value = '';
        document.getElementById('f-periode').value = '';
        
        selectedFileData = null;
        document.getElementById('btn-download-file').classList.add('hidden');
        document.getElementById('btn-export-pdf').classList.add('hidden');
        document.getElementById('selected-file-name').classList.add('hidden');
        document.getElementById('result-count').classList.add('hidden');
        closePreview();
        
        // Show instructions state
        document.getElementById('card-grid').classList.add('hidden');
        document.getElementById('empty-state').classList.add('hidden');
        document.getElementById('no-data-state').classList.add('hidden');
        document.getElementById('invalid-combination-state').classList.add('hidden');
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('instructions-state').classList.remove('hidden');
    }

    async function exportPDF() {
        if (!selectedFileData) { showToast('Pilih file terlebih dahulu!', 'error'); return; }
        window.location.href = `<?php echo e(route('user.file.export-pdf', ':id')); ?>`.replace(':id', selectedFileData.id);
    }

    function showToast(msg, type='success') {
        const t = document.getElementById('toast');
        const bgColor = type === 'success' ? 'bg-emerald-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : 'info';
        
        t.className = 'toast fixed bottom-6 right-6 z-50 px-5 py-3.5 rounded-2xl shadow-xl text-sm font-bold flex items-center gap-2 max-w-xs text-white';
        t.classList.add(bgColor);
        t.innerHTML = `<i data-lucide="${icon}" class="w-4 h-4 shrink-0"></i><span>${msg}</span>`;
        t.classList.remove('hidden');
        lucide.createIcons();
        setTimeout(() => t.classList.add('hidden'), 4000);
    }
    </script>
</body>
</html>
<?php /**PATH C:\Users\user\Downloads\adikasn666\resources\views/user/dashboard.blade.php ENDPATH**/ ?>