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
        .file-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .file-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(59, 130, 246, 0.15); }
        .file-tag { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .tag-pdf { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; }
        .tag-doc { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1d4ed8; }
        .tag-xls { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #047857; }
        .tag-img { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #b45309; }
        .tag-other { background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); color: #374151; }
        select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%236b7280'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px; padding-right: 36px !important; }
        .gradient-primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
        .gradient-header { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="gradient-header border-b border-slate-200/50 px-4 sm:px-8 h-16 flex justify-between items-center sticky top-0 z-50 shadow-lg backdrop-blur-sm">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-br from-blue-400 to-blue-600 p-2.5 rounded-2xl text-white shadow-lg">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="font-black text-white text-sm sm:text-base">BKPSDM</span>
                <p class="text-[10px] sm:text-xs text-blue-100">Badan Kepegawaian dan Pengembangan Sumber Daya Manusia</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden sm:flex items-center gap-2 bg-white/10 text-white px-4 py-2 rounded-full text-xs font-bold border border-white/20 backdrop-blur-md">
                <i data-lucide="user-circle" class="w-4 h-4"></i>
                <span id="nav-name">User</span>
            </div>
            <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="p-2.5 text-white/60 hover:text-red-300 hover:bg-red-500/20 rounded-xl transition-all duration-300" title="Keluar">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </button>
            </form>
        </div>
    </nav>

    <div id="toast" class="hidden fixed bottom-6 right-6 z-50 px-5 py-3.5 rounded-2xl shadow-xl text-sm font-bold flex items-center gap-2 toast max-w-xs"></div>

    <!-- Main -->
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 max-w-7xl mx-auto w-full">

        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-block mb-4">
                <div class=" from-blue-100 to-indigo-100 p-4 rounded-3xl">
                    
                </div>
            </div>
            <h1 class="text-4xl sm:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 mb-3">ADIKASN</h1>
            <p class="text-slate-600 text-sm font-semibold max-w-2xl mx-auto">Anjungan Data Informasi Kepegawaian Aparatur Sipil Negara</p>
        </div>

        <!-- Filter Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-lg p-6 sm:p-8 mb-8 backdrop-blur-sm">
            <div class="mb-6">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <div class="bg-gradient-to-br from-blue-100 to-blue-50 p-2 rounded-xl">
                        <i data-lucide="filter" class="w-5 h-5 text-blue-600"></i>
                    </div> 
                    Cari & Filter File
                </h2>
                <p class="text-sm text-slate-500 mt-2">Gunakan filter untuk menemukan file yang sesuai kebutuhan Anda</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-600 uppercase tracking-wider">Kabupaten</label>
                    <select id="f-kabupaten" onchange="applyFilter()" class="w-full px-4 py-3 bg-gradient-to-br from-slate-50 to-slate-100 border-2 border-slate-200 rounded-xl text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer transition-all duration-300 hover:border-blue-300">
                        <option value="">Semua Kabupaten</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-600 uppercase tracking-wider">Satuan Unit Kerja</label>
                    <select id="f-skpd" onchange="applyFilter()" class="w-full px-4 py-3 bg-gradient-to-br from-slate-50 to-slate-100 border-2 border-slate-200 rounded-xl text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer transition-all duration-300 hover:border-blue-300">
                        <option value="">Semua Unit Kerja</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-600 uppercase tracking-wider">Jenis Data</label>
                    <select id="f-jenis-data" onchange="applyFilter()" class="w-full px-4 py-3 bg-gradient-to-br from-slate-50 to-slate-100 border-2 border-slate-200 rounded-xl text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer transition-all duration-300 hover:border-blue-300">
                        <option value="">Semua Jenis</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-600 uppercase tracking-wider">Periode</label>
                    <select id="f-periode" onchange="applyFilter()" class="w-full px-4 py-3 bg-gradient-to-br from-slate-50 to-slate-100 border-2 border-slate-200 rounded-xl text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer transition-all duration-300 hover:border-blue-300">
                        <option value="">Semua Periode</option>
                    </select>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="mt-8 pt-6 border-t-2 border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <p id="result-count" class="text-sm text-slate-600 font-black hidden">✓ Ditemukan: <span class="text-blue-600">0</span> file</p>
                    <p id="selected-file-name" class="text-sm text-blue-700 font-bold mt-2 hidden"></p>
                </div>
                <div class="flex gap-3 flex-wrap">
                    <button onclick="resetFilter()" class="px-5 py-3 rounded-2xl font-bold text-sm text-slate-700 bg-gradient-to-r from-slate-200 to-slate-300 hover:from-slate-300 hover:to-slate-400 transition-all duration-300 flex items-center gap-2 shadow-md hover:shadow-lg">
                        <i data-lucide="refresh-ccw" class="w-4 h-4"></i> Reset
                    </button>
                    <button onclick="downloadSelectedFile()" id="btn-download-file" class="hidden px-6 py-3 rounded-2xl font-bold text-sm bg-gradient-to-r from-emerald-500 to-emerald-600 text-white hover:from-emerald-600 hover:to-emerald-700 transition-all duration-300 flex items-center gap-2 shadow-lg shadow-emerald-200 hover:shadow-emerald-300">
                        <i data-lucide="download" class="w-4 h-4"></i> Unduh File Asli
                    </button>
                    <button onclick="exportPDF()" id="btn-export-pdf" class="hidden px-6 py-3 rounded-2xl font-bold text-sm bg-gradient-to-r from-slate-800 to-slate-900 text-white hover:from-slate-900 hover:to-black transition-all duration-300 flex items-center gap-2 shadow-lg shadow-slate-300 hover:shadow-slate-400">
                        <i data-lucide="file-down" class="w-4 h-4"></i> Unduh PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- File Preview Area -->
        <div id="preview-area" class="hidden mb-8 animate-fade-in">
            <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-lg p-6 backdrop-blur-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-800 flex items-center gap-3 text-lg">
                        <div class="bg-gradient-to-br from-blue-100 to-blue-50 p-2 rounded-xl">
                            <i data-lucide="eye" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <span id="preview-title">Preview File</span>
                    </h3>
                    <button onclick="closePreview()" class="text-slate-400 hover:text-slate-600 p-2 rounded-lg hover:bg-slate-100 transition-all">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div id="preview-content" class="rounded-2xl overflow-hidden bg-gradient-to-br from-slate-50 to-slate-100 min-h-56"></div>
            </div>
        </div>

        <!-- File Grid -->
        <div id="card-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 animate-fade-in"></div>

        <!-- Loading State -->
        <div id="loading-state" class="hidden text-center py-24">
            <div class="inline-block">
                <div class="w-14 h-14 border-4 border-slate-300 border-t-blue-500 rounded-full animate-spin mb-6 shadow-lg"></div>
            </div>
            <p class="text-slate-700 font-bold text-lg">Memproses kategori...</p>
            <p class="text-slate-500 text-sm mt-2">Tunggu sebentar, kami sedang memuat data.</p>
        </div>

        <!-- Instructions State -->
        <div id="instructions-state" class="hidden text-center py-24">
            <div class="bg-gradient-to-br from-blue-100 to-indigo-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                <i data-lucide="check-circle" class="w-12 h-12 text-blue-600"></i>
            </div>
            <p class="text-slate-700 font-black text-xl mb-2">File Siap Diakses!</p>
            <p class="text-slate-600 text-sm max-w-md mx-auto">Gunakan filter di atas untuk menemukan file yang sesuai dengan kebutuhan Anda</p>
        </div>

        <!-- Invalid Combination State -->
        <div id="invalid-combination-state" class="hidden text-center py-24">
            <div class="bg-gradient-to-br from-amber-100 to-orange-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                <i data-lucide="alert-triangle" class="w-12 h-12 text-amber-600"></i>
            </div>
            <p class="text-slate-700 font-black text-lg mb-2">Kombinasi Filter Tidak Valid</p>
            <p class="text-slate-600 text-sm mt-4 max-w-lg mx-auto">
                Silakan pilih salah satu kombinasi yang tersedia:
            </p>
            <div class="mt-6 space-y-3 max-w-md mx-auto">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-3">
                    <p class="text-slate-700 font-bold">✓ Alat Lengkap</p>
                    <p class="text-slate-600 text-xs mt-1">Kabupaten + SKPD + Jenis Data + Periode</p>
                </div>
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-3">
                    <p class="text-slate-700 font-bold">✓ Tanpa Kabupaten</p>
                    <p class="text-slate-600 text-xs mt-1">SKPD + Jenis Data + Periode</p>
                </div>
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-3">
                    <p class="text-slate-700 font-bold">✓ Tanpa SKPD</p>
                    <p class="text-slate-600 text-xs mt-1">Kabupaten + Jenis Data + Periode</p>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden text-center py-24">
            <div class="bg-gradient-to-br from-slate-100 to-slate-200 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                <i data-lucide="search-x" class="w-12 h-12 text-slate-500"></i>
            </div>
            <p class="text-slate-700 font-black text-lg mb-2">Tidak Ada File Ditemukan</p>
            <p class="text-slate-600 text-sm max-w-md mx-auto">Tidak ada file yang sesuai dengan filter yang Anda pilih. Coba ubah filter atau hubungi admin.</p>
        </div>

        <!-- No Data State -->
        <div id="no-data-state" class="hidden text-center py-24">
            <div class="bg-gradient-to-br from-indigo-100 to-blue-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                <i data-lucide="inbox" class="w-12 h-12 text-indigo-600"></i>
            </div>
            <p class="text-slate-700 font-black text-lg mb-2">Belum Ada File</p>
            <p class="text-slate-600 text-sm max-w-md mx-auto">Admin belum mengunggah file apapun. Silakan kembali lagi nanti untuk mengecek ketersediaan file.</p>
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
<?php /**PATH C:\xampp\htdocs\adikasn666\resources\views/user/dashboard.blade.php ENDPATH**/ ?>