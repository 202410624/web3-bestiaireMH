<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid tm-mt-60">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h2 class="tm-text-primary mb-4"><?= esc(lang('Site.login')) ?></h2>
            <form action="<?= site_url('login') ?>" method="POST" class="tm-contact-form">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="login-identifier"><?= esc(lang('Site.loginIdentifier')) ?></label>
                    <input type="text" id="login-identifier" name="identifier" class="form-control rounded-0" autocomplete="username" maxlength="254" required>
                </div>
                <div class="form-group">
                    <label for="login-password"><?= esc(lang('Site.password')) ?></label>
                    <input type="password" id="login-password" name="password" class="form-control rounded-0" autocomplete="current-password" required>
                </div>
                <button type="submit" class="btn btn-primary"><?= esc(lang('Site.loginAction')) ?></button>
                <a href="<?= site_url('register') ?>" class="btn btn-link"><?= esc(lang('Site.createAccount')) ?></a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
