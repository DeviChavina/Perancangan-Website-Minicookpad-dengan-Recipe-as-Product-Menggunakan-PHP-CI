<?= $this->include('layout/header') ?>

<div class="mc-container" style="margin-top:1.5rem;margin-bottom:2rem">
    <div class="mc-auth-card">
        <!-- Auth Header -->
        <div class="mc-auth-header">
            <div class="mc-logo-icon" style="width:48px;height:48px;font-size:1.5rem;margin:0 auto 1rem">🍳</div>
            <h1 style="font-size:1.375rem;font-weight:700;margin-bottom:0.25rem">Masuk ke Mini Cookpad</h1>
            <p style="opacity:0.9;font-size:0.875rem">Temukan dan bagikan resep terbaik</p>
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
                    ❌
                    <div>
                        <?php foreach ($errors as $error): ?>
                            <div><?= esc($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form action="/login" method="post">
                <?= csrf_field() ?>

                <div class="mc-form-group">
                    <label for="email">Email</label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="mc-input"
                           placeholder="nama@email.com"
                           value="<?= old('email') ?>"
                           required>
                </div>

                <div class="mc-form-group">
                    <label for="password">Password</label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="mc-input"
                           placeholder="Minimal 6 karakter"
                           required>
                </div>

                <button type="submit" class="mc-btn mc-btn-primary mc-btn-block" style="margin-top:0.5rem">
                    🔑 Masuk
                </button>
            </form>

            <div style="text-align:center;margin-top:1.25rem;font-size:0.875rem;color:var(--mc-gray)">
                Belum punya akun?
                <a href="/register" style="font-weight:600">Daftar sekarang</a>
            </div>
        </div>
    </div>

    <!-- Demo Account Info -->
    <div style="max-width:28rem;margin:1.5rem auto 0;background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.25rem">
        <h3 style="font-size:0.9375rem;font-weight:700;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.375rem">
            🧪 Akun Demo
        </h3>

        <div style="display:grid;gap:0.75rem;font-size:0.8125rem">

            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem 0.75rem;background:var(--mc-muted);border-radius:8px">
                <div>
                    <strong style="color:var(--mc-dark)">User Free</strong>
                    <span style="color:var(--mc-gray)"> — user.andi@cookpad.com / user123</span>
                </div>
                <span class="mc-badge mc-badge-premium">Free</span>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem 0.75rem;background:var(--mc-muted);border-radius:8px">
                <div>
                    <strong style="color:var(--mc-dark)">User Free</strong>
                    <span style="color:var(--mc-gray)"> — user.sari@cookpad.com / user123</span>
                </div>
                <span class="mc-badge mc-badge-premium">Free</span>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem 0.75rem;background:var(--mc-muted);border-radius:8px">
                <div>
                    <strong style="color:var(--mc-dark)">Chef Verified</strong>
                    <span style="color:var(--mc-gray)"> — chef.rina@cookpad.com / chef123</span>
                </div>
                <span class="mc-badge mc-badge-chef">Chef</span>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem 0.75rem;background:var(--mc-muted);border-radius:8px">
                <div>
                    <strong style="color:var(--mc-dark)">Chef Verified</strong>
                    <span style="color:var(--mc-gray)"> — chef.takeshi@cookpad.com / chef123</span>
                </div>
                <span class="mc-badge mc-badge-chef">Chef</span>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem 0.75rem;background:var(--mc-muted);border-radius:8px">
                <div>
                    <strong style="color:var(--mc-dark)">Chef Unverified</strong>
                    <span style="color:var(--mc-gray)"> — chef.marco@cookpad.com / chef123</span>
                </div>
                <span class="mc-badge mc-badge-chef">Chef</span>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem 0.75rem;background:var(--mc-muted);border-radius:8px">
                <div>
                    <strong style="color:var(--mc-dark)">Admin</strong>
                    <span style="color:var(--mc-gray)"> — admin@cookpad.com / admin123</span>
                </div>
                <span class="mc-badge mc-badge-admin">Admin</span>
            </div>

        </div>
    </div>
</div>

<?= $this->include('layout/footer') ?>