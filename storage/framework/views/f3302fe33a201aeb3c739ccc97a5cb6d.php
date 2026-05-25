<?php $__env->startSection('content'); ?>

<div class="manager-pagina">

    <div class="manager-pagina-header">
        <h2 class="manager-pagina-titel"><?php echo e(__('Effecten beheren')); ?></h2>
    </div>

    <div class="effects-manager-toolbar">
        <button
            id="add-function-btn"
            class="btn btn-add"
            type="button"
            aria-label="<?php echo e(__('Voeg nieuwe functie toe')); ?>"
        >
            <span aria-hidden="true">+</span> <?php echo e(__('Functie toevoegen')); ?>

        </button>

        <button
            id="reset-defaults-btn"
            class="btn btn-reset"
            type="button"
            aria-label="<?php echo e(__('Reset alle effecten naar standaardwaarden')); ?>"
            title="<?php echo e(__('Herstel alle effecten naar de standaardwaarden uit config/effects.php')); ?>"
        >
            <span aria-hidden="true">↻</span> <?php echo e(__('Reset naar defaults')); ?>

        </button>

        <div class="effects-manager-zoek">
            <input
                type="text"
                id="effects-manager-zoek-input"
                placeholder="<?php echo e(__('Zoek functie...')); ?>"
                aria-label="<?php echo e(__('Zoek functie...')); ?>"
                autocomplete="off"
            >
        </div>
    </div>

    <div class="effects-manager-tabel-wrapper" tabindex="0">
        <table
            class="effects-manager-tabel"
            role="table"
            aria-label="<?php echo e(__('Editable effecten per stadsfunctie')); ?>"
        >
            <thead>
                <tr role="row">
                    <th scope="col" class="th-functie"><?php echo e(__('Functie')); ?></th>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th scope="col" class="th-categorie"><?php echo e(__($category)); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <th scope="col" class="th-actions" aria-label="<?php echo e(__('Acties')); ?>"></th>
                </tr>
            </thead>
            <tbody id="effects-tbody">
                <?php $__empty_1 = true; $__currentLoopData = $groupedByFunction; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $functionName => $effects): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr role="row" class="effects-row" data-function="<?php echo e($functionName); ?>">
                        <td class="td-functienaam"><?php echo e(__($functionName)); ?></td>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $effect = $effects->firstWhere('category', $category);
                                $points = $effect?->points ?? 0;
                            ?>
                            <td role="cell" class="td-points">
                                <div class="effect-input-wrap" data-status="idle">
                                    <input
                                        type="number"
                                        class="effect-input"
                                        value="<?php echo e($points); ?>"
                                        min="-5"
                                        max="5"
                                        step="1"
                                        data-function="<?php echo e($functionName); ?>"
                                        data-category="<?php echo e($category); ?>"
                                        data-initial="<?php echo e($points); ?>"
                                        aria-label="<?php echo e(__($functionName)); ?> - <?php echo e(__($category)); ?>"
                                    />
                                </div>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td class="td-actions">
                            <button
                                class="btn-delete-function"
                                data-function="<?php echo e($functionName); ?>"
                                aria-label="<?php echo e(__('Verwijder')); ?> <?php echo e(__($functionName)); ?>"
                                type="button"
                                title="<?php echo e(__('Verwijder')); ?> <?php echo e(__($functionName)); ?>"
                            ><?php echo e(__('Verwijder')); ?></button>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr id="effects-empty-row">
                        <td colspan="<?php echo e(count($categories) + 2); ?>" class="effects-empty">
                            <?php echo e(__('Nog geen functies. Voeg er een toe!')); ?>

                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Hp\Downloads\metropolis\metropolis\resources\views/effecten-beheren.blade.php ENDPATH**/ ?>