<?= $this->include('layout/header') ?>

<?php
$currentStep = $step ?? 1;
$error = $error ?? null;
$old = $old ?? [];
?>

<div class="mc-container" style="margin-top:1.5rem;margin-bottom:2rem">
    <div style="max-width:40rem;margin:0 auto">
        <!-- Page Title -->
        <div style="text-align:center;margin-bottom:1.5rem">
            <h1 style="font-size:1.5rem;font-weight:700;margin-bottom:0.375rem">👨‍🍳 Verifikasi Chef</h1>
            <p style="color:var(--mc-gray);font-size:0.875rem">Lengkapi data berikut untuk menjadi chef terverifikasi</p>
        </div>

        <!-- Wizard Steps Indicator -->
        <div class="mc-wizard-steps">
            <div class="mc-wizard-step <?= $currentStep >= 1 ? 'active' : '' ?> <?= $currentStep > 1 ? 'done' : '' ?>" data-step="1">1</div>
            <div class="mc-wizard-line <?= $currentStep > 1 ? 'done' : '' ?>"></div>
            <div class="mc-wizard-step <?= $currentStep >= 2 ? 'active' : '' ?> <?= $currentStep > 2 ? 'done' : '' ?>" data-step="2">2</div>
            <div class="mc-wizard-line <?= $currentStep > 2 ? 'done' : '' ?>"></div>
            <div class="mc-wizard-step <?= $currentStep >= 3 ? 'active' : '' ?> <?= $currentStep > 3 ? 'done' : '' ?>" data-step="3">3</div>
            <div class="mc-wizard-line <?= $currentStep > 3 ? 'done' : '' ?>"></div>
            <div class="mc-wizard-step <?= $currentStep >= 4 ? 'active' : '' ?>" data-step="4">4</div>
        </div>

        <!-- Step Labels -->
        <div style="display:flex;justify-content:space-between;margin-bottom:1.5rem;font-size:0.75rem;color:var(--mc-gray)">
            <span style="text-align:center;width:25%">Identitas<br>Diri</span>
            <span style="text-align:center;width:25%">Informasi<br>Profesional</span>
            <span style="text-align:center;width:25%">Portofolio &<br>Sertifikat</span>
            <span style="text-align:center;width:25%">Review &<br>Submit</span>
        </div>

        <!-- Error Alert -->
        <?php if ($error): ?>
            <div class="mc-alert mc-alert-error" style="margin-bottom:1rem">
                ❌ <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <?php $errors = session()->getFlashdata('errors'); ?>
        <?php if ($errors): ?>
            <div class="mc-alert mc-alert-error" style="margin-bottom:1rem">
                ❌ <div>
                    <?php foreach ($errors as $err): ?>
                        <div><?= esc($err) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <form action="/chef/verify" method="post" enctype="multipart/form-data" id="mcVerifyForm">
            <?= csrf_field() ?>

            <!-- Step 1: Identitas Diri -->
            <div class="mc-wizard-panel" id="mcStep1" style="<?= $currentStep === 1 ? '' : 'display:none' ?>">
                <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.5rem">
                    <h2 style="font-size:1.125rem;font-weight:700;margin-bottom:0.25rem">📋 Identitas Diri</h2>
                    <p style="font-size:0.8125rem;color:var(--mc-gray);margin-bottom:1.25rem">Masukkan informasi identitas Anda untuk verifikasi</p>

                    <div class="mc-form-group">
                        <label for="id_card_number">Nomor KTP <span style="color:var(--mc-red)">*</span></label>
                        <input type="text" id="id_card_number" name="id_card_number" class="mc-input"
                               placeholder="Masukkan 16 digit nomor KTP"
                               value="<?= esc($old['id_card_number'] ?? old('id_card_number')) ?>" required>
                    </div>

                    <div class="mc-form-group">
                        <label>Foto KTP <span style="color:var(--mc-red)">*</span></label>
                        <div class="mc-upload-area" onclick="document.getElementById('id_card_photo').click()">
                            <div style="font-size:2rem;margin-bottom:0.5rem">📷</div>
                            <p style="font-size:0.875rem;font-weight:600;color:var(--mc-dark)">Klik untuk upload foto KTP</p>
                            <p style="font-size:0.75rem;color:var(--mc-gray);margin-top:0.25rem">JPG, PNG max 2MB</p>
                            <input type="file" id="id_card_photo" name="id_card_photo" accept="image/*"
                                   style="display:none" onchange="mcShowFileName(this, 'id_card_photo_name')">
                            <p id="id_card_photo_name" style="font-size:0.8125rem;color:var(--mc-green);margin-top:0.5rem"></p>
                        </div>
                    </div>

                    <div style="display:flex;justify-content:flex-end;margin-top:1.5rem">
                        <button type="button" class="mc-btn mc-btn-primary" onclick="mcGoToStep(2)">
                            Selanjutnya →
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Informasi Profesional -->
            <div class="mc-wizard-panel" id="mcStep2" style="<?= $currentStep === 2 ? '' : 'display:none' ?>">
                <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.5rem">
                    <h2 style="font-size:1.125rem;font-weight:700;margin-bottom:0.25rem">💼 Informasi Profesional</h2>
                    <p style="font-size:0.8125rem;color:var(--mc-gray);margin-bottom:1.25rem">Ceritakan pengalaman memasak Anda</p>

                    <div class="mc-form-group">
                        <label for="specialization">Spesialisasi <span style="color:var(--mc-red)">*</span></label>
                        <select id="specialization" name="specialization" class="mc-select" required>
                            <option value="">Pilih spesialisasi</option>
                            <option value="Masakan Indonesia" <?= ($old['specialization'] ?? old('specialization')) === 'Masakan Indonesia' ? 'selected' : '' ?>>Masakan Indonesia</option>
                            <option value="Masakan Jepang" <?= ($old['specialization'] ?? old('specialization')) === 'Masakan Jepang' ? 'selected' : '' ?>>Masakan Jepang</option>
                            <option value="Masakan Italia" <?= ($old['specialization'] ?? old('specialization')) === 'Masakan Italia' ? 'selected' : '' ?>>Masakan Italia</option>
                            <option value="Masakan Korea" <?= ($old['specialization'] ?? old('specialization')) === 'Masakan Korea' ? 'selected' : '' ?>>Masakan Korea</option>
                            <option value="Masakan Thailand" <?= ($old['specialization'] ?? old('specialization')) === 'Masakan Thailand' ? 'selected' : '' ?>>Masakan Thailand</option>
                            <option value="Masakan Meksiko" <?= ($old['specialization'] ?? old('specialization')) === 'Masakan Meksiko' ? 'selected' : '' ?>>Masakan Meksiko</option>
                            <option value="Pastry & Dessert" <?= ($old['specialization'] ?? old('specialization')) === 'Pastry & Dessert' ? 'selected' : '' ?>>Pastry & Dessert</option>
                            <option value="Fusion" <?= ($old['specialization'] ?? old('specialization')) === 'Fusion' ? 'selected' : '' ?>>Fusion</option>
                            <option value="Lainnya" <?= ($old['specialization'] ?? old('specialization')) === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>

                    <div class="mc-form-group">
                        <label for="experience">Pengalaman <span style="color:var(--mc-red)">*</span></label>
                        <textarea id="experience" name="experience" class="mc-textarea" rows="5"
                                  placeholder="Ceritakan pengalaman memasak Anda, di mana Anda pernah bekerja, dll." required><?= esc($old['experience'] ?? old('experience')) ?></textarea>
                    </div>

                    <div style="display:flex;justify-content:space-between;margin-top:1.5rem">
                        <button type="button" class="mc-btn mc-btn-outline" onclick="mcGoToStep(1)">
                            ← Sebelumnya
                        </button>
                        <button type="button" class="mc-btn mc-btn-primary" onclick="mcGoToStep(3)">
                            Selanjutnya →
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Portofolio & Sertifikat -->
            <div class="mc-wizard-panel" id="mcStep3" style="<?= $currentStep === 3 ? '' : 'display:none' ?>">
                <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.5rem">
                    <h2 style="font-size:1.125rem;font-weight:700;margin-bottom:0.25rem">🏆 Portofolio & Sertifikat</h2>
                    <p style="font-size:0.8125rem;color:var(--mc-gray);margin-bottom:1.25rem">Upload sertifikat dan link portofolio Anda (opsional)</p>

                    <div class="mc-form-group">
                        <label>Sertifikat / Ijazah</label>
                        <div class="mc-upload-area" onclick="document.getElementById('certificate_photo').click()">
                            <div style="font-size:2rem;margin-bottom:0.5rem">🎓</div>
                            <p style="font-size:0.875rem;font-weight:600;color:var(--mc-dark)">Klik untuk upload sertifikat</p>
                            <p style="font-size:0.75rem;color:var(--mc-gray);margin-top:0.25rem">JPG, PNG max 2MB (opsional)</p>
                            <input type="file" id="certificate_photo" name="certificate_photo" accept="image/*"
                                   style="display:none" onchange="mcShowFileName(this, 'certificate_photo_name')">
                            <p id="certificate_photo_name" style="font-size:0.8125rem;color:var(--mc-green);margin-top:0.5rem"></p>
                        </div>
                    </div>

                    <div class="mc-form-group">
                        <label for="portfolio_url">URL Portofolio</label>
                        <input type="url" id="portfolio_url" name="portfolio_url" class="mc-input"
                               placeholder="https://portofolio-anda.com"
                               value="<?= esc($old['portfolio_url'] ?? old('portfolio_url')) ?>">
                        <p style="font-size:0.75rem;color:var(--mc-gray);margin-top:0.25rem">Website, Instagram, atau link lain yang menunjukkan kemampuan Anda</p>
                    </div>

                    <div style="display:flex;justify-content:space-between;margin-top:1.5rem">
                        <button type="button" class="mc-btn mc-btn-outline" onclick="mcGoToStep(2)">
                            ← Sebelumnya
                        </button>
                        <button type="button" class="mc-btn mc-btn-primary" onclick="mcGoToStep(4)">
                            Selanjutnya →
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 4: Review & Submit -->
            <div class="mc-wizard-panel" id="mcStep4" style="<?= $currentStep === 4 ? '' : 'display:none' ?>">
                <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.5rem">
                    <h2 style="font-size:1.125rem;font-weight:700;margin-bottom:0.25rem">✅ Review & Submit</h2>
                    <p style="font-size:0.8125rem;color:var(--mc-gray);margin-bottom:1.25rem">Periksa kembali data Anda sebelum mengirim</p>

                    <!-- Review Summary -->
                    <div style="background:var(--mc-muted);border-radius:8px;padding:1.25rem;margin-bottom:1rem">
                        <h3 style="font-size:0.875rem;font-weight:700;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.375rem">📋 Identitas Diri</h3>
                        <div style="font-size:0.8125rem;display:grid;gap:0.5rem">
                            <div><strong>Nomor KTP:</strong> <span id="review_id_card">-</span></div>
                            <div><strong>Foto KTP:</strong> <span id="review_id_photo">Belum dipilih</span></div>
                        </div>
                    </div>

                    <div style="background:var(--mc-muted);border-radius:8px;padding:1.25rem;margin-bottom:1rem">
                        <h3 style="font-size:0.875rem;font-weight:700;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.375rem">💼 Informasi Profesional</h3>
                        <div style="font-size:0.8125rem;display:grid;gap:0.5rem">
                            <div><strong>Spesialisasi:</strong> <span id="review_specialization">-</span></div>
                            <div><strong>Pengalaman:</strong> <span id="review_experience">-</span></div>
                        </div>
                    </div>

                    <div style="background:var(--mc-muted);border-radius:8px;padding:1.25rem;margin-bottom:1.25rem">
                        <h3 style="font-size:0.875rem;font-weight:700;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.375rem">🏆 Portofolio & Sertifikat</h3>
                        <div style="font-size:0.8125rem;display:grid;gap:0.5rem">
                            <div><strong>Sertifikat:</strong> <span id="review_certificate">Belum dipilih</span></div>
                            <div><strong>Portofolio:</strong> <span id="review_portfolio">-</span></div>
                        </div>
                    </div>

                    <div class="mc-alert mc-alert-warning" style="margin-bottom:1rem">
                        ⚠️ Pastikan semua data yang Anda masukkan sudah benar. Data yang sudah dikirim tidak dapat diubah.
                    </div>

                    <div style="display:flex;justify-content:space-between;margin-top:1.5rem">
                        <button type="button" class="mc-btn mc-btn-outline" onclick="mcGoToStep(3)">
                            ← Sebelumnya
                        </button>
                        <button type="submit" class="mc-btn mc-btn-secondary">
                            📤 Kirim Permintaan Verifikasi
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
// Wizard Step Navigation
function mcGoToStep(step) {
    // Validate current step before moving forward
    if (step > mcCurrentStep) {
        if (!mcValidateStep(mcCurrentStep)) return;
    }

    // Hide all panels
    document.querySelectorAll('.mc-wizard-panel').forEach(p => p.style.display = 'none');

    // Show target panel
    document.getElementById('mcStep' + step).style.display = '';

    // Update step indicators
    document.querySelectorAll('.mc-wizard-step').forEach((el, idx) => {
        const s = idx + 1;
        el.classList.remove('active', 'done');
        if (s < step) el.classList.add('done');
        if (s === step) el.classList.add('active');
        if (s > step && s <= step) el.classList.add('active');
    });

    // Update wizard lines
    const lines = document.querySelectorAll('.mc-wizard-line');
    lines.forEach((line, idx) => {
        if (idx + 1 < step) {
            line.classList.add('done');
        } else {
            line.classList.remove('done');
        }
    });

    // Update review summary on step 4
    if (step === 4) mcUpdateReview();

    mcCurrentStep = step;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

let mcCurrentStep = <?= $currentStep ?>;

function mcValidateStep(step) {
    const panel = document.getElementById('mcStep' + step);
    const requiredFields = panel.querySelectorAll('[required]');
    for (const field of requiredFields) {
        if (!field.value.trim()) {
            field.focus();
            field.style.borderColor = 'var(--mc-red)';
            alert('Mohon lengkapi field yang wajib diisi');
            return false;
        }
        field.style.borderColor = '';
    }
    return true;
}

function mcShowFileName(input, targetId) {
    const target = document.getElementById(targetId);
    if (input.files && input.files[0]) {
        target.textContent = '✓ ' + input.files[0].name;
    }
}

function mcUpdateReview() {
    const idCard = document.getElementById('id_card_number');
    const spec = document.getElementById('specialization');
    const exp = document.getElementById('experience');
    const port = document.getElementById('portfolio_url');
    const idPhoto = document.getElementById('id_card_photo');
    const certPhoto = document.getElementById('certificate_photo');

    document.getElementById('review_id_card').textContent = idCard.value || '-';
    document.getElementById('review_specialization').textContent = spec.options[spec.selectedIndex]?.text || '-';
    document.getElementById('review_experience').textContent = exp.value ? (exp.value.length > 100 ? exp.value.substring(0, 100) + '...' : exp.value) : '-';
    document.getElementById('review_portfolio').textContent = port.value || '-';
    document.getElementById('review_id_photo').textContent = idPhoto.files?.[0]?.name || 'Belum dipilih';
    document.getElementById('review_certificate').textContent = certPhoto.files?.[0]?.name || 'Belum dipilih';
}
</script>

<?= $this->include('layout/footer') ?>
