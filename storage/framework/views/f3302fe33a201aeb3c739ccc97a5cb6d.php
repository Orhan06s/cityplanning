<?php $__env->startSection('content'); ?>

<div class="manager-pagina">

    <div class="manager-pagina-header">
        <h2 class="manager-pagina-titel"><?php echo e(__('Effecten beheren')); ?></h2>
    </div>

    <p class="manager-pagina-tekst">
        <?php echo e(__('Pas hier de effect-waarden aan voor elke stadsfunctie per categorie.')); ?>

        <span class="effects-manager-hint"><?php echo e(__('Wijzigingen worden automatisch opgeslagen en zijn meteen actief op de grid.')); ?></span>
    </p>

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

        <span id="effects-manager-stats" class="effects-manager-stats" aria-live="polite"></span>
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
                                    <span class="effect-status" aria-hidden="true"></span>
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


<div
    id="add-function-modal"
    class="submodal"
    role="dialog"
    aria-labelledby="add-function-titel"
    aria-hidden="true"
    aria-modal="true"
    hidden
>
    <div class="submodal-inner">
        <h3 id="add-function-titel" class="submodal-titel"><?php echo e(__('Nieuwe functie toevoegen')); ?></h3>
        <p class="submodal-tekst"><?php echo e(__('Voer de naam van de nieuwe stadsfunctie in. Alle effect-waarden beginnen op 0.')); ?></p>

        <form id="add-function-form" novalidate>
            <label for="add-function-input" class="submodal-label"><?php echo e(__('Functienaam')); ?></label>
            <input
                type="text"
                id="add-function-input"
                class="submodal-input"
                maxlength="50"
                required
                autocomplete="off"
                aria-describedby="add-function-fout"
            />
            <p id="add-function-fout" class="submodal-fout" role="alert" hidden></p>

            <div class="submodal-knoppen">
                <button type="button" class="btn btn-cancel" data-close-submodal><?php echo e(__('Annuleer')); ?></button>
                <button type="submit" class="btn btn-add"><?php echo e(__('Toevoegen')); ?></button>
            </div>
        </form>
    </div>
</div>


<div
    id="reset-defaults-modal"
    class="submodal"
    role="alertdialog"
    aria-labelledby="reset-defaults-titel"
    aria-hidden="true"
    aria-modal="true"
    hidden
>
    <div class="submodal-inner">
        <h3 id="reset-defaults-titel" class="submodal-titel"><?php echo e(__('Reset naar standaardwaarden?')); ?></h3>
        <p class="submodal-tekst">
            <?php echo e(__('Dit verwijdert alle huidige effect-waarden en herstelt ze naar de defaults.')); ?>

            <br><br>
            <strong><?php echo e(__('Eigen aanpassingen gaan verloren!')); ?></strong>
        </p>

        <div class="submodal-knoppen">
            <button type="button" class="btn btn-cancel" data-close-submodal><?php echo e(__('Annuleer')); ?></button>
            <button type="button" id="reset-defaults-bevestig" class="btn btn-danger"><?php echo e(__('Ja, reset')); ?></button>
        </div>
    </div>
</div>


<div
    id="delete-function-modal"
    class="submodal"
    role="alertdialog"
    aria-labelledby="delete-function-titel"
    aria-hidden="true"
    aria-modal="true"
    hidden
>
    <div class="submodal-inner">
        <h3 id="delete-function-titel" class="submodal-titel"><?php echo e(__('Functie verwijderen?')); ?></h3>
        <p class="submodal-tekst">
            <?php echo e(__('Weet je zeker dat je deze functie wilt verwijderen?')); ?>

            <strong id="delete-function-naam"></strong>
        </p>

        <div class="submodal-knoppen">
            <button type="button" class="btn btn-cancel" data-close-submodal><?php echo e(__('Annuleer')); ?></button>
            <button type="button" id="delete-function-bevestig" class="btn btn-danger"><?php echo e(__('Verwijder')); ?></button>
        </div>
    </div>
</div>


<div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Hp\Downloads\metropolis\metropolis\resources\views/effecten-beheren.blade.php ENDPATH**/ ?>