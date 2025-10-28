<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate QRIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col items-center justify-center h-screen bg-gray-100">

    
    <div class="flex flex-col items-center mb-6">
        <svg xmlns="http://www.w3.org/2000/svg"
             fill="none"
             viewBox="0 0 24 24"
             stroke-width="1.5"
             stroke="currentColor"
             class="w-20 h-20 text-blue-500">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M8.288 15.712a5 5 0 017.424 0M5.1 12.525a9 9 0 0113.8 0M2.292 9.292a13 13 0 0119.416 0M12 18h.01" />
        </svg>
        <h1 class="text-2xl font-semibold text-gray-700 mt-3">Beli Wifi</h1>
    </div>

    
    <form action="<?php echo e(route('generate.qr')); ?>" method="POST" class="bg-white p-6 rounded-2xl shadow-md w-80">
        <?php echo csrf_field(); ?>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm mb-1">Nama</label>
            <input type="text" name="name"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200"
                   placeholder="Nama lengkap" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm mb-1">NIM</label>
            <input type="number" name="NIM"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200"
                   placeholder="Nomor Induk Mahasiswa" required>
        </div>

        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm mb-1">Durasi</label>
            <div class="flex items-center gap-2">
                <input type="number" id="duration" name="duration"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200"
                       placeholder="Masukkan detik" min="1" required>
                <span class="text-gray-600 text-sm whitespace-nowrap">10 per detik</span>
            </div>
        </div>

        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm mb-1">Total Harga (Rp)</label>
            <input type="text" id="total" name="total"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700"
                   readonly>
        </div>

        <button type="submit"
                class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition">
            Beli dengan Qris
        </button>
    </form>

    <script>
        const durationInput = document.getElementById('duration');
        const totalInput = document.getElementById('total');

        durationInput.addEventListener('input', () => {
            const detik = parseInt(durationInput.value) || 0;
            const total = detik * 10;
            totalInput.value = total.toLocaleString('id-ID');
        });
    </script>

</body>
</html>
<?php /**PATH C:\laragon\www\wifi\resources\views/LOG.blade.php ENDPATH**/ ?>