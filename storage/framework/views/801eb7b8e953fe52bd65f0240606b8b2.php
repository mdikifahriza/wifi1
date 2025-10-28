<div class="flex flex-col items-center justify-center 
            p-6 rounded-xl shadow-lg bg-white 
            max-w-sm w-full mx-auto">

    <div class="text-lg font-semibold text-gray-800 mb-4 text-center">
        Pembayaran melalui <span id="tipe-pembayaran" class="font-bold uppercase"><?php echo e($type); ?></span>
    </div>

    <!-- Gambar QR Code dari Midtrans -->
    <img src="<?php echo e($src); ?>" alt="QR Code Transaksi"
         class="w-full max-w-[200px] h-auto border-4 border-gray-100 rounded-lg p-2 bg-white transition-all duration-300 hover:shadow-xl"
         onerror="this.onerror=null;this.src='https://placehold.co/200x200/FF5733/ffffff?text=QR+Error';"
         style="aspect-ratio: 1/1; object-fit: contain;">

    <div class="text-3xl font-extrabold text-green-600 mt-5 tracking-wider">
        <?php echo e($mataUang . " " . number_format($rp, 0, ',', '.')); ?>

    </div>
    <p class="text-sm text-gray-500 mt-1">Total pembayaran</p>
</div>
<?php /**PATH C:\laragon\www\wifi\resources\views/qrcode.blade.php ENDPATH**/ ?>