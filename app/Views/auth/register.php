<?= $this->include('layout/header') ?>

<div class="mc-container" style="margin-top:1.5rem;margin-bottom:2rem">
    <div class="mc-auth-card">
        <!-- Auth Header -->
        <div class="mc-auth-header">
            <div class="mc-logo-icon" style="width:48px;height:48px;font-size:1.5rem;margin:0 auto 1rem">🍳</div>
            <h1 style="font-size:1.375rem;font-weight:700;margin-bottom:0.25rem">Buat Akun Baru</h1>
            <p style="opacity:0.9;font-size:0.875rem">Mulai perjalanan memasak Anda</p>
        </div>

        <!-- Auth Body -->
        <div class="mc-auth-body">
            <!-- Error message from session flashdata -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="mc-alert mc-alert-error">
                    ❌ <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <!-- Validation errors -->
            <?php $errors = session()->getFlashdata('errors'); ?>
            <?php if ($errors): ?>
                <div class="mc-alert mc-alert-error">
                    ❌ <div>
                        <?php foreach ($errors as $error): ?>
                            <div><?= esc($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form action="/register" method="post">
                <?= csrf_field() ?>
                <div class="mc-form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="mc-input" placeholder="Masukkan nama lengkap" value="<?= old('name') ?>" required>
                </div>

                <div class="mc-form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="mc-input" placeholder="nama@email.com" value="<?= old('email') ?>" required>
                </div>

                <div class="mc-form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="mc-input" placeholder="Minimal 6 karakter" required>
                </div>

                <div class="mc-form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="mc-input" placeholder="Ulangi password" required>
                </div>

                <button type="submit" class="mc-btn mc-btn-primary mc-btn-block" style="margin-top:0.5rem">✨ Buat Akun</button>
            </form>

            <div style="text-align:center;margin-top:1.25rem;font-size:0.875rem;color:var(--mc-gray)">
                Sudah punya akun? <a href="/login" style="font-weight:600">Masuk di sini</a>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layout/footer') ?>
