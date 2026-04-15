<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - ADIKASN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .dropzone-active { border-color: #2563eb !important; background-color: #eff6ff !important; }
        .toast { animation: slideIn .3s ease; }
        @keyframes slideIn { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .progress-bar { transition: width 0.3s ease; }
        .file-tag { display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .tag-pdf { background: #fee2e2; color: #ef4444; }
        .tag-doc { background: #dbeafe; color: #3b82f6; }
        .tag-xls { background: #d1fae5; color: #10b981; }
        .tag-img { background: #fef3c7; color: #f59e0b; }
        .tag-other { background: #f3f4f6; color: #6b7280; }
        .btn-loading { pointer-events: none; opacity: 0.7; }
        .btn-loading i { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen flex flex-col">
    <nav class="bg-white border-b border-slate-100 px-4 sm:px-8 h-16 flex justify-between items-center sticky top-0 z-40 shadow-sm">
        <div>
            <p class="text-sm font-bold text-slate-800">Panel Admin</p>
            <p class="text-xs text-slate-600 hidden sm:block">BKPSDM Kabupaten Tabalong</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden sm:flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-1.5 rounded-full text-xs font-bold">
                <i data-lucide="shield" class="w-3 h-3"></i>
                <span id="nav-name">Admin</span>
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

    <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 max-w-5xl w-full mx-auto">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Panel Admin</h1>
            <p class="text-slate-600 text-sm mt-1">Kelola file dan kategori drop list untuk pengguna</p>
        </div>

        <!-- UPLOAD FILE -->
        <div id="section-upload" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-4 sm:p-6 lg:p-8">
            <h2 class="text-base font-bold text-slate-800 mb-5 flex items-center gap-2">
                <i data-lucide="upload-cloud" class="w-5 h-5 text-blue-600"></i> Upload File untuk Pengguna
            </h2>
            <div id="dropzone" class="border-2 border-dashed border-slate-200 rounded-2xl p-6 sm:p-8 text-center hover:border-blue-400 hover:bg-blue-50 transition-all cursor-pointer">
                <div class="flex flex-col items-center">
                    <div class="bg-slate-50 p-4 rounded-full mb-3">
                        <i data-lucide="upload-cloud" class="w-8 h-8 text-slate-300"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-700">Tarik &amp; lepas file di sini</p>
                    <p class="text-xs text-slate-400 mt-1">atau klik untuk memilih file</p>
                    <p class="text-xs text-blue-500 mt-2 font-semibold">Excel (.xls/.xlsx) • PDF • Word (.doc/.docx) • Gambar (maks. 5MB)</p>
                    <input type="file" id="file-input" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.webp">
                </div>
            </div>

            <div id="upload-progress" class="hidden mt-4">
                <div class="flex justify-between text-xs text-slate-500 mb-1">
                    <span id="upload-filename">Membaca file...</span>
                    <span id="upload-percent">0%</span>
                </div>
                <div class="bg-slate-100 rounded-full h-2">
                    <div id="progress-bar" class="progress-bar bg-blue-500 h-2 rounded-full" style="width:0%"></div>
                </div>
            </div>

            <div id="upload-form" class="hidden mt-5 space-y-3 border-t border-slate-50 pt-5">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Label File (Opsional — untuk filter user)</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Kabupaten</label>
                        <select id="upload-kabupaten" class="w-full mt-1 px-3 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Satuan Unit Kerja</label>
                        <select id="upload-skpd" class="w-full mt-1 px-3 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Jenis Data</label>
                        <select id="upload-jenis" class="w-full mt-1 px-3 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Periode</label>
                        <select id="upload-periode" class="w-full mt-1 px-3 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Deskripsi File</label>
                    <input type="text" id="upload-desc" placeholder="Contoh: Data kepegawaian bulan Maret 2026" class="w-full mt-1 px-3 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button id="btn-confirm-upload" onclick="confirmUpload()" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl text-sm font-bold hover:bg-blue-700 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-lucide="check" class="w-4 h-4"></i> <span>Simpan &amp; Publikasikan</span>
                    </button>
                    <button id="btn-cancel-upload" onclick="cancelUpload()" class="px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        Batal
                    </button>
                </div>
            </div>
        </div>

        <!-- FILE TERSIMPAN -->
        <div id="section-files" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-4 sm:p-6 lg:p-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-5 gap-3">
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="folder-open" class="w-5 h-5 text-blue-600"></i> File Tersimpan
                    <span id="file-count-badge" class="hidden bg-blue-100 text-blue-700 text-xs font-black px-2 py-0.5 rounded-full"></span>
                </h2>
                <button onclick="clearAllFiles()" id="btn-clear-all" class="hidden text-xs text-red-500 hover:text-red-700 font-semibold px-3 py-1.5 hover:bg-red-50 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed w-full sm:w-auto">
                    Hapus Semua
                </button>
            </div>
            <div id="file-history" class="space-y-3 max-h-[480px] overflow-y-auto pr-1"></div>
        </div>

        <!-- KELOLA DROP LIST -->
        <div id="section-dropdown" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-4 sm:p-6 lg:p-8">
            <h2 class="text-base font-bold text-slate-800 mb-5 flex items-center gap-2">
                <i data-lucide="settings-2" class="w-5 h-5 text-blue-600"></i> Tambah &amp; Kelola Drop List
            </h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tambah Kategori Baru</p>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Kabupaten</label>
                        <input type="text" id="add-kabupaten" placeholder="Contoh: Kabupaten Tabalong" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Satuan Unit Kerja</label>
                        <input type="text" id="add-skpd" placeholder="Contoh: Dinas Kesehatan" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Jenis Data</label>
                        <input type="text" id="add-gol" placeholder="Contoh: Berdasarkan Jabatan" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Periode</label>
                        <input type="text" id="add-periode" placeholder="Contoh: 2026" class="w-full mt-1 px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-100">
                    </div>
                    <button id="btn-add-category" onclick="addDataManual()" class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kategori
                    </button>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Daftar Kategori</p>
                    <div id="filter-lists" class="space-y-3 max-h-72 overflow-y-auto pr-1"></div>
                </div>
            </div>
        </div>
    </main>

    <script>
        window.onload = () => {
            try {
                const nameEl = document.getElementById('nav-name');
                if (nameEl) {
                    nameEl.innerText = '<?php echo e(Auth::user()->nip); ?>' || 'Admin';
                }
                
                // Initialize dropzone first
                initDropzone();
                
                // Load data
                loadFilterLists().catch(err => {
                    console.error('Failed to load filter lists:', err);
                    showToast('Gagal memuat kategori!', 'error');
                });
                
                loadFileHistory().catch(err => {
                    console.error('Failed to load file history:', err);
                    showToast('Gagal memuat file history!', 'error');
                });
                
                // Initialize icons
                if (window.lucide) {
                    lucide.createIcons();
                }
            } catch (error) {
                console.error('Error initializing dashboard:', error);
                showToast('Terjadi kesalahan saat memuat dashboard!', 'error');
            }
        };

        // STATE MANAGEMENT
        let expandedSections = { kabupaten: false, skpd: false, jenisData: false, periode: false };
        let expandedFiles = false;
        let pendingFile = null;
        let isLoading = false;

        function setButtonLoading(btnId, isLoading) {
            const btn = document.getElementById(btnId);
            if (!btn) return;
            btn.disabled = isLoading;
            if (isLoading) {
                btn.classList.add('btn-loading');
            } else {
                btn.classList.remove('btn-loading');
            }
        }

        // FILTER LISTS
        function toggleFileHistory() {
            expandedFiles = !expandedFiles;
            loadFileHistory();
        }

        function toggleSection(type) {
            expandedSections[type] = !expandedSections[type];
            loadFilterLists();
        }

        function populateUploadDropdowns(categories) {
            const kabupatenEl = document.getElementById('upload-kabupaten');
            const skpdEl = document.getElementById('upload-skpd');
            const jenisEl = document.getElementById('upload-jenis');
            const periodeEl = document.getElementById('upload-periode');

            kabupatenEl.innerHTML = '<option value="">-- Pilih --</option>';
            skpdEl.innerHTML = '<option value="">-- Pilih --</option>';
            jenisEl.innerHTML = '<option value="">-- Pilih --</option>';
            periodeEl.innerHTML = '<option value="">-- Pilih --</option>';

            if (categories && categories.kabupaten && categories.kabupaten.length > 0) {
                categories.kabupaten.forEach(k => kabupatenEl.innerHTML += `<option value="${k.id}">${k.name}</option>`);
            }
            if (categories && categories.skpd && categories.skpd.length > 0) {
                categories.skpd.forEach(s => skpdEl.innerHTML += `<option value="${s.id}">${s.name}</option>`);
            }
            if (categories && categories.jenisData && categories.jenisData.length > 0) {
                categories.jenisData.forEach(j => jenisEl.innerHTML += `<option value="${j.id}">${j.name}</option>`);
            }
            if (categories && categories.periode && categories.periode.length > 0) {
                categories.periode.forEach(p => periodeEl.innerHTML += `<option value="${p.id}">${p.name}</option>`);
            }
        }

        async function loadFilterLists() {
            try {
                console.log('Loading filter lists...');
                const res = await fetch('<?php echo e(route("admin.categories")); ?>?t=' + new Date().getTime());
                if (!res.ok) {
                    throw new Error('HTTP ' + res.status + ': Failed to fetch categories');
                }
                
                const data = await res.json();
                console.log('Categories loaded:', data);
                
                if (!data.success) {
                    showToast('Gagal memuat kategori!', 'error');
                    return;
                }

                const categories = data.categories;
                const container = document.getElementById('filter-lists');
                if (!container) {
                    console.error('Filter lists container not found');
                    return;
                }
                
                const sections = [
                    { label: 'Kabupaten', type: 'kabupaten', items: categories.kabupaten || [] },
                    { label: 'Satuan Unit Kerja', type: 'skpd', items: categories.skpd || [] },
                    { label: 'Jenis Data', type: 'jenisData', items: categories.jenisData || [] },
                    { label: 'Berdasarkan Periode', type: 'periode', items: categories.periode || [] }
                ];
                const totalItems = (categories.kabupaten?.length || 0) + (categories.skpd?.length || 0) + (categories.jenisData?.length || 0) + (categories.periode?.length || 0);
                
                console.log('Total items:', totalItems, 'Sections:', sections);
                
                if (totalItems === 0) {
                    container.innerHTML = `<div class="text-center py-8 text-slate-300"><i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2"></i><p class="text-sm text-slate-400">Belum ada kategori</p></div>`;
                    if (window.lucide) lucide.createIcons();
                    populateUploadDropdowns(categories);
                    return;
                }

                container.innerHTML = sections.map(s => {
                    if (!s.items || !s.items.length) return '';
                    
                    const isExpanded = expandedSections[s.type];
                    const displayItems = isExpanded ? s.items : s.items.slice(0, 2);
                    const hasMore = s.items.length > 2;

                    return `<div class="mb-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">${s.label}</p>
                        <div class="space-y-1.5">
                        ${displayItems.map(item => `<div class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-lg border border-slate-100 group hover:border-red-200">
                            <span class="text-sm text-slate-700 font-medium truncate mr-2">${item.name || item}</span>
                            <button id="btn-delete-${item.id}" onclick="deleteFilterItem(${item.id},'${s.type}','${(item.name || item).replace(/'/g,"\\'")}')" class="shrink-0 text-slate-300 hover:text-red-500 p-1 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed" type="button">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>`).join('')}
                        </div>
                        ${hasMore ? `
                            <button onclick="toggleSection('${s.type}')" class="mt-2 text-[10px] font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors" type="button">
                                <i data-lucide="${isExpanded ? 'chevron-up' : 'chevron-down'}" class="w-3 h-3"></i>
                                ${isExpanded ? 'Sembunyikan' : `Lihat Semua (${s.items.length})`}
                            </button>` : ''}
                    </div>`;
                }).join('');
                
                if (window.lucide) lucide.createIcons();
                populateUploadDropdowns(categories);
            } catch (error) {
                console.error('Error loading filter lists:', error);
                showToast('Terjadi kesalahan saat memuat kategori!', 'error');
            }
        }

        async function deleteFilterItem(id, type, name) {
            if (!confirm(`Hapus "${name}"?`)) return;
            
            setButtonLoading('btn-delete-' + id, true);
            
            try {
                const res = await fetch('<?php echo e(route("admin.category.delete", ":id")); ?>'.replace(':id', id), {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Kategori berhasil dihapus!', 'success');
                    await loadFilterLists();
                } else {
                    showToast(data.message || 'Hapus gagal!', 'error');
                }
            } catch (error) {
                console.error('Error deleting filter item:', error);
                showToast('Terjadi kesalahan saat menghapus kategori!', 'error');
            } finally {
                setButtonLoading('btn-delete-' + id, false);
            }
        }

        async function addDataManual() {
            const kabupaten = document.getElementById('add-kabupaten').value.trim();
            const skpd = document.getElementById('add-skpd').value.trim();
            const jenisData = document.getElementById('add-gol').value.trim();
            const periode = document.getElementById('add-periode').value.trim();
            
            if (!kabupaten && !skpd && !jenisData && !periode) {
                showToast('Isi minimal satu field!', 'error');
                return;
            }

            setButtonLoading('btn-add-category', true);

            try {
                let successCount = 0;
                let lastError = '';

                if (kabupaten) {
                    try {
                        const res = await fetch('<?php echo e(route("admin.category.add")); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                            },
                            body: JSON.stringify({
                                type: 'kabupaten',
                                name: kabupaten
                            }),
                        });
                        const data = await res.json();
                        console.log('Kabupaten response:', { status: res.status, data: data });
                        
                        if (res.ok && data.success) {
                            successCount++;
                        } else {
                            lastError = data.message || 'Gagal menambah kabupaten';
                        }
                    } catch (err) {
                        lastError = 'Error kabupaten: ' + err.message;
                        console.error('Kabupaten error:', err);
                    }
                }

                if (skpd) {
                    try {
                        const res = await fetch('<?php echo e(route("admin.category.add")); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                            },
                            body: JSON.stringify({
                                type: 'skpd',
                                name: skpd
                            }),
                        });
                        const data = await res.json();
                        console.log('SKPD response:', { status: res.status, data: data });
                        
                        if (res.ok && data.success) {
                            successCount++;
                        } else {
                            lastError = data.message || 'Gagal menambah SKPD';
                        }
                    } catch (err) {
                        lastError = 'Error SKPD: ' + err.message;
                        console.error('SKPD error:', err);
                    }
                }

                if (jenisData) {
                    try {
                        const res = await fetch('<?php echo e(route("admin.category.add")); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                            },
                            body: JSON.stringify({
                                type: 'jenis_data',
                                name: jenisData
                            }),
                        });
                        const data = await res.json();
                        console.log('Jenis Data response:', { status: res.status, data: data });
                        
                        if (res.ok && data.success) {
                            successCount++;
                        } else {
                            lastError = data.message || 'Gagal menambah Jenis Data';
                        }
                    } catch (err) {
                        lastError = 'Error Jenis Data: ' + err.message;
                        console.error('Jenis Data error:', err);
                    }
                }

                if (periode) {
                    try {
                        const res = await fetch('<?php echo e(route("admin.category.add")); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                            },
                            body: JSON.stringify({
                                type: 'periode',
                                name: periode
                            }),
                        });
                        const data = await res.json();
                        console.log('Periode response:', { status: res.status, data: data });
                        
                        if (res.ok && data.success) {
                            successCount++;
                        } else {
                            lastError = data.message || 'Gagal menambah Periode';
                        }
                    } catch (err) {
                        lastError = 'Error Periode: ' + err.message;
                        console.error('Periode error:', err);
                    }
                }

                if (successCount > 0) {
                    document.getElementById('add-kabupaten').value = '';
                    document.getElementById('add-skpd').value = '';
                    document.getElementById('add-gol').value = '';
                    document.getElementById('add-periode').value = '';
                    await loadFilterLists();
                    showToast(`${successCount} kategori berhasil ditambahkan!`, 'success');
                } else if (lastError) {
                    showToast(lastError, 'error');
                    console.error('Last error:', lastError);
                } else {
                    showToast('Gagal menambahkan kategori!', 'error');
                }
            } catch (error) {
                console.error('Error adding category:', error);
                showToast('Terjadi kesalahan: ' + error.message, 'error');
            } finally {
                setButtonLoading('btn-add-category', false);
            }
        }

        // FILE STORAGE
        async function loadFileHistory() {
            try {
                const res = await fetch('<?php echo e(route("admin.files")); ?>');
                if (!res.ok) {
                    throw new Error('Failed to fetch files');
                }
                
                const data = await res.json();
                if (!data.success) {
                    showToast('Gagal memuat file history!', 'error');
                    return;
                }
                
                const files = data.files || [];
                const el = document.getElementById('file-history');
                const badge = document.getElementById('file-count-badge');
                const clearBtn = document.getElementById('btn-clear-all');
                
                if (!el) {
                    console.error('File history container not found');
                    return;
                }
                
                if (files.length === 0) {
                    el.innerHTML = `<div class="text-center py-10 text-slate-300"><i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2"></i><p class="text-sm font-medium text-slate-400">Belum ada file yang diupload</p></div>`;
                    badge.classList.add('hidden');
                    clearBtn.classList.add('hidden');
                    if (window.lucide) lucide.createIcons();
                    return;
                }

                badge.innerText = files.length;
                badge.classList.remove('hidden');
                clearBtn.classList.remove('hidden');

                const displayFiles = expandedFiles ? files : files.slice(0, 3);
                const hasMore = files.length > 3;

                let html = displayFiles.map(f => {
                    const ext = f.ext || 'unknown';
                    const tagCls = ext==='pdf'?'tag-pdf':['doc','docx'].includes(ext)?'tag-doc':['xls','xlsx'].includes(ext)?'tag-xls':['jpg','jpeg','png','gif','webp'].includes(ext)?'tag-img':'tag-other';
                    const sz = f.size ? (f.size/1024).toFixed(1)+' KB' : '-';
                    return `<div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all group">
                        <div class="shrink-0 w-10 h-10 bg-white rounded-xl flex items-center justify-center border border-slate-100 shadow-sm">
                            <i data-lucide="${getFileIcon(ext)}" class="w-5 h-5 text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                        </div>
                        <div class="flex-1 min-w-0 w-full">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-bold text-slate-800 truncate">${f.filename || 'Unknown'}</p>
                                <span class="file-tag ${tagCls}">${ext.toUpperCase()}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-1 flex-wrap text-xs text-slate-400">
                                <span>${f.date || 'Unknown'}</span>
                                ${f.size ? `<span>•</span><span class="font-semibold text-blue-500">${sz}</span>` : ''}
                                ${f.kabupaten ? `<span class="bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full text-[10px] font-bold">${f.kabupaten}</span>` : ''}
                                ${f.skpd ? `<span>•</span><span>${f.skpd}</span>` : ''}
                                ${f.jenis ? `<span class="bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full text-[10px] font-bold">${f.jenis}</span>` : ''}
                                ${f.periode ? `<span class="bg-blue-50 text-blue-500 px-2 py-0.5 rounded-full text-[10px] font-bold">${f.periode}</span>` : ''}
                            </div>
                            ${f.desc ? `<p class="text-xs text-slate-400 mt-1 italic truncate">${f.desc}</p>` : ''}
                        </div>
                        <button id="btn-delete-${f.id}" onclick="deleteFile(${f.id})" class="shrink-0 text-slate-300 hover:text-red-500 hover:bg-red-50 p-2 rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed" title="Hapus" type="button">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>`;
                }).join('');

                if (hasMore) {
                    html += `
                        <div class="pt-2 flex justify-center">
                            <button onclick="toggleFileHistory()" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors py-2 px-4 bg-blue-50 rounded-xl" type="button">
                                <i data-lucide="${expandedFiles ? 'chevron-up' : 'chevron-down'}" class="w-4 h-4"></i>
                                ${expandedFiles ? 'Sembunyikan' : `Lihat Semua File (${files.length})`}
                            </button>
                        </div>`;
                }

                el.innerHTML = html;
                if (window.lucide) lucide.createIcons();
            } catch (error) {
                console.error('Error loading file history:', error);
                showToast('Terjadi kesalahan saat memuat file!', 'error');
            }
        }

        async function deleteFile(id) {
            if (!confirm('Hapus file ini? Pengguna tidak akan bisa mengaksesnya lagi.')) return;
            
            setButtonLoading('btn-delete-' + id, true);
            
            try {
                const res = await fetch(`<?php echo e(route("admin.file.delete", ":id")); ?>`.replace(':id', id), {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'}
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'File berhasil dihapus!', 'success');
                    loadFileHistory();
                } else {
                    showToast(data.message || 'Gagal menghapus file!', 'error');
                }
            } catch (error) {
                console.error('Error deleting file:', error);
                showToast('Terjadi kesalahan saat menghapus file!', 'error');
            } finally {
                setButtonLoading('btn-delete-' + id, false);
            }
        }

        async function clearAllFiles() {
            if (!confirm('Hapus SEMUA file? Tindakan ini tidak bisa dibatalkan.')) return;
            
            setButtonLoading('btn-clear-all', true);
            
            try {
                const res = await fetch('<?php echo e(route("admin.files.clear")); ?>', {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'}
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Semua file berhasil dihapus!', 'success');
                    loadFileHistory();
                } else {
                    showToast(data.message || 'Gagal menghapus file!', 'error');
                }
            } catch (error) {
                console.error('Error clearing files:', error);
                showToast('Terjadi kesalahan saat menghapus file!', 'error');
            } finally {
                setButtonLoading('btn-clear-all', false);
            }
        }

        // DROPZONE
        function initDropzone() {
            const dz = document.getElementById('dropzone');
            const fi = document.getElementById('file-input');
            
            if (!dz || !fi) {
                console.error('Dropzone or file input element not found');
                return;
            }
            
            dz.addEventListener('click', () => fi.click());
            
            dz.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.stopPropagation();
                dz.classList.add('dropzone-active');
            });
            
            dz.addEventListener('dragleave', (e) => {
                e.preventDefault();
                e.stopPropagation();
                dz.classList.remove('dropzone-active');
            });
            
            dz.addEventListener('drop', (e) => {
                e.preventDefault();
                e.stopPropagation();
                dz.classList.remove('dropzone-active');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFileSelect(files[0]);
                }
            });
            
            fi.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFileSelect(e.target.files[0]);
                }
            });
        }

        function handleFileSelect(file) {
            if (!file) return;
            
            // Check file size
            if (file.size > 5 * 1024 * 1024) {
                showToast('File terlalu besar! Maksimal 5MB.', 'error');
                document.getElementById('file-input').value = '';
                return;
            }
            
            // Check file type (extension-based)
            const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'webp'];
            const filename = file.name.toLowerCase();
            const fileExtension = filename.split('.').pop();
            
            if (!allowedExtensions.includes(fileExtension)) {
                showToast(`Tipe file tidak didukung! Gunakan: Excel (.xls/.xlsx), PDF, Word (.doc/.docx), atau Gambar.`, 'error');
                document.getElementById('file-input').value = '';
                return;
            }
            
            pendingFile = file;
            document.getElementById('upload-form').classList.remove('hidden');
            
            const progress = document.getElementById('upload-progress');
            const bar = document.getElementById('progress-bar');
            const pct = document.getElementById('upload-percent');
            
            progress.classList.remove('hidden');
            document.getElementById('upload-filename').innerText = file.name;
            
            let p = 0;
            const iv = setInterval(() => {
                p += Math.random() * 25;
                if (p >= 100) { 
                    p = 100; 
                    clearInterval(iv); 
                }
                bar.style.width = p + '%';
                pct.innerText = Math.floor(p) + '%';
            }, 80);
            
            loadFilterLists().catch(() => {
                console.error('Failed to load filter lists');
            });
            document.getElementById('file-input').value = '';
        }

        async function confirmUpload() {
            if (!pendingFile) {
                showToast('Tidak ada file yang dipilih!', 'error');
                return;
            }
            
            setButtonLoading('btn-confirm-upload', true);
            setButtonLoading('btn-cancel-upload', true);
            
            try {
                const kabupatenId = document.getElementById('upload-kabupaten').value;
                const skpdId = document.getElementById('upload-skpd').value;
                const jenisId = document.getElementById('upload-jenis').value;
                const periodeId = document.getElementById('upload-periode').value;
                const desc = document.getElementById('upload-desc').value.trim();

                const formData = new FormData();
                formData.append('file', pendingFile);
                if (kabupatenId) formData.append('kabupaten_id', kabupatenId);
                if (skpdId) formData.append('skpd_id', skpdId);
                if (jenisId) formData.append('jenis_data_id', jenisId);
                if (periodeId) formData.append('periode_id', periodeId);
                if (desc) formData.append('description', desc);

                const res = await fetch('<?php echo e(route("admin.upload")); ?>', {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
                    body: formData,
                });
                
                if (!res.ok) {
                    const errData = await res.json();
                    throw new Error(errData.message || 'Upload gagal');
                }
                
                const data = await res.json();
                if (data.success) {
                    await loadFileHistory();
                    showToast(data.message || 'File berhasil diupload!', 'success');
                    cancelUpload();
                } else {
                    showToast(data.message || 'Upload gagal!', 'error');
                }
            } catch (error) {
                console.error('Error uploading file:', error);
                showToast(error.message || 'Terjadi kesalahan saat mengupload file!', 'error');
            } finally {
                setButtonLoading('btn-confirm-upload', false);
                setButtonLoading('btn-cancel-upload', false);
            }
        }

        function cancelUpload() {
            pendingFile = null;
            document.getElementById('upload-form').classList.add('hidden');
            document.getElementById('upload-progress').classList.add('hidden');
            document.getElementById('progress-bar').style.width = '0%';
            document.getElementById('upload-percent').innerText = '0%';
            document.getElementById('upload-kabupaten').value = '';
            document.getElementById('upload-skpd').value = '';
            document.getElementById('upload-jenis').value = '';
            document.getElementById('upload-periode').value = '';
            document.getElementById('upload-desc').value = '';
            document.getElementById('file-input').value = '';
        }

        function getFileIcon(ext) {
            if (ext === 'pdf') return 'file-text';
            if (['doc','docx'].includes(ext)) return 'file-word';
            if (['xls','xlsx'].includes(ext)) return 'table-2';
            if (['jpg','jpeg','png','gif','webp'].includes(ext)) return 'image';
            return 'file';
        }

        function showToast(msg, type='success') {
            if (!msg) return;
            
            const t = document.getElementById('toast');
            if (!t) {
                console.error('Toast element not found');
                return;
            }
            
            const bgColor = type === 'success' ? 'bg-emerald-500' : 'bg-red-500';
            const icon = type === 'success' ? 'check-circle' : 'alert-circle';
            
            t.className = `toast fixed bottom-6 right-6 z-50 px-5 py-3.5 rounded-2xl shadow-xl text-sm font-bold flex items-center gap-2 max-w-xs ${bgColor} text-white`;
            t.innerHTML = `<i data-lucide="${icon}" class="w-4 h-4 shrink-0"></i><span>${msg}</span>`;
            t.classList.remove('hidden');
            
            if (lucide) {
                lucide.createIcons();
            }
            
            setTimeout(() => {
                if (t) t.classList.add('hidden');
            }, 4000);
        }
    </script>
</body>
</html><?php /**PATH C:\Users\user\Downloads\adikasn666\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>