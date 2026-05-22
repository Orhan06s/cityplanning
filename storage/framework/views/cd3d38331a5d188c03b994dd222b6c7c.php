<?php
    $catLabels = [
        'veiligheid'      => __('veiligheid'),
        'recreatie'       => __('recreatie'),
        'milieukwaliteit' => __('milieukwaliteit'),
        'voorzieningen'   => __('voorzieningen'),
        'mobiliteit'      => __('mobiliteit'),
    ];
?>

<div class="totalen-paneel" role="region" aria-label="<?php echo e(__('Effectenmatrix')); ?>">
    <div class="totalen-titel"><?php echo e(__('Effecten per stadsfunctie en categorie')); ?></div>
    <div class="totalen-grid">
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $score   = $effecten[$cat] ?? 0;
                $klasse  = $score > 0 ? 'totaal-positief' : ($score < 0 ? 'totaal-negatief' : 'totaal-neutraal');
                $teken   = $score > 0 ? "+{$score}" : (string) $score;
                $label   = $catLabels[$cat] ?? $cat;
            ?>
            <div class="totaal-kaart <?php echo e($klasse); ?>">
                <span class="totaal-label"><?php echo e($label); ?></span>
                <span class="totaal-score"><?php echo e($teken); ?></span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php /**PATH C:\Users\Hp\Downloads\metropolis\metropolis\resources\views/partials/totalen.blade.php ENDPATH**/ ?>