<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid tm-mt-60">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h2 class="tm-text-primary mb-4"><?= esc(lang('Site.register')) ?></h2>
            <form action="<?= site_url('register') ?>" method="POST" class="tm-contact-form">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="register-name"><?= esc(lang('Site.username')) ?></label>
                    <input type="text" id="register-name" name="name" class="form-control rounded-0" autocomplete="username" minlength="2" maxlength="100" pattern="[^@]+" required>
                    <small class="form-text text-muted"><?= esc(lang('Site.usernameHint')) ?></small>
                </div>
                <div class="form-group">
                    <label for="register-email"><?= esc(lang('Site.email')) ?></label>
                    <input type="email" id="register-email" name="email" class="form-control rounded-0" autocomplete="email" maxlength="254" required>
                </div>
                <div class="form-group">
                    <label for="register-password"><?= esc(lang('Site.password')) ?></label>
                    <input type="password" id="register-password" name="password" class="form-control rounded-0" autocomplete="new-password" minlength="8" required>
                    <small class="form-text text-muted"><?= esc(lang('Site.passwordHint')) ?></small>
                </div>
                <button type="submit" class="btn btn-primary"><?= esc(lang('Site.registerAction')) ?></button>
                <a href="<?= site_url('login') ?>" class="btn btn-link"><?= esc(lang('Site.alreadyRegistered')) ?></a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
