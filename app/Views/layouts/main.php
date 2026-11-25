<?php
$active = $active ?? '';
$signedIn = session()->has('user_id');
$navigation = ['photos' => ['/', lang('Site.monsters')], 'about' => ['about', lang('Site.about')]];
if ($signedIn) {
    $navigation['profile'] = ['profil', lang('Site.profileNav')];
    if ((new \App\Models\UserModel())->isAdministrator((int) session()->get('user_id'))) {
        $navigation['management'] = ['gestion/monstres', lang('Site.management')];
    }
} else {
    $navigation['login'] = ['login', lang('Site.login')];
    $navigation['register'] = ['register', lang('Site.register')];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? lang('Site.brand')) ?></title>
    <link rel="stylesheet" href="<?= base_url('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/templatemo-style.css?v=4') ?>">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand rgb-logo" href="<?= site_url('/') ?>">
                <i class="fas fa-dragon mr-2" aria-hidden="true"></i>
                <?= esc(lang('Site.brand')) ?>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="<?= esc(lang('Site.menu'), 'attr') ?>">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto mb-2 mb-lg-0">
                    <?php foreach ($navigation as $key => [$route, $label]): ?>
                        <li class="nav-item">
                            <a class="nav-link nav-link-1<?= $active === $key ? ' active' : '' ?>"<?= $active === $key ? ' aria-current="page"' : '' ?> href="<?= site_url($route) ?>"><?= esc($label) ?></a>
                        </li>
                    <?php endforeach ?>
                    <li class="nav-item">
                        <form action="" method="post">
                            <button type="submit" class="btn btn-link nav-link">English</button>
                        </form>
                    </li>
                    <?php if ($signedIn): ?>
                        <li class="nav-item">
                            <form action="<?= site_url('logout') ?>" method="post">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-link nav-link"><?= esc(lang('Site.logout')) ?></button>
                            </form>
                        </li>
                    <?php endif ?>
                </ul>
            </div>
        </div>
    </nav>

    <?php if ($message = session()->getFlashdata('error')): ?>
        <div class="container mt-3"><div class="alert alert-danger" role="alert"><?= esc($message) ?></div></div>
    <?php endif ?>
    <?php if ($message = session()->getFlashdata('success')): ?>
        <div class="container mt-3"><div class="alert alert-success" role="status"><?= esc($message) ?></div></div>
    <?php endif ?>

    <?= $this->renderSection('content') ?>

    <footer class="tm-bg-gray py-4 tm-text-gray">
        <div class="container d-flex flex-wrap justify-content-between">
            <span>© <?= date('Y') ?> <?= esc(lang('Site.brand')) ?></span>
            <span><?= esc(lang('Site.template')) ?> <a href="https://templatemo.com" class="tm-text-gray">TemplateMo</a></span>
        </div>
    </footer>

    <script src="<?= base_url('js/plugins.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
