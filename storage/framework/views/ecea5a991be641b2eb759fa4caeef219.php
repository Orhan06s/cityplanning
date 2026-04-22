
<div class="effecten-panel">
    <div class="effect-item">
        <div class="effect-header">
            <span class="effect-label">🌿 Leefbaarheid</span>
            <span class="effect-value" aria-label="Leefbaarheid: <?php echo e($effecten['leefbaarheid']); ?> van <?php echo e($effecten['max']); ?>">
                <?php echo e($effecten['leefbaarheid']); ?>

            </span>
        </div>
        <div class="effect-bar" role="progressbar"
             aria-valuenow="<?php echo e($effecten['leefbaarheid']); ?>"
             aria-valuemin="0"
             aria-valuemax="<?php echo e($effecten['max']); ?>">
            <div class="effect-bar-fill leefbaarheid-fill"
                 style="width: <?php echo e(min(100, ($effecten['leefbaarheid'] / $effecten['max']) * 100)); ?>%"></div>
        </div>
    </div>

    <div class="effect-item">
        <div class="effect-header">
            <span class="effect-label">❤️ Burgerwelzijn</span>
            <span class="effect-value" aria-label="Burgerwelzijn: <?php echo e($effecten['welzijn']); ?> van <?php echo e($effecten['max']); ?>">
                <?php echo e($effecten['welzijn']); ?>

            </span>
        </div>
        <div class="effect-bar" role="progressbar"
             aria-valuenow="<?php echo e($effecten['welzijn']); ?>"
             aria-valuemin="0"
             aria-valuemax="<?php echo e($effecten['max']); ?>">
            <div class="effect-bar-fill welzijn-fill"
                 style="width: <?php echo e(min(100, ($effecten['welzijn'] / $effecten['max']) * 100)); ?>%"></div>
        </div>
    </div>

    <p class="effect-info">
        Parken en voorzieningen verhogen leefbaarheid.<br>
        Hulpdiensten en voorzieningen verhogen welzijn.
    </p>
</div>
<?php /**PATH C:\Users\Hp\Downloads\metropolis\metropolis\resources\views/partials/effecten.blade.php ENDPATH**/ ?>