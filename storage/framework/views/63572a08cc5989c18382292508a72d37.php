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

    <script src="<?php echo e(asset('js/metropolis.js')); ?>?v=<?php echo e(filemtime(public_path('js/metropolis.js'))); ?>"></script>
</body>
</html>
<?php /**PATH C:\Users\Hp\Downloads\metropolis\metropolis\resources\views/layouts/app.blade.php ENDPATH**/ ?>