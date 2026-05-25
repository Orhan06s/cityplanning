<input type="text" id="zoekterm" placeholder="<?php echo e(__('Zoek functie...')); ?>" autocomplete="off" aria-label="<?php echo e(__('Zoek functie...')); ?>">

<div id="library-lijst" role="list">
    <?php $__currentLoopData = $destinationsByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <p class="categorie-titel"><?php echo e(__($category)); ?></p>
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="library-item"
                 role="listitem"
                 draggable="true"
                 tabindex="0"
                 data-id="<?php echo e($item->id); ?>"
                 data-name="<?php echo e($item->getNaam()); ?>"
                 data-icon="<?php echo e($item->icon); ?>"
                 aria-label="<?php echo e($item->getNaam()); ?> - <?php echo e(__('sleep naar een cel om te plaatsen')); ?>">
                <span aria-hidden="true"><?php echo e($item->icon); ?></span> <?php echo e($item->getNaam()); ?>

            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <p id="geen-resultaten" style="display:none; color: gray;" role="status"><?php echo e(__('Geen functies gevonden.')); ?></p>
</div>
<?php /**PATH C:\Users\Hp\Downloads\metropolis\metropolis\resources\views/partials/library.blade.php ENDPATH**/ ?>