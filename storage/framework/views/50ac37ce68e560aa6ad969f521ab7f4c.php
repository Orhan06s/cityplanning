

<?php $__env->startSection('content'); ?>

<?php echo $__env->make('partials.totalen', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="container">

    <div class="library">
        <h2><?php echo e(__('Functiebibliotheek')); ?></h2>
        <?php echo $__env->make('partials.library', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('partials.effects', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <div class="grid-wrapper">
        <h2><?php echo e(__('Stadsgebied')); ?></h2>
        <?php echo $__env->make('partials.grid', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Hp\Downloads\metropolis\metropolis\resources\views/metropolis.blade.php ENDPATH**/ ?>