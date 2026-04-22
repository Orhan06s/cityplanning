<div class="grid" role="grid" aria-label="<?php echo e(__('Stadsgebied grid')); ?>">
    <?php $__currentLoopData = $gridCells; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="cel <?php echo e($cell->destination ? 'bezet' : 'leeg'); ?>"
             role="gridcell"
             tabindex="0"
             data-cell-id="<?php echo e($cell->id); ?>"
             aria-label="Cel <?php echo e($cell->position + 1); ?>: <?php echo e($cell->destination ? $cell->destination->getNaam() : __('leeg')); ?>">

            <?php if($cell->destination): ?>
                <span class="cel-icon" aria-hidden="true"><?php echo e($cell->destination->icon); ?></span>
                <span class="cel-naam"><?php echo e($cell->destination->getNaam()); ?></span>

                <form action="<?php echo e(route('remove')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="cell_id" value="<?php echo e($cell->id); ?>">
                    <button type="submit" class="verwijder-btn"
                            aria-label="<?php echo e(__('Verwijder')); ?> <?php echo e($cell->destination->getNaam()); ?>">✕</button>
                </form>
            <?php else: ?>
                <span class="leeg-tekst" aria-hidden="true"><?php echo e(__('Sleep hier')); ?></span>
            <?php endif; ?>

        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH C:\Users\Hp\Downloads\metropolis\metropolis\resources\views/partials/grid.blade.php ENDPATH**/ ?>