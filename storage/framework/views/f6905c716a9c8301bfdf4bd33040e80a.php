<?php if($type == 'err'): ?>
    <?php echo e($message ?? "Transaksi error"); ?>

<?php else: ?>
    <?php switch($type):
        case ('qris'): ?>
            <?php if (isset($component)) { $__componentOriginale01c72cadde667e91dc79c4da80c5097 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale01c72cadde667e91dc79c4da80c5097 = $attributes; } ?>
<?php $component = App\View\Components\Qrcode::resolve(['src' => $src,'type' => $type,'mataUang' => $mataUang,'rp' => $Rp] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('Qrcode'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Qrcode::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'qr']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale01c72cadde667e91dc79c4da80c5097)): ?>
<?php $attributes = $__attributesOriginale01c72cadde667e91dc79c4da80c5097; ?>
<?php unset($__attributesOriginale01c72cadde667e91dc79c4da80c5097); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale01c72cadde667e91dc79c4da80c5097)): ?>
<?php $component = $__componentOriginale01c72cadde667e91dc79c4da80c5097; ?>
<?php unset($__componentOriginale01c72cadde667e91dc79c4da80c5097); ?>
<?php endif; ?>
            <?php break; ?>
        <?php default: ?>
    <?php endswitch; ?>
<?php endif; ?>
<div id="confirmation" class="d-flex flex-column align-items-center justify-content-center"></div>
<script>
    const route = Object.freeze({
        form : "<?php echo e(route('form.simulation')); ?>",
        poll : "<?php echo e(route('midtrans.statusNotif')); ?>",
        orderID: "<?php echo e($OrderId); ?>"
    });
</script>
<script src="<?php echo e(asset('js/custom.js')); ?>"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<?php /**PATH C:\laragon\www\wifi\resources\views/displaytransaction.blade.php ENDPATH**/ ?>