<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metropolis</title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
</head>
<body>

    <header>
        <div class="header-inner">
            <div>
                <h1> Metropolis</h1>
            </div>
            <div class="taal-knop">
                <?php if(app()->getLocale() === 'nl'): ?>
                    <a href="<?php echo e(route('taal', 'en')); ?>" class="taal-btn"> English</a>
                <?php else: ?>
                    <a href="<?php echo e(route('taal', 'nl')); ?>" class="taal-btn"> Nederlands</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <?php if(session('success')): ?>
        <div class="melding"><?php echo e(session('success')); ?></div>
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