<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<main class="container py-5">
    <h1 class="tm-text-primary mb-4"><?= esc(lang('Site.aboutHeading')) ?></h1>
    <p><?= esc(lang('Site.aboutIntro')) ?></p>
    <a class="btn btn-primary" href="<?= site_url('/') ?>"><?= esc(lang('Site.browse')) ?></a>
</main>
<?= $this->endSection() ?>
