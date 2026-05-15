<?php
    $effectenMatrix = [
        'Politiebureau'    => ['veiligheid' =>  5, 'recreatie' =>  1, 'milieukwaliteit' =>  0, 'voorzieningen' =>  1, 'mobiliteit' =>  2],
        'Brandweerkazerne' => ['veiligheid' =>  4, 'recreatie' =>  1, 'milieukwaliteit' =>  2, 'voorzieningen' =>  1, 'mobiliteit' =>  2],
        'Park'             => ['veiligheid' => -2, 'recreatie' =>  5, 'milieukwaliteit' =>  4, 'voorzieningen' =>  0, 'mobiliteit' =>  0],
        'Bioscoop'         => ['veiligheid' => -1, 'recreatie' =>  4, 'milieukwaliteit' =>  0, 'voorzieningen' =>  2, 'mobiliteit' =>  0],
        'Sportpark'        => ['veiligheid' =>  0, 'recreatie' =>  5, 'milieukwaliteit' =>  2, 'voorzieningen' =>  3, 'mobiliteit' =>  0],
        'Waterzuivering'   => ['veiligheid' =>  0, 'recreatie' =>  0, 'milieukwaliteit' =>  5, 'voorzieningen' =>  2, 'mobiliteit' =>  6],
        'School'           => ['veiligheid' =>  2, 'recreatie' =>  2, 'milieukwaliteit' =>  0, 'voorzieningen' =>  5, 'mobiliteit' => -3],
        'Winkel'           => ['veiligheid' =>  0, 'recreatie' =>  0, 'milieukwaliteit' => -2, 'voorzieningen' =>  5, 'mobiliteit' =>  0],
        'Ziekenhuis'       => ['veiligheid' =>  3, 'recreatie' =>  0, 'milieukwaliteit' =>  0, 'voorzieningen' =>  5, 'mobiliteit' =>  0],
        'Station'          => ['veiligheid' => -2, 'recreatie' =>  2, 'milieukwaliteit' => -4, 'voorzieningen' =>  4, 'mobiliteit' =>  5],
        'Weg'              => ['veiligheid' => -4, 'recreatie' =>  2, 'milieukwaliteit' => -4, 'voorzieningen' =>  3, 'mobiliteit' =>  5],
        'Fietspad'         => ['veiligheid' =>  0, 'recreatie' =>  3, 'milieukwaliteit' =>  3, 'voorzieningen' =>  3, 'mobiliteit' =>  4],
        'Tankstation'      => ['veiligheid' => -2, 'recreatie' =>  0, 'milieukwaliteit' => -4, 'voorzieningen' =>  1, 'mobiliteit' =>  4],
    ];

    $categorieen = [
        'veiligheid'      => __('veiligheid'),
        'recreatie'       => __('recreatie'),
        'milieukwaliteit' => __('milieukwaliteit'),
        'voorzieningen'   => __('voorzieningen'),
        'mobiliteit'      => __('mobiliteit'),
    ];
?>

<div id="effecten-trigger-wrapper">
    <button
        id="effecten-trigger"
        class="effecten-trigger-btn"
        aria-expanded="false"
        aria-controls="effecten-panel"
        aria-label="<?php echo e(__('Toon effectenmatrix')); ?>"
        type="button"
    >
        <span class="trigger-icon" aria-hidden="true">&#128202;</span>
        <span class="trigger-label"><?php echo e(__('Effectenmatrix')); ?></span>
        <span class="trigger-chevron" aria-hidden="true">&#9662;</span>
    </button>
</div>

<div
    id="effecten-panel"
    class="effecten-panel"
    role="region"
    aria-labelledby="effecten-titel"
    aria-hidden="true"
    hidden
>
    <div class="effecten-panel-inner">
        <div class="effecten-panel-header">
            <h2 id="effecten-titel" class="effecten-titel"><?php echo e(__('Effectenmatrix')); ?></h2>
            <button
                id="effecten-sluit"
                class="effecten-sluit-btn"
                aria-label="<?php echo e(__('Sluit effectenmatrix')); ?>"
                type="button"
            >&times;</button>
        </div>

        <p class="effecten-subtitel">
            <?php echo e(__('Impact van elke stadsfunctie op de vijf leefbaarheidscategorieen.')); ?>

        </p>

        <div class="effecten-legenda" role="note" aria-label="<?php echo e(__('Kleurlegenda')); ?>">
            <span class="legenda-item">
                <span class="legenda-kleur effect-positief" aria-hidden="true"></span>
                <?php echo e(__('Positief')); ?>

            </span>
            <span class="legenda-item">
                <span class="legenda-kleur effect-neutraal" aria-hidden="true"></span>
                <?php echo e(__('Neutraal')); ?>

            </span>
            <span class="legenda-item">
                <span class="legenda-kleur effect-negatief" aria-hidden="true"></span>
                <?php echo e(__('Negatief')); ?>

            </span>
        </div>

        <div class="effecten-tabel-wrapper" tabindex="0">
            <table
                class="effecten-tabel"
                role="table"
                aria-label="<?php echo e(__('Effecten per stadsfunctie en categorie')); ?>"
            >
                <thead>
                    <tr role="row">
                        <th scope="col" class="th-functie"><?php echo e(__('Functie')); ?></th>
                        <?php $__currentLoopData = $categorieen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sleutel => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th scope="col" class="th-categorie th-<?php echo e($sleutel); ?>"><?php echo e($label); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $effectenMatrix; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $functie => $scores): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr role="row" class="effecten-rij" style="animation-delay: <?php echo e($loop->index * 35); ?>ms">
                            <th scope="row" class="td-functienaam"><?php echo e(__($functie)); ?></th>
                            <?php $__currentLoopData = $categorieen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sleutel => $catLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $score = $scores[$sleutel] ?? 0;
                                    $klasse = $score > 0 ? 'effect-positief' : ($score < 0 ? 'effect-negatief' : 'effect-neutraal');
                                    $label = $score > 0 ? "+{$score}" : (string) $score;
                                ?>
                                <td
                                    role="cell"
                                    class="td-score <?php echo e($klasse); ?>"
                                    aria-label="<?php echo e($catLabel); ?>: <?php echo e($label); ?>"
                                    tabindex="0"
                                >
                                    <span aria-hidden="true"><?php echo e($label); ?></span>
                                    <span class="sr-only"><?php echo e($catLabel); ?>: <?php echo e($label); ?></span>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Hp\Downloads\metropolis\metropolis\resources\views/partials/effects.blade.php ENDPATH**/ ?>