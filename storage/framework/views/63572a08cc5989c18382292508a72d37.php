<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metropolis</title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/css/flag-icons.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>?v=<?php echo e(filemtime(public_path('css/style.css'))); ?>">
</head>
<body>

    <header>
        <div class="header-inner">
            <div class="header-merk">
                <h1>Metropolis</h1>
            </div>

            <nav class="header-nav" aria-label="<?php echo e(__('Hoofdnavigatie')); ?>">
                <a href="<?php echo e(route('home')); ?>"
                   class="nav-link <?php echo e(request()->routeIs('home') ? 'is-actief' : ''); ?>">
                    <?php echo e(__('Stadsgebied')); ?>

                </a>
                <a href="<?php echo e(route('effects.manager')); ?>"
                   class="nav-link <?php echo e(request()->routeIs('effects.manager') ? 'is-actief' : ''); ?>">
                    <?php echo e(__('Effecten beheren')); ?>

                </a>
            </nav>

            <div class="taal-knop">
                <a href="<?php echo e(route('taal', 'nl')); ?>" class="taal-vlag <?php echo e(app()->getLocale() === 'nl' ? 'actief' : ''); ?>" title="Nederlands" aria-label="Nederlands">
                    <span class="fi fi-nl"></span>
                </a>
                <a href="<?php echo e(route('taal', 'en')); ?>" class="taal-vlag <?php echo e(app()->getLocale() === 'en' ? 'actief' : ''); ?>" title="English" aria-label="English">
                    <span class="fi fi-gb"></span>
                </a>
            </div>
        </div>
    </header>

    <?php if(session('success')): ?>
        <div class="melding" role="status" aria-live="polite"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="melding melding-fout" role="alert" aria-live="assertive">
            <strong><?php echo e(__('Er ging iets mis. Probeer het opnieuw.')); ?></strong>
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($fout); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <footer>
        <p>Metropolis Simulator &copy; <?php echo e(date('Y')); ?></p>
    </footer>

    <script>
        window.metroI18n = {
            saved: <?php echo json_encode(__('Opgeslagen'), 15, 512) ?>,
            saving: <?php echo json_encode(__('Opslaan...'), 15, 512) ?>,
            saveError: <?php echo json_encode(__('Fout bij opslaan!'), 15, 512) ?>,
            addError: <?php echo json_encode(__('Fout bij toevoegen!'), 15, 512) ?>,
            deleteError: <?php echo json_encode(__('Fout bij verwijderen!'), 15, 512) ?>,
            nameRequired: <?php echo json_encode(__('Voer een naam in.'), 15, 512) ?>,
            nameExists: <?php echo json_encode(__('Deze functie bestaat al.'), 15, 512) ?>,
            nameTooLong: <?php echo json_encode(__('Naam is te lang (max 50 tekens).'), 15, 512) ?>,
            functionAdded: <?php echo json_encode(__('Functie toegevoegd!'), 15, 512) ?>,
            functionDeleted: <?php echo json_encode(__('Functie verwijderd!'), 15, 512) ?>,
            noResults: <?php echo json_encode(__('Geen functies gevonden.'), 15, 512) ?>,
            functionCount: <?php echo json_encode(__(':count functies'), 15, 512) ?>,
            delete: <?php echo json_encode(__('Verwijder'), 15, 512) ?>,
            resetDone: <?php echo json_encode(__('Effecten gereset naar standaardwaarden!'), 15, 512) ?>,
            resetError: <?php echo json_encode(__('Fout bij resetten.'), 15, 512) ?>,
            invalidNumber: <?php echo json_encode(__('Ongeldig getal'), 15, 512) ?>,
            rangeError: <?php echo json_encode(__('Waarde moet tussen -5 en 5 zijn'), 15, 512) ?>,
        };
    </script>
    <script src="<?php echo e(asset('js/metropolis.js')); ?>?v=<?php echo e(filemtime(public_path('js/metropolis.js'))); ?>"></script>
</body>
</html>
<?php /**PATH C:\Users\Hp\Downloads\metropolis\metropolis\resources\views/layouts/app.blade.php ENDPATH**/ ?>