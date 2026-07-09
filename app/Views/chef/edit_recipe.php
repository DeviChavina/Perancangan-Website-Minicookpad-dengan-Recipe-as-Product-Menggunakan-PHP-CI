<?= $this->include('layout/header') ?>

<?php
 $recipe = $recipe ?? null;
if (!$recipe) {
    echo '<div class="mc-container" style="margin-top:1.5rem"><div class="mc-alert mc-alert-error">❌ Resep tidak ditemukan</div></div>';
    echo $this->include('layout/footer');
    return;
}

// Pakai old() kalau validasi gagal, kalau tidak pakai data dari DB
 $ingredients = old('ingredients') ?: ($recipe['ingredients'] ?? []);
 $steps       = old('steps')       ?: ($recipe['steps']       ?? []);

 $imgUrl   = recipe_image_url($recipe['image'] ?? null);
 $hasPhoto = !empty($recipe['image']);

 $cuisineOpts = ['Indonesian', 'Japanese', 'Italian', 'Korean', 'Thai', 'Mexican'];
 $catOpts = [
    'appetizer'   => '🥗 Appetizer',
    'main_course' => '🍽️ Main Course',
    'dessert'     => '🍰 Dessert',
    'snack'       => '🍿 Snack',
    'drink'       => '🥤 Minuman',
    'soup'        => '🍲 Sup',
    'other'       => '🍽️ Lainnya',
];
 $diffOpts = ['easy' => 'Mudah', 'medium' => 'Sedang', 'hard' => 'Sulit'];

 $selCuisine = old('cuisine', $recipe['cuisine']);
 $selCat     = old('category', $recipe['category']);
 $selDiff    = old('difficulty', $recipe['difficulty']);
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
            <a href="/chef/dashboard" class="mc-btn mc-btn-outline mc-btn-sm" style="margin-bottom:1rem">← Kembali</a>
            <h1 style="font-size:1.5rem;font-weight:700">✏️ Edit Resep</h1>
            <p style="color:var(--mc-gray);font-size:0.875rem">Perbarui resep "<strong><?= esc($recipe['title']) ?></strong>"</p>
        </div>

        <form action="/chef/recipe/<?= $recipe['id'] ?>/update" method="post" enctype="multipart/form-data" id="mcRecipeForm">
            <?= csrf_field() ?>

            <!-- Basic Information -->
            <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.5rem">
                <h2 style="font-size:1.125rem;font-weight:700;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem">📋 Informasi Dasar</h2>

                <div class="mc-form-group">
                    <label for="title">Judul Resep <span style="color:var(--mc-red)">*</span></label>
                    <input type="text" id="title" name="title" class="mc-input"
                           value="<?= esc(old('title', $recipe['title'])) ?>" required>
                </div>

                <div class="mc-form-group">
                    <label for="description">Deskripsi <span style="color:var(--mc-red)">*</span></label>
                    <textarea id="description" name="description" class="mc-textarea" rows="3" required><?= esc(old('description', $recipe['description'])) ?></textarea>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="mc-form-group">
                        <label for="cuisine">Masakan <span style="color:var(--mc-red)">*</span></label>
                        <select id="cuisine" name="cuisine" class="mc-select" required>
                            <option value="">Pilih masakan</option>
                            <?php foreach ($cuisineOpts as $c): ?>
                            <option value="<?= $c ?>" <?= $selCuisine === $c ? 'selected' : '' ?>><?= cuisine_label($c) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mc-form-group">
                        <label for="category">Kategori <span style="color:var(--mc-red)">*</span></label>
                        <select id="category" name="category" class="mc-select" required>
                            <option value="">Pilih kategori</option>
                            <?php foreach ($catOpts as $k => $v): ?>
                            <option value="<?= $k ?>" <?= $selCat === $k ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">
                    <div class="mc-form-group">
                        <label for="difficulty">Tingkat Kesulitan <span style="color:var(--mc-red)">*</span></label>
                        <select id="difficulty" name="difficulty" class="mc-select" required>
                            <option value="">Pilih level</option>
                            <?php foreach ($diffOpts as $k => $v): ?>
                            <option value="<?= $k ?>" <?= $selDiff === $k ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mc-form-group">
                        <label for="cooking_time">Waktu Memasak (menit) <span style="color:var(--mc-red)">*</span></label>
                        <input type="number" id="cooking_time" name="cooking_time" class="mc-input"
                               min="1" value="<?= esc(old('cooking_time', $recipe['cooking_time'])) ?>" required>
                    </div>
                    <div class="mc-form-group">
                        <label for="servings">Porsi <span style="color:var(--mc-red)">*</span></label>
                        <input type="number" id="servings" name="servings" class="mc-input"
                               min="1" value="<?= esc(old('servings', $recipe['servings'])) ?>" required>
                    </div>
                </div>

                <!-- Foto Resep -->
                <div class="mc-form-group">
                    <label>Foto Resep</label>
                    <?php if ($hasPhoto): ?>
                    <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:0.75rem;padding:0.75rem;background:var(--mc-muted);border-radius:8px">
                        <img src="<?= $imgUrl ?>" alt="Foto resep saat ini"
                             style="width:5rem;height:5rem;object-fit:cover;border-radius:8px;border:1px solid var(--mc-border)">
                        <div>
                            <p style="font-size:0.8125rem;font-weight:600;color:var(--mc-dark)">Foto saat ini</p>
                            <label style="display:flex;align-items:center;gap:0.375rem;font-size:0.8125rem;color:var(--mc-red);cursor:pointer;margin-top:0.25rem">
                                <input type="checkbox" name="remove_image" value="1"
                                       style="width:1rem;height:1rem;accent-color:var(--mc-red)">
                                🗑️ Hapus foto ini
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="mc-upload-area" onclick="document.getElementById('recipe_image').click()">
                        <div style="font-size:1.5rem;margin-bottom:0.25rem">📷</div>
                        <p style="font-size:0.8125rem;font-weight:600;color:var(--mc-dark)">
                            <?= $hasPhoto ? 'Ganti foto resep (opsional)' : 'Klik untuk upload foto resep' ?>
                        </p>
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
                           <?= !empty(old('is_premium', $recipe['is_premium'])) ? 'checked' : '' ?>>
                    <label for="is_premium" style="cursor:pointer;font-size:0.875rem;font-weight:600;display:flex;align-items:center;gap:0.375rem">
                        ⭐ Resep Premium
                    </label>
                    <span style="font-size:0.75rem;color:var(--mc-gray)">User perlu beli dengan koin untuk unlock</span>
                </div>
                <div id="coin_price_section" style="margin-top:.75rem;padding:.75rem 1rem;background:#fef3c7;border-radius:8px;display:<?= !empty($recipe['is_premium']) ? 'block' : 'none' ?>">
                    <label class="mc-label" style="margin-bottom:.5rem;display:block">
                        🪙 Harga Koin
                        <span style="font-size:0.75rem;font-weight:400;color:#92400e">(5–50 koin)</span>
                    </label>
                    <div style="display:flex;align-items:center;gap:.5rem">
                        <input type="number" name="coin_price" class="mc-input"
                               value="<?= (int)($recipe['coin_price'] ?? 10) ?>" min="5" max="50"
                               style="max-width:100px" onchange="updateEarning(this.value)">
                        <span style="color:#92400e;font-size:0.875rem">🪙</span>
                        <span id="earning_preview" style="color:#10b981;font-size:0.875rem;font-weight:600"></span>
                    </div>
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
            updateEarning(document.querySelector('input[name="coin_price"]').value);
            </script>

            <!-- Ingredients Section -->
            <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.5rem">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
                    <h2 style="font-size:1.125rem;font-weight:700;display:flex;align-items:center;gap:0.5rem">🥕 Bahan-Bahan</h2>
                    <button type="button" class="mc-btn mc-btn-outline mc-btn-sm" onclick="mcAddIngredient()">+ Tambah Bahan</button>
                </div>
                <div id="mcIngredientsList">
                    <?php if (!empty($ingredients)): ?>
                        <?php foreach ($ingredients as $idx => $ing): ?>
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
                            <input type="text" name="ingredients[0][name]" class="mc-input" placeholder="Nama bahan" style="flex:2">
                            <input type="text" name="ingredients[0][amount]" class="mc-input" placeholder="Jumlah" style="flex:1">
                            <input type="text" name="ingredients[0][unit]" class="mc-input" placeholder="Satuan" style="flex:1">
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
                    <?php if (!empty($steps)): ?>
                        <?php foreach ($steps as $idx => $st): ?>
                            <?php $stepImgUrl = step_image_url($st['image'] ?? null); ?>
                            <div class="mc-step-row" style="display:flex;gap:0.75rem;align-items:flex-start;margin-bottom:0.75rem;padding:1rem;background:var(--mc-muted);border-radius:8px">
                                <div style="width:2rem;height:2rem;background:var(--mc-green);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8125rem;flex-shrink:0" class="mc-step-number"><?= $idx + 1 ?></div>
                                <div style="flex:1">
                                    <textarea name="steps[<?= $idx ?>][description]" class="mc-textarea"
                                              placeholder="Jelaskan langkah ini..." rows="2" style="min-height:60px"><?= esc($st['description'] ?? '') ?></textarea>
                                    <input type="text" name="steps[<?= $idx ?>][tip]" class="mc-input"
                                           placeholder="💡 Tips (opsional)" value="<?= esc($st['tip'] ?? '') ?>"
                                           style="margin-top:0.5rem">
                                    <?php if ($stepImgUrl): ?>
                                    <div style="display:flex;align-items:center;gap:0.5rem;margin-top:0.5rem;padding:0.5rem;background:white;border-radius:6px;border:1px solid var(--mc-border)">
                                        <img src="<?= $stepImgUrl ?>" alt="Foto langkah" style="width:3rem;height:3rem;object-fit:cover;border-radius:4px">
                                        <div style="font-size:0.75rem;color:var(--mc-gray)">
                                            <p style="font-weight:600;color:var(--mc-dark)">Foto langkah saat ini</p>
                                            <p>Upload foto baru untuk mengganti (kosongkan untuk tetap):</p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <label style="display:flex;align-items:center;gap:0.5rem;margin-top:0.5rem;cursor:pointer;font-size:0.8125rem;color:var(--mc-gray)">
                                        📷 <span>Foto langkah <?= $stepImgUrl ? '(ganti)' : '(opsional)' ?>:</span>
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
                                <textarea name="steps[0][description]" class="mc-textarea" placeholder="Jelaskan langkah ini..." rows="2" style="min-height:60px"></textarea>
                                <input type="text" name="steps[0][tip]" class="mc-input" placeholder="💡 Tips (opsional)" style="margin-top:0.5rem">
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
                <button type="submit" name="status" value="draft" class="mc-btn mc-btn-outline">💾 Simpan sebagai Draft</button>
                <button type="submit" name="status" value="published" class="mc-btn mc-btn-primary">📤 Simpan &amp; Publikasikan</button>
            </div>

        </form>
    </div>
</div>

<script>
let mcIngredientCount = <?= max(count($ingredients), 1) ?>;

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

let mcStepCount = <?= max(count($steps), 1) ?>;

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