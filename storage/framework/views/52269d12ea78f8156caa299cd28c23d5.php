<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ADIKASN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .bg-login { 
            background-image: url('/images/tabalong.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
            min-height: 100vh;
        }
        
        /* Overlay untuk readability */
        .bg-login::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(2px);
            pointer-events: none;
            z-index: 0;
        }
        
        .tab-active { background: white; color: #2563eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .tab-inactive { color: #94a3b8; }
        input:focus { box-shadow: 0 0 0 4px rgba(37,99,235,0.1); }
    </style>
</head>
<body class="bg-login min-h-screen flex items-center justify-center p-4 sm:p-6 relative z-10">
    <div class="bg-white/95 backdrop-blur-sm rounded-[2rem] sm:rounded-[2.5rem] shadow-2xl w-full max-w-md p-6 sm:p-10 flex flex-col items-center relative z-20">
        <div class="text-center mb-6 sm:mb-8">
            <div class="mb-4 flex justify-center">
                <img src="<?php echo e(asset('images/tabalong-logo.png')); ?>" alt="Logo Tabalong" class="w-24 sm:w-28 h-auto object-contain drop-shadow-lg">
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">ADIKASN</h1>
            <p class="text-slate-600 text-xs sm:text-sm mt-2">Anjungan Data Informasi Kepegawaian<br>Aparatur Sipil Negara</p>
            <div class="mt-4 inline-flex items-center gap-2 bg-blue-50 text-blue-600 px-4 py-1.5 rounded-full text-xs font-bold border border-blue-100">
                <span id="role-label">Masuk sebagai User</span>
            </div>
        </div>

        <!-- Tab Role -->
        <div class="bg-slate-100 p-1.5 rounded-2xl flex w-full mb-6 sm:mb-8">
            <button id="tab-user" onclick="setRole('user')" class="tab-active flex-1 flex items-center justify-center gap-2 py-2.5 sm:py-3 rounded-xl text-sm font-bold transition-all">
                <i data-lucide="user" class="w-4 h-4"></i> User
            </button>
            <button id="tab-admin" onclick="setRole('admin')" class="tab-inactive flex-1 flex items-center justify-center gap-2 py-2.5 sm:py-3 rounded-xl text-sm font-bold transition-all hover:text-slate-700">
                <i data-lucide="shield" class="w-4 h-4"></i> Admin
            </button>
        </div>

        <!-- Error -->
        <div id="error-msg" class="hidden w-full mb-4 bg-red-50 border border-red-200 text-red-600 text-sm rounded-2xl px-4 py-3 flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
            <span id="error-text">NIP / Username atau password salah.</span>
        </div>

        <!-- Form -->
        <div class="w-full space-y-3 sm:space-y-4">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 pointer-events-none">
                    <i data-lucide="user-circle" class="w-4 h-4"></i>
                </span>
                <input type="text" id="input-nip" placeholder="NIP / Username"
                    onkeydown="if(event.key==='Enter') document.getElementById('input-pass').focus()"
                    class="w-full pl-11 pr-4 py-3.5 sm:py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm outline-none transition-all">
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 pointer-events-none">
                    <i data-lucide="lock" class="w-4 h-4"></i>
                </span>
                <input type="password" id="input-pass" placeholder="Password"
                    onkeydown="if(event.key==='Enter') handleLogin()"
                    class="w-full pl-11 pr-12 py-3.5 sm:py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm outline-none transition-all">
                <button onclick="togglePass()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="eye" class="w-4 h-4" id="eye-icon"></i>
                </button>
            </div>
            <button onclick="handleLogin()"
                class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white py-3.5 sm:py-4 rounded-2xl font-bold flex items-center justify-center gap-2 shadow-xl shadow-blue-100 transition-all active:scale-95 mt-2">
                <i data-lucide="log-in" class="w-5 h-5"></i>
                <span>Masuk</span>
            </button>
        </div>

        
        <div class="mt-4 pt-4 border-t border-slate-200 w-full text-center">
            <p class="text-[10px] text-slate-700 font-bold tracking-[0.2em] uppercase">BKPSDM Kabupaten Tabalong</p>
            <p class="text-[9px] text-slate-700 mt-1">© 2026 Hak Cipta Dilindungi</p>
        </div>
    </div>

    <script>
        let activeRole = 'user';

        function setRole(r) {
            activeRole = r;
            const isUser = r === 'user';
            document.getElementById('tab-user').className = isUser ? 'tab-active flex-1 flex items-center justify-center gap-2 py-2.5 sm:py-3 rounded-xl text-sm font-bold transition-all' : 'tab-inactive flex-1 flex items-center justify-center gap-2 py-2.5 sm:py-3 rounded-xl text-sm font-bold transition-all hover:text-slate-700';
            document.getElementById('tab-admin').className = !isUser ? 'tab-active flex-1 flex items-center justify-center gap-2 py-2.5 sm:py-3 rounded-xl text-sm font-bold transition-all' : 'tab-inactive flex-1 flex items-center justify-center gap-2 py-2.5 sm:py-3 rounded-xl text-sm font-bold transition-all hover:text-slate-700';
            document.getElementById('role-label').innerText = `Masuk sebagai ${isUser ? 'User' : 'Admin'}`;
            document.getElementById('error-msg').classList.add('hidden');
            lucide.createIcons();
        }

        async function handleLogin() {
            const nip = document.getElementById('input-nip').value.trim();
            const pass = document.getElementById('input-pass').value;
            if (!nip || !pass) { showError('NIP / Username dan password wajib diisi.'); return; }

            try {
                const res = await fetch('<?php echo e(route("login.post")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    },
                    body: JSON.stringify({
                        nip: nip,
                        password: pass,
                        role: activeRole,
                    }),
                });

                const data = await res.json();
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    showError(data.message || 'Login gagal!');
                }
            } catch (e) {
                showError('Terjadi kesalahan. Silakan coba lagi.');
            }
        }

        function showError(msg) {
            const el = document.getElementById('error-msg');
            document.getElementById('error-text').innerText = msg;
            el.classList.remove('hidden');
            lucide.createIcons();
        }

        function togglePass() {
            const input = document.getElementById('input-pass');
            const icon = document.getElementById('eye-icon');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.setAttribute('data-lucide', input.type === 'password' ? 'eye' : 'eye-off');
            lucide.createIcons();
        }

        window.onload = () => {
            lucide.createIcons();
        };
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\adikasn666\resources\views/auth/login.blade.php ENDPATH**/ ?>