<?= $this->include('layout/header') ?>

<?php
 $old = $old ?? [];
 $oldIngredients = $old['ingredients'] ?? [];
 $oldSteps = $old['steps'] ?? [];
?>

<!-- Flash messages -->
<?php if (session()->getFlashdata('error')): ?>
<div class="mc-container" style="margin-top:1rem">
    <div class="mc-alert mc-alert-error">❌ <?= esc(session()->getFlashdata('error')) ?></div>
</div>
<?php endif; ?>

<?php $errors = session()->getFlashdata('errors'); ?>
<?php if ($errors): ?>
<div class="mc-container" style="margin-top:1rem">
    <div class="mc-alert mc-alert-error">
        ❌ <div>
            <?php foreach ($errors as $err): ?>
                <div><?= esc($err) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="mc-container" style="margin-top:1.5rem;margin-bottom:2rem">
    <div style="max-width:48rem;margin:0 auto">

        <!-- Page Header -->
        <div style="margin-bottom:1.5rem">
            <h1 style="font-size:1.5rem;font-weight:700">📝 Buat Resep Baru</h1>
            <p style="color:var(--mc-gray);font-size:0.875rem">Bagikan resep terbaik Anda kepada pecinta masakan</p>
        </div>

        <form action="/chef/recipe/store" method="post" enctype="multipart/form-data" id="mcRecipeForm">
            <?= csrf_field() ?>

            <!-- Basic Information -->
            <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.5rem">
                <h2 style="font-size:1.125rem;font-weight:700;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem">📋 Informasi Dasar</h2>

                <div class="mc-form-group">
                    <label for="title">Judul Resep <span style="color:var(--mc-red)">*</span></label>
                    <input type="text" id="title" name="title" class="mc-input"
                           placeholder="Contoh: Nasi Goreng Spesial"
                           value="<?= esc($old['title'] ?? old('title')) ?>" required>
                </div>

                <div class="mc-form-group">
                    <label for="description">Deskripsi <span style="color:var(--mc-red)">*</span></label>
                    <textarea id="description" name="description" class="mc-textarea" rows="3"
                              placeholder="Ceritakan tentang resep ini..." required><?= esc($old['description'] ?? old('description')) ?></textarea>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="mc-form-group">
                        <label for="cuisine">Masakan <span style="color:var(--mc-red)">*</span></label>
                        <select id="cuisine" name="cuisine" class="mc-select" required>
                            <option value="">Pilih masakan</option>
                            <option value="Indonesian" <?= ($old['cuisine'] ?? old('cuisine')) === 'Indonesian' ? 'selected' : '' ?>>🇮🇩 Indonesia</option>
                            <option value="Japanese" <?= ($old['cuisine'] ?? old('cuisine')) === 'Japanese' ? 'selected' : '' ?>>🇯🇵 Jepang</option>
                            <option value="Italian" <?= ($old['cuisine'] ?? old('cuisine')) === 'Italian' ? 'selected' : '' ?>>🇮🇹 Italia</option>
                            <option value="Korean" <?= ($old['cuisine'] ?? old('cuisine')) === 'Korean' ? 'selected' : '' ?>>🇰🇷 Korea</option>
                            <option value="Thai" <?= ($old['cuisine'] ?? old('cuisine')) === 'Thai' ? 'selected' : '' ?>>🇹🇭 Thailand</option>
                            <option value="Mexican" <?= ($old['cuisine'] ?? old('cuisine')) === 'Mexican' ? 'selected' : '' ?>>🇲🇽 Meksiko</option>
                        </select>
                    </div>
                    <div class="mc-form-group">
                        <label for="category">Kategori <span style="color:var(--mc-red)">*</span></label>
                        <select id="category" name="category" class="mc-select" required>
                            <option value="">Pilih kategori</option>
                            <option value="appetizer" <?= ($old['category'] ?? old('category')) === 'appetizer' ? 'selected' : '' ?>>🥗 Appetizer</option>
                            <option value="main_course" <?= ($old['category'] ?? old('category')) === 'main_course' ? 'selected' : '' ?>>🍽️ Main Course</option>
                            <option value="dessert" <?= ($old['category'] ?? old('category')) === 'dessert' ? 'selected' : '' ?>>🍰 Dessert</option>
                            <option value="snack" <?= ($old['category'] ?? old('category')) === 'snack' ? 'selected' : '' ?>>🍿 Snack</option>
                            <option value="drink" <?= ($old['category'] ?? old('category')) === 'drink' ? 'selected' : '' ?>>🥤 Minuman</option>
                            <option value="soup" <?= ($old['category'] ?? old('category')) === 'soup' ? 'selected' : '' ?>>🍲 Sup</option>
                            <option value="other" <?= ($old['category'] ?? old('category')) === 'other' ? 'selected' : '' ?>>🍽️ Lainnya</option>
                        </select>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">
                    <div class="mc-form-group">
                        <label for="difficulty">Tingkat Kesulitan <span style="color:var(--mc-red)">*</span></label>
                        <select id="difficulty" name="difficulty" class="mc-select" required>
                            <option value="">Pilih level</option>
                            <option value="easy" <?= ($old['difficulty'] ?? old('difficulty')) === 'easy' ? 'selected' : '' ?>>Mudah</option>
                            <option value="medium" <?= ($old['difficulty'] ?? old('difficulty')) === 'medium' ? 'selected' : '' ?>>Sedang</option>
                            <option value="hard" <?= ($old['difficulty'] ?? old('difficulty')) === 'hard' ? 'selected' : '' ?>>Sulit</option>
                        </select>
                    </div>
                    <div class="mc-form-group">
                        <label for="cooking_time">Waktu Memasak (menit) <span style="color:var(--mc-red)">*</span></label>
                        <input type="number" id="cooking_time" name="cooking_time" class="mc-input"
                               placeholder="30" min="1"
                               value="<?= esc($old['cooking_time'] ?? old('cooking_time')) ?>" required>
                    </div>
                    <div class="mc-form-group">
                        <label for="servings">Porsi <span style="color:var(--mc-red)">*</span></label>
                        <input type="number" id="servings" name="servings" class="mc-input"
                               placeholder="2" min="1"
                               value="<?= esc($old['servings'] ?? old('servings')) ?>" required>
                    </div>
                </div>

                <div class="mc-form-group">
                    <label>Foto Resep</label>
                    <div class="mc-upload-area" onclick="document.getElementById('recipe_image').click()">
                        <div style="font-size:2rem;margin-bottom:0.5rem">📷</div>
                        <p style="font-size:0.875rem;font-weight:600;color:var(--mc-dark)">Klik untuk upload foto resep</p>
                        <p style="font-size:0.75rem;color:var(--mc-gray);margin-top:0.25rem">JPG, PNG max 2MB</p>
                        <input type="file" id="recipe_image" name="image" accept="image/*"
                               style="display:none" onchange="mcShowFileName(this, 'recipe_image_name')">
                        <p id="recipe_image_name" style="font-size:0.8125rem;color:var(--mc-green);margin-top:0.5rem"></p>
                    </div>
                </div>

                <!-- Premium Toggle -->
                <div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;background:var(--mc-muted);border-radius:8px;margin-top:0.5rem">
                    <input type="checkbox" id="is_premium" name="is_premium" value="1"
                           style="width:1.125rem;height:1.125rem;accent-color:var(--mc-gold);cursor:pointer"
                           <?= !empty($old['is_premium'] ?? old('is_premium')) ? 'checked' : '' ?>>
                    <label for="is_premium" style="cursor:pointer;font-size:0.875rem;font-weight:600;display:flex;align-items:center;gap:0.375rem">
                        ⭐ Resep Premium
                    </label>
                    <span style="font-size:0.75rem;color:var(--mc-gray)">User perlu beli dengan koin untuk unlock</span>
                </div>
                <!-- Coin Price (shown when premium is checked) -->
                <div id="coin_price_section" style="margin-top:.75rem;padding:.75rem 1rem;background:#fef3c7;border-radius:8px;display:none">
                    <label class="mc-label" style="margin-bottom:.5rem;display:block">
                        🪙 Harga Koin
                        <span style="font-size:0.75rem;font-weight:400;color:#92400e">(5–50 koin · Anda dapat <?= session()->get('user_role') === 'CHEF_VERIFIED' ? '70%' : '50%' ?>)</span>
                    </label>
                    <div style="display:flex;align-items:center;gap:.5rem">
                        <input type="number" name="coin_price" class="mc-input" value="10" min="5" max="50"
                               style="max-width:100px" onchange="updateEarning(this.value)">
                        <span style="color:#92400e;font-size:0.875rem">🪙</span>
                        <span id="earning_preview" style="color:#10b981;font-size:0.875rem;font-weight:600"></span>
                    </div>
                    <div style="font-size:0.75rem;color:#b45309;margin-top:.25rem">Contoh: 10 koin → Anda dapat <?= session()->get('user_role') === 'CHEF_VERIFIED' ? '7' : '5' ?> koin per unlock</div>
                </div>
            </div>
            <script>
            document.getElementById('is_premium').addEventListener('change', function() {
                document.getElementById('coin_price_section').style.display = this.checked ? 'block' : 'none';
                if (this.checked) updateEarning(document.querySelector('input[name="coin_price"]').value);
            });
            function updateEarning(val) {
                const pct = <?= session()->get('user_role') === 'CHEF_VERIFIED' ? 0.7 : 0.5 ?>;
                const earn = Math.floor(val * pct);
                document.getElementById('earning_preview').textContent = '→ Anda dapat ' + earn + ' 🪙 per unlock';
            }
            if (document.getElementById('is_premium').checked) {
                document.getElementById('coin_price_section').style.display = 'block';
                updateEarning(10);
            }
            </script>

            <!-- Ingredients Section -->
            <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.5rem">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
                    <h2 style="font-size:1.125rem;font-weight:700;display:flex;align-items:center;gap:0.5rem">🥕 Bahan-Bahan</h2>
                    <button type="button" class="mc-btn mc-btn-outline mc-btn-sm" onclick="mcAddIngredient()">+ Tambah Bahan</button>
                </div>

                <div id="mcIngredientsList">
                    <?php if (!empty($oldIngredients)): ?>
                        <?php foreach ($oldIngredients as $idx => $ing): ?>
                        <div class="mc-ingredient-row" style="display:flex;gap:0.5rem;align-items:flex-start;margin-bottom:0.5rem">
                            <input type="text" name="ingredients[<?= $idx ?>][name]" class="mc-input"
                                   placeholder="Nama bahan" value="<?= esc($ing['name'] ?? '') ?>" style="flex:2">
                            <input type="text" name="ingredients[<?= $idx ?>][amount]" class="mc-input"
                                   placeholder="Jumlah" value="<?= esc($ing['amount'] ?? '') ?>" style="flex:1">
                            <input type="text" name="ingredients[<?= $idx ?>][unit]" class="mc-input"
                                   placeholder="Satuan" value="<?= esc($ing['unit'] ?? '') ?>" style="flex:1">
                            <button type="button" class="mc-btn mc-btn-danger mc-btn-sm" onclick="mcRemoveRow(this)" style="flex-shrink:0">✕</button>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="mc-ingredient-row" style="display:flex;gap:0.5rem;align-items:flex-start;margin-bottom:0.5rem">
                            <input type="text" name="ingredients[0][name]" class="mc-input"
                                   placeholder="Nama bahan" style="flex:2">
                            <input type="text" name="ingredients[0][amount]" class="mc-input"
                                   placeholder="Jumlah" style="flex:1">
                            <input type="text" name="ingredients[0][unit]" class="mc-input"
                                   placeholder="Satuan" style="flex:1">
                            <button type="button" class="mc-btn mc-btn-danger mc-btn-sm" onclick="mcRemoveRow(this)" style="flex-shrink:0">✕</button>
                        </div>
                    <?php endif; ?>
                </div>
                <p style="font-size:0.75rem;color:var(--mc-gray);margin-top:0.5rem">Contoh: Bawang Merah · 5 · siung</p>
            </div>

            <!-- Steps Section -->
            <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.5rem">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
                    <h2 style="font-size:1.125rem;font-weight:700;display:flex;align-items:center;gap:0.5rem">👨‍🍳 Langkah Memasak</h2>
                    <button type="button" class="mc-btn mc-btn-outline mc-btn-sm" onclick="mcAddStep()">+ Tambah Langkah</button>
                </div>

                <div id="mcStepsList">
                    <?php if (!empty($oldSteps)): ?>
                        <?php foreach ($oldSteps as $idx => $st): ?>
                        <div class="mc-step-row" style="display:flex;gap:0.75rem;align-items:flex-start;margin-bottom:0.75rem;padding:1rem;background:var(--mc-muted);border-radius:8px">
                            <div style="width:2rem;height:2rem;background:var(--mc-green);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8125rem;flex-shrink:0" class="mc-step-number"><?= $idx + 1 ?></div>
                            <div style="flex:1">
                                <textarea name="steps[<?= $idx ?>][description]" class="mc-textarea"
                                          placeholder="Jelaskan langkah ini..." rows="2" style="min-height:60px"><?= esc($st['description'] ?? '') ?></textarea>
                                <input type="text" name="steps[<?= $idx ?>][tip]" class="mc-input"
                                       placeholder="💡 Tips (opsional)" value="<?= esc($st['tip'] ?? '') ?>"
                                       style="margin-top:0.5rem">
                                <label style="display:flex;align-items:center;gap:0.5rem;margin-top:0.5rem;cursor:pointer;font-size:0.8125rem;color:var(--mc-gray)">
                                    📷 <span>Foto langkah (opsional):</span>
                                    <input type="file" name="steps[<?= $idx ?>][image]" accept="image/*" style="font-size:0.75rem">
                                </label>
                            </div>
                            <button type="button" class="mc-btn mc-btn-danger mc-btn-sm" onclick="mcRemoveRow(this)" style="flex-shrink:0">✕</button>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="mc-step-row" style="display:flex;gap:0.75rem;align-items:flex-start;margin-bottom:0.75rem;padding:1rem;background:var(--mc-muted);border-radius:8px">
                            <div style="width:2rem;height:2rem;background:var(--mc-green);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8125rem;flex-shrink:0" class="mc-step-number">1</div>
                            <div style="flex:1">
                                <textarea name="steps[0][description]" class="mc-textarea"
                                          placeholder="Jelaskan langkah ini..." rows="2" style="min-height:60px"></textarea>
                                <input type="text" name="steps[0][tip]" class="mc-input"
                                       placeholder="💡 Tips (opsional)"
                                       style="margin-top:0.5rem">
                                <label style="display:flex;align-items:center;gap:0.5rem;margin-top:0.5rem;cursor:pointer;font-size:0.8125rem;color:var(--mc-gray)">
                                    📷 <span>Foto langkah (opsional):</span>
                                    <input type="file" name="steps[0][image]" accept="image/*" style="font-size:0.75rem">
                                </label>
                            </div>
                            <button type="button" class="mc-btn mc-btn-danger mc-btn-sm" onclick="mcRemoveRow(this)" style="flex-shrink:0">✕</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div style="display:flex;gap:0.75rem;justify-content:flex-end;flex-wrap:wrap">
                <a href="/chef/dashboard" class="mc-btn mc-btn-outline">Batal</a>
                <button type="submit" name="status" value="draft" class="mc-btn mc-btn-outline">💾 Simpan Draft</button>
                <button type="submit" name="status" value="published" class="mc-btn mc-btn-primary">📤 Publikasikan</button>
            </div>

        </form>
    </div>
</div>

<script>
let mcIngredientCount = <?= !empty($oldIngredients) ? count($oldIngredients) : 1 ?>;

function mcAddIngredient() {
    const container = document.getElementById('mcIngredientsList');
    const idx = mcIngredientCount++;
    const row = document.createElement('div');
    row.className = 'mc-ingredient-row';
    row.style.cssText = 'display:flex;gap:0.5rem;align-items:flex-start;margin-bottom:0.5rem';
    row.innerHTML = `
        <input type="text" name="ingredients[${idx}][name]" class="mc-input" placeholder="Nama bahan" style="flex:2">
        <input type="text" name="ingredients[${idx}][amount]" class="mc-input" placeholder="Jumlah" style="flex:1">
        <input type="text" name="ingredients[${idx}][unit]" class="mc-input" placeholder="Satuan" style="flex:1">
        <button type="button" class="mc-btn mc-btn-danger mc-btn-sm" onclick="mcRemoveRow(this)" style="flex-shrink:0">✕</button>
    `;
    container.appendChild(row);
    row.querySelector('input').focus();
}

let mcStepCount = <?= !empty($oldSteps) ? count($oldSteps) : 1 ?>;

function mcAddStep() {
    const container = document.getElementById('mcStepsList');
    const idx = mcStepCount++;
    const num = container.querySelectorAll('.mc-step-row').length + 1;
    const row = document.createElement('div');
    row.className = 'mc-step-row';
    row.style.cssText = 'display:flex;gap:0.75rem;align-items:flex-start;margin-bottom:0.75rem;padding:1rem;background:var(--mc-muted);border-radius:8px';
    row.innerHTML = `
        <div style="width:2rem;height:2rem;background:var(--mc-green);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8125rem;flex-shrink:0" class="mc-step-number">${num}</div>
        <div style="flex:1">
            <textarea name="steps[${idx}][description]" class="mc-textarea" placeholder="Jelaskan langkah ini..." rows="2" style="min-height:60px"></textarea>
            <input type="text" name="steps[${idx}][tip]" class="mc-input" placeholder="💡 Tips (opsional)" style="margin-top:0.5rem">
            <label style="display:flex;align-items:center;gap:0.5rem;margin-top:0.5rem;cursor:pointer;font-size:0.8125rem;color:var(--mc-gray)">
                📷 <span>Foto langkah (opsional):</span>
                <input type="file" name="steps[${idx}][image]" accept="image/*" style="font-size:0.75rem">
            </label>
        </div>
        <button type="button" class="mc-btn mc-btn-danger mc-btn-sm" onclick="mcRemoveRow(this)" style="flex-shrink:0">✕</button>
    `;
    container.appendChild(row);
    row.querySelector('textarea').focus();
}

function mcRemoveRow(btn) {
    const row = btn.closest('.mc-ingredient-row, .mc-step-row');
    if (row) {
        row.remove();
        mcReindexStepNumbers();
    }
}

function mcReindexStepNumbers() {
    document.querySelectorAll('#mcStepsList .mc-step-row').forEach((row, idx) => {
        const numEl = row.querySelector('.mc-step-number');
        if (numEl) numEl.textContent = idx + 1;
    });
}

function mcShowFileName(input, targetId) {
    const target = document.getElementById(targetId);
    if (input.files && input.files[0]) {
        target.textContent = '✓ ' + input.files[0].name;
    }
}
</script>

<?= $this->include('layout/footer') ?>