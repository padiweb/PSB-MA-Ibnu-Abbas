<?php
// views/public/daftar.php
$prodiList  = $prodi_list ?? [];
$tahunAktif = $tahun_aktif ?? null;
?>

<!-- HERO DAFTAR -->
<section style="background:linear-gradient(135deg,var(--blue-dark) 0%,var(--blue-main) 50%,var(--blue-mid) 100%);padding:3rem 0 2rem;position:relative;overflow:hidden">
  <div class="container text-center position-relative" style="z-index:2">
    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3"
         style="background:rgba(201,162,39,.2);border:1px solid rgba(201,162,39,.4)">
      <i class="bi bi-mortarboard-fill" style="color:var(--gold-light)"></i>
      <span style="color:var(--gold-light);font-size:.8rem;font-weight:600">
        PMB <?= htmlspecialchars($tahunAktif['nama'] ?? '2026/2027') ?>
      </span>
    </div>
    <h1 class="text-white fw-bold mb-2" style="font-family:'Playfair Display',serif;font-size:clamp(1.4rem,4vw,2rem)">
      Formulir Pendaftaran Mahasiswa Baru
    </h1>
    <p class="mb-0" style="color:rgba(255,255,255,.7);font-size:.9rem">
      Ma'had Aly Ibnu Abbas Karanganyar — Proses cepat &amp; mudah
    </p>
  </div>
</section>

<div class="container" style="margin-top:2rem;padding-bottom:4rem">
  <div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">

      <!-- STEP INDICATOR -->
      <div class="rounded-4 p-3 mb-4" style="background:#fff;box-shadow:0 4px 20px rgba(0,0,0,.08)">
        <div class="d-flex align-items-center" id="stepIndicator">
          <?php $steps = ['Data Diri','Program Studi','Dokumen','Review','Kirim']; ?>
          <?php foreach ($steps as $i => $step): ?>
          <div class="d-flex flex-column align-items-center flex-fill step-item <?= $i===0?'active':'' ?>" data-step="<?= $i+1 ?>">
            <div class="step-circle <?= $i===0?'active':'' ?>">
              <span class="step-num"><?= $i+1 ?></span>
              <i class="bi bi-check-lg step-check d-none"></i>
            </div>
            <div class="step-label d-none d-sm-block"><?= $step ?></div>
          </div>
          <?php if ($i < 4): ?>
          <div class="step-line flex-fill" id="line-<?= $i+1 ?>"></div>
          <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- FORM -->
      <div class="rounded-4" style="background:#fff;box-shadow:0 4px 20px rgba(0,0,0,.08);overflow:hidden">
        <form id="regForm" novalidate>
          <input type="hidden" name="csrf_token" value="<?= Security::generateCsrf() ?>">
          <input type="hidden" name="tahun_akademik_id" value="<?= $tahunAktif['id'] ?? '' ?>">

          <!-- ──── STEP 1: DATA DIRI ──── -->
          <div class="form-step active" data-step="1">
            <div class="p-4 p-lg-5">
              <div class="d-flex align-items-center gap-3 mb-4 pb-3" style="border-bottom:2px solid var(--blue-pale)">
                <div style="width:44px;height:44px;border-radius:12px;background:var(--blue-pale);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--blue-main)">
                  <i class="bi bi-person-fill"></i>
                </div>
                <div>
                  <h5 class="mb-0 fw-700" style="color:var(--blue-main)">Data Diri Pendaftar</h5>
                  <p class="mb-0 text-muted" style="font-size:.78rem">Isi data sesuai dokumen resmi</p>
                </div>
              </div>

              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label fw-600 small required">Nama Lengkap</label>
                  <input type="text" name="nama_lengkap" class="form-control" required
                         placeholder="Sesuai Ijazah / Dokumen Resmi">
                  <div class="form-text">Untuk S2: sesuai ijazah S1</div>
                </div>
                <div class="col-md-5">
                  <label class="form-label fw-600 small required">Tempat Lahir</label>
                  <input type="text" name="tempat_lahir" class="form-control" required placeholder="Nama kota">
                </div>
                <div class="col-md-7">
                  <label class="form-label fw-600 small required">Tanggal Lahir</label>
                  <input type="date" name="tanggal_lahir" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-600 small required">Jenis Kelamin</label>
                  <select name="jenis_kelamin" class="form-select" required>
                    <option value="">-- Pilih --</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-600 small required">Nomor HP / WhatsApp</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                    <input type="tel" name="nomor_hp" class="form-control" required
                           placeholder="08xxxxxxxxxx" maxlength="16"
                           oninput="validatePhone(this)">
                  </div>
                  <div class="form-text text-muted" style="font-size:.75rem">Format: 08xx-xxxx-xxxx (8–16 digit)</div>
                </div>
                <div class="col-12">
                  <label class="form-label fw-600 small required">Nama Ibu Kandung</label>
                  <input type="text" name="nama_ibu_kandung" class="form-control" required placeholder="Nama ibu kandung">
                </div>
                <div class="col-12">
                  <label class="form-label fw-600 small required">Alamat Lengkap (sesuai KTP)</label>
                  <textarea name="alamat" class="form-control" rows="2" required
                            placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi"></textarea>
                </div>
                <div class="col-12">
                  <hr class="my-1">
                  <p class="fw-600 small mb-2" style="color:var(--blue-main)"><i class="bi bi-lock-fill me-1"></i>Buat Password Akun</p>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-600 small required">Password</label>
                  <div class="input-group">
                    <input type="password" name="password" class="form-control" id="pw1" required minlength="8"
                           placeholder="Min. 8 karakter">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePw('pw1',this)" tabindex="-1">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-600 small required">Konfirmasi Password</label>
                  <div class="input-group">
                    <input type="password" name="password_confirm" class="form-control" id="pw2" required
                           placeholder="Ulangi password">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePw('pw2',this)" tabindex="-1">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="px-4 px-lg-5 pb-4 d-flex justify-content-end">
              <button type="button" class="btn-next btn-primary-blue">
                Berikutnya <i class="bi bi-arrow-right ms-1"></i>
              </button>
            </div>
          </div>

          <!-- ──── STEP 2: PILIH PROGRAM ──── -->
          <div class="form-step" data-step="2" style="display:none">
            <div class="p-4 p-lg-5">
              <div class="d-flex align-items-center gap-3 mb-4 pb-3" style="border-bottom:2px solid var(--blue-pale)">
                <div style="width:44px;height:44px;border-radius:12px;background:var(--blue-pale);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--blue-main)">
                  <i class="bi bi-mortarboard"></i>
                </div>
                <div>
                  <h5 class="mb-0 fw-700" style="color:var(--blue-main)">Pilih Program Studi</h5>
                  <p class="mb-0 text-muted" style="font-size:.78rem">Pilih satu program studi yang Anda inginkan</p>
                </div>
              </div>

              <div class="row g-3" id="prodiCards">
                <?php
                $grouped = [];
                foreach ($prodiList as $pr) { $grouped[$pr['jenjang']][] = $pr; }
                ksort($grouped);
                foreach ($grouped as $jenjang => $prodis):
                ?>
                <div class="col-12">
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge rounded-pill <?= $jenjang==='S2'?'bg-warning text-dark':'bg-primary' ?>"><?= $jenjang ?></span>
                    <span class="fw-700 small" style="color:var(--text-mid)">Program <?= $jenjang === 'S2' ? 'Magister' : 'Sarjana' ?></span>
                  </div>
                </div>
                <?php foreach ($prodis as $pr): ?>
                <div class="col-md-6">
                  <label class="prodi-card" for="prodi_<?= $pr['id'] ?>" style="cursor:pointer;display:block;border:2px solid var(--border);border-radius:12px;padding:1rem;transition:.2s">
                    <input type="radio" name="program_studi_id" id="prodi_<?= $pr['id'] ?>"
                           value="<?= $pr['id'] ?>" class="prodi-radio" required
                           data-jenjang="<?= htmlspecialchars($pr['jenjang']) ?>"
                           data-nama="<?= htmlspecialchars($pr['nama_prodi']) ?>"
                           data-gelar="<?= htmlspecialchars($pr['gelar']) ?>"
                           data-biaya-daftar="<?= (int)($pr['biaya_pendaftaran'] ?? 0) ?>"
                           data-biaya-spp="<?= (int)($pr['biaya_spp'] ?? 0) ?>"
                           style="position:absolute;opacity:0;pointer-events:none">
                    <div class="d-flex align-items-start gap-3">
                      <div class="prodi-check-icon mt-1" style="width:20px;height:20px;border-radius:50%;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:.2s">
                        <i class="bi bi-check-lg" style="font-size:.7rem;color:#fff;display:none"></i>
                      </div>
                      <div class="flex-fill">
                        <div class="fw-700" style="font-size:.9rem;color:var(--blue-main)">
                          <?= htmlspecialchars($pr['nama_prodi']) ?>
                        </div>
                        <div class="text-muted" style="font-size:.75rem;margin-top:2px">
                          <?= htmlspecialchars($pr['fakultas'] ?? $pr['nama_fakultas'] ?? '') ?>
                          · Gelar: <strong><?= htmlspecialchars($pr['gelar']) ?></strong>
                        </div>
                        <div class="mt-2 d-flex flex-wrap gap-2 align-items-center">
                          <span class="badge rounded-pill" style="background:var(--blue-pale);color:var(--blue-main);font-size:.72rem">
                            <i class="bi bi-receipt me-1"></i>Daftar: Rp <?= number_format($pr['biaya_pendaftaran'] ?? 0) ?>
                          </span>
                          <?php if (($pr['biaya_spp'] ?? 0) > 0): ?>
                          <span class="badge rounded-pill" style="background:var(--gold-pale);color:var(--gold-deep);font-size:.72rem">
                            SPP: Rp <?= number_format($pr['biaya_spp']) ?>/bln
                          </span>
                          <?php endif; ?>
                          <?php if (($pr['biaya_pendidikan'] ?? 0) > 0): ?>
                          <span class="badge rounded-pill bg-warning text-dark" style="font-size:.72rem">
                            s/d Lulus: Rp <?= number_format($pr['biaya_pendidikan']) ?>
                          </span>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </label>
                </div>
                <?php endforeach; ?>
                <?php endforeach; ?>
              </div>

              <!-- Biaya terpilih -->
              <div id="biayaInfo" class="mt-4 p-3 rounded-3" style="background:var(--blue-pale);border:1px solid rgba(26,58,107,.2);display:none">
                <h6 class="fw-700 mb-2" style="color:var(--blue-main);font-size:.85rem">
                  <i class="bi bi-check-circle-fill me-1"></i>Program Dipilih
                </h6>
                <div id="biayaDetail" style="font-size:.85rem"></div>
              </div>
            </div>
            <div class="px-4 px-lg-5 pb-4 d-flex justify-content-between">
              <button type="button" class="btn-prev btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali
              </button>
              <button type="button" class="btn-next btn-primary-blue">
                Berikutnya <i class="bi bi-arrow-right ms-1"></i>
              </button>
            </div>
          </div>

          <!-- ──── STEP 3: UPLOAD DOKUMEN ──── -->
          <div class="form-step" data-step="3" style="display:none">
            <div class="p-4 p-lg-5">
              <div class="d-flex align-items-center gap-3 mb-3 pb-3" style="border-bottom:2px solid var(--blue-pale)">
                <div style="width:44px;height:44px;border-radius:12px;background:var(--blue-pale);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--blue-main)">
                  <i class="bi bi-cloud-upload"></i>
                </div>
                <div>
                  <h5 class="mb-0 fw-700" style="color:var(--blue-main)">Upload Dokumen</h5>
                  <p class="mb-0 text-muted" style="font-size:.78rem">PDF, JPG, PNG · Maks. 5 MB per file</p>
                </div>
              </div>

              <div class="rounded-3 p-3 mb-4 d-flex gap-2 align-items-start"
                   style="background:#fffbeb;border:1px solid #fde68a;font-size:.82rem">
                <i class="bi bi-info-circle-fill mt-1" style="color:#d97706;flex-shrink:0"></i>
                <span style="color:#92400e">
                  Pastikan dokumen <strong>jelas, tidak buram</strong>, dan ukuran file tidak melebihi 5 MB.
                  Dokumen dengan tanda <span class="text-danger fw-bold">*</span> wajib diupload sekarang.
                </span>
              </div>

              <div class="row g-3">
                <?php
                $docs = [
                  ['key'=>'ktp',        'label'=>'Scan KTP Asli',                        'icon'=>'bi-card-heading',  'required'=>true],
                  ['key'=>'kk',         'label'=>'Scan Kartu Keluarga (KK)',              'icon'=>'bi-people',        'required'=>true],
                  ['key'=>'akte',       'label'=>'Scan Akte Kelahiran',                   'icon'=>'bi-file-person',   'required'=>true],
                  ['key'=>'ijazah_sma', 'label'=>'Scan Ijazah SMA + Transkrip',           'icon'=>'bi-award',         'required'=>true],
                  ['key'=>'ijazah_s1',  'label'=>'Scan Ijazah S1 + Transkrip (khusus S2)','icon'=>'bi-award-fill',    'required'=>false],
                  ['key'=>'foto',       'label'=>'Foto Resmi (Jas Hitam, BG Biru)',       'icon'=>'bi-camera',        'required'=>true],
                ];
                foreach ($docs as $doc):
                ?>
                <div class="col-md-6">
                  <div class="rounded-3 p-3"
                       style="border:2px dashed <?= $doc['required']?'var(--border)':'rgba(201,162,39,.4)' ?>;transition:.2s;background:<?= $doc['required']?'#fafafa':'#fffdf5' ?>"
                       ondragover="handleDragOver(event,this)"
                       ondragleave="handleDragLeave(this)"
                       ondrop="handleDrop(event,'doc_<?= $doc['key'] ?>')">
                    <label class="d-flex align-items-center gap-2 fw-600 mb-2 cursor-pointer" for="doc_<?= $doc['key'] ?>"
                           style="font-size:.82rem;cursor:pointer">
                      <i class="bi <?= $doc['icon'] ?> <?= $doc['required']?'text-blue':'text-gold' ?>"></i>
                      <?= $doc['label'] ?>
                      <?php if ($doc['required']): ?>
                      <span class="text-danger">*</span>
                      <?php else: ?>
                      <span class="badge" style="background:var(--gold-pale);color:var(--gold-deep);font-size:.68rem;font-weight:500">Bisa Menyusul</span>
                      <?php endif; ?>
                    </label>
                    <input type="file" name="doc_<?= $doc['key'] ?>" id="doc_<?= $doc['key'] ?>"
                           class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png"
                           <?= $doc['required']?'required':'' ?>
                           onchange="previewFile(this,'prev_<?= $doc['key'] ?>')">
                    <div id="prev_<?= $doc['key'] ?>" class="mt-2"></div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="px-4 px-lg-5 pb-4 d-flex justify-content-between">
              <button type="button" class="btn-prev btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali
              </button>
              <button type="button" class="btn-next btn-primary-blue">
                Berikutnya <i class="bi bi-arrow-right ms-1"></i>
              </button>
            </div>
          </div>

          <!-- ──── STEP 4: REVIEW ──── -->
          <div class="form-step" data-step="4" style="display:none">
            <div class="p-4 p-lg-5">
              <div class="d-flex align-items-center gap-3 mb-4 pb-3" style="border-bottom:2px solid var(--blue-pale)">
                <div style="width:44px;height:44px;border-radius:12px;background:var(--blue-pale);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--blue-main)">
                  <i class="bi bi-clipboard-check"></i>
                </div>
                <div>
                  <h5 class="mb-0 fw-700" style="color:var(--blue-main)">Review Data</h5>
                  <p class="mb-0 text-muted" style="font-size:.78rem">Periksa kembali sebelum mengirim</p>
                </div>
              </div>

              <div class="row g-3 mb-4" id="reviewContent">
                <div class="col-12 text-center py-4 text-muted">
                  <div class="spinner-border spinner-border-sm me-2"></div> Memuat ringkasan...
                </div>
              </div>

              <div class="p-3 rounded-3 d-flex gap-2 align-items-start"
                   style="background:#fffbeb;border:1px solid #fde68a;font-size:.82rem">
                <i class="bi bi-exclamation-triangle-fill mt-1" style="color:#d97706;flex-shrink:0"></i>
                <span style="color:#92400e">
                  Pastikan semua data sudah benar. Data yang sudah dikirim akan diverifikasi oleh tim admin.
                </span>
              </div>
            </div>
            <div class="px-4 px-lg-5 pb-4 d-flex justify-content-between">
              <button type="button" class="btn-prev btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali
              </button>
              <button type="button" class="btn-next btn-primary-gold">
                <i class="bi bi-send-fill me-2"></i>Konfirmasi & Kirim
              </button>
            </div>
          </div>

          <!-- ──── STEP 5: SUBMIT ──── -->
          <div class="form-step" data-step="5" style="display:none">
            <div class="p-4 p-lg-5 text-center">
              <div id="submitProgress" class="py-4">
                <div class="mb-3">
                  <div class="spinner-border text-primary" style="width:3rem;height:3rem"></div>
                </div>
                <h5 class="fw-700" style="color:var(--blue-main)">Mengirim Pendaftaran...</h5>
                <p class="text-muted small">Mohon tunggu, jangan tutup halaman ini.</p>
              </div>
              <div id="submitResult" style="display:none"></div>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<style>
.form-label.required::after { content:' *'; color:#dc2626 }
.step-circle {
  width:36px;height:36px;border-radius:50%;border:2px solid var(--border);
  display:flex;align-items:center;justify-content:center;
  font-weight:700;font-size:.82rem;background:#fff;color:#94a3b8;
  transition:.3s;flex-shrink:0
}
.step-circle.active { border-color:var(--blue-main);background:var(--blue-main);color:#fff }
.step-circle.done   { border-color:#16a34a;background:#16a34a;color:#fff }
.step-label { font-size:.68rem;font-weight:600;color:#94a3b8;margin-top:5px;text-align:center }
.step-item.active .step-label { color:var(--blue-main) }
.step-item.done   .step-label { color:#16a34a }
.step-line { height:2px;background:var(--border);position:relative;top:-18px;margin:0 4px;transition:.4s }
.step-line.done { background:#16a34a }
.prodi-card:hover { border-color:var(--blue-main)!important;background:var(--blue-pale)!important }
.prodi-card.selected { border-color:var(--blue-main)!important;background:var(--blue-pale)!important }
.prodi-card.selected .prodi-check-icon { border-color:var(--blue-main)!important;background:var(--blue-main)!important }
.prodi-card.selected .prodi-check-icon i { display:inline!important }
.upload-area:hover { border-color:var(--blue-main)!important }
</style>

<script>
const BASE_URL = '<?= BASE_URL ?>';
const SAVE_KEY  = 'pmb_form_data';
let currentStep = 1;
const totalSteps = 5;

// ── Simpan data form ke sessionStorage ──────────────────────────
function saveFormData() {
  try {
    const fd = new FormData(document.getElementById('regForm'));
    const data = {};
    for (const [k, v] of fd.entries()) {
      // Jangan simpan password dan file
      if (['password','password_confirm','csrf_token'].includes(k)) continue;
      if (v instanceof File) continue;
      data[k] = v;
    }
    // Simpan juga prodi yang dipilih
    const radio = document.querySelector('.prodi-radio:checked');
    if (radio) {
      data['_prodi_id']    = radio.value;
      data['_prodi_nama']  = radio.dataset.nama;
      data['_prodi_jenjang']= radio.dataset.jenjang;
    }
    sessionStorage.setItem(SAVE_KEY, JSON.stringify(data));
  } catch(e) {}
}

// ── Pulihkan data dari sessionStorage ───────────────────────────
function restoreFormData() {
  try {
    const raw = sessionStorage.getItem(SAVE_KEY);
    if (!raw) return;
    const data = JSON.parse(raw);
    Object.entries(data).forEach(([k, v]) => {
      if (k.startsWith('_')) return; // skip metadata
      const el = document.querySelector(`[name="${k}"]`);
      if (!el) return;
      if (el.tagName === 'SELECT' || el.type === 'text' || el.type === 'tel'
          || el.type === 'date'  || el.type === 'email' || el.tagName === 'TEXTAREA') {
        el.value = v;
      }
    });
    // Pulihkan pilihan prodi
    if (data['_prodi_id']) {
      const radio = document.querySelector(`.prodi-radio[value="${data['_prodi_id']}"]`);
      if (radio) {
        radio.checked = true;
        radio.closest('.prodi-card')?.classList.add('selected');
        const icon = radio.closest('.prodi-card')?.querySelector('.prodi-check-icon i');
        if (icon) icon.style.display = 'inline';
        const checkIcon = radio.closest('.prodi-card')?.querySelector('.prodi-check-icon');
        if (checkIcon) { checkIcon.style.borderColor='var(--blue-main)'; checkIcon.style.background='var(--blue-main)'; }
        updateBiayaInfo(radio.dataset);
      }
    }
  } catch(e) {}
}

// ── Auto-save saat input berubah ────────────────────────────────
document.getElementById('regForm').addEventListener('change', saveFormData);
document.getElementById('regForm').addEventListener('input',  saveFormData);

function showStep(step) {
  document.querySelectorAll('.form-step').forEach(s => s.style.display = 'none');
  const el = document.querySelector(`.form-step[data-step="${step}"]`);
  if (el) el.style.display = 'block';

  document.querySelectorAll('.step-circle').forEach((c, i) => {
    const sn = i + 1;
    c.classList.remove('active','done');
    c.querySelector('.step-num').classList.remove('d-none');
    c.querySelector('.step-check').classList.add('d-none');
    if (sn < step)      { c.classList.add('done'); c.querySelector('.step-num').classList.add('d-none'); c.querySelector('.step-check').classList.remove('d-none'); }
    else if (sn === step) c.classList.add('active');
  });
  document.querySelectorAll('.step-item').forEach((item, i) => {
    item.classList.remove('active','done');
    const sn = i + 1;
    if (sn < step)      item.classList.add('done');
    else if (sn === step) item.classList.add('active');
  });
  document.querySelectorAll('.step-line').forEach((line, i) => {
    line.classList.toggle('done', (i + 1) < step);
  });

  if (step === 4) buildReview();
  if (step === 5) submitForm();
  currentStep = step;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function validateStep(step) {
  const stepEl = document.querySelector(`.form-step[data-step="${step}"]`);
  if (!stepEl) return true;
  const inputs = stepEl.querySelectorAll('[required]');
  let valid = true;
  let firstInvalid = null;

  inputs.forEach(inp => {
    inp.classList.remove('is-invalid');
    const isEmpty = inp.type === 'file' ? inp.files.length === 0 : !inp.value.trim();
    if (isEmpty) {
      inp.classList.add('is-invalid');
      valid = false;
      if (!firstInvalid) firstInvalid = inp;
    }
  });

  // Validasi password khusus step 1
  if (step === 1) {
    const pw1El = document.querySelector('[name=password]');
    const pw2El = document.querySelector('[name=password_confirm]');
    const pw1   = pw1El?.value || '';
    const pw2   = pw2El?.value || '';

    // Cek panjang minimal
    if (pw1.length > 0 && pw1.length < 8) {
      pw1El.classList.add('is-invalid');
      // Tampilkan pesan di bawah field
      showFieldError(pw1El, 'Password minimal 8 karakter');
      if (!firstInvalid) firstInvalid = pw1El;
      valid = false;
    }
    // Cek kecocokan - hanya jika keduanya sudah diisi
    if (pw1.length >= 8 && pw2.length > 0 && pw1 !== pw2) {
      pw2El.classList.add('is-invalid');
      showFieldError(pw2El, 'Password tidak cocok');
      if (!firstInvalid) firstInvalid = pw2El;
      valid = false;
    }
    // Jika pw2 kosong dan pw1 sudah valid
    if (valid && pw1.length >= 8 && pw2.length === 0) {
      pw2El.classList.add('is-invalid');
      showFieldError(pw2El, 'Konfirmasi password wajib diisi');
      firstInvalid = pw2El;
      valid = false;
    }
  }

  if (!valid) {
    showToast('Periksa kembali data yang belum lengkap', 'danger');
    if (firstInvalid) firstInvalid.focus();
  }
  return valid;
}

// ── Tampilkan pesan error di bawah field ────────────────────────
function validatePhone(el) {
  // Hanya izinkan angka, +, -, spasi
  el.value = el.value.replace(/[^0-9+\-\s]/g, '');
  const digits = el.value.replace(/[^0-9]/g, '');
  const existing = el.closest('.input-group').nextElementSibling;
  // Hapus pesan error lama
  const errDiv = el.closest('.mb-3')?.querySelector('.field-error-msg');
  if (errDiv) errDiv.remove();
  el.classList.remove('is-invalid','is-valid');
  if (digits.length > 0) {
    if (digits.length < 8) {
      el.classList.add('is-invalid');
    } else {
      el.classList.add('is-valid');
    }
  }
}

function showFieldError(el, msg) {
  // Hapus pesan lama
  const existing = el.parentElement.querySelector('.field-error-msg');
  if (existing) existing.remove();
  const div = document.createElement('div');
  div.className = 'field-error-msg';
  div.style.cssText = 'color:#dc2626;font-size:.78rem;margin-top:.25rem';
  div.textContent = msg;
  el.parentElement.appendChild(div);
  // Hapus otomatis saat field diubah
  el.addEventListener('input', () => { div.remove(); el.classList.remove('is-invalid'); }, { once: true });
}

// ── Real-time password check ────────────────────────────────────
document.getElementById('pw2')?.addEventListener('input', function() {
  const pw1 = document.getElementById('pw1')?.value || '';
  const pw2 = this.value;
  const existing = this.parentElement.querySelector('.field-error-msg');
  if (existing) existing.remove();
  if (pw2.length > 0 && pw1 !== pw2) {
    this.classList.add('is-invalid');
    showFieldError(this, 'Password tidak cocok');
  } else if (pw2.length > 0 && pw1 === pw2) {
    this.classList.remove('is-invalid');
    this.classList.add('is-valid');
  }
});
document.getElementById('pw1')?.addEventListener('input', function() {
  const pw2El = document.getElementById('pw2');
  if (pw2El?.value) pw2El.dispatchEvent(new Event('input'));
  const existing = this.parentElement.querySelector('.field-error-msg');
  if (existing) existing.remove();
  if (this.value.length > 0 && this.value.length < 8) {
    this.classList.add('is-invalid');
    showFieldError(this, `${this.value.length}/8 karakter minimum`);
  } else if (this.value.length >= 8) {
    this.classList.remove('is-invalid');
    this.classList.add('is-valid');
  }
});

document.querySelectorAll('.btn-next').forEach(btn => {
  btn.addEventListener('click', () => {
    saveFormData();
    if (validateStep(currentStep)) showStep(currentStep + 1);
  });
});
document.querySelectorAll('.btn-prev').forEach(btn => {
  btn.addEventListener('click', () => { if (currentStep > 1) showStep(currentStep - 1); });
});

// Prodi selection
document.querySelectorAll('.prodi-radio').forEach(radio => {
  radio.addEventListener('change', function() {
    document.querySelectorAll('.prodi-card').forEach(c => c.classList.remove('selected'));
    this.closest('.prodi-card').classList.add('selected');
    updateBiayaInfo(this.dataset);
  });
});
// Click on card label
document.querySelectorAll('.prodi-card').forEach(card => {
  card.addEventListener('click', function() {
    const radio = this.querySelector('.prodi-radio');
    if (radio) { radio.checked = true; radio.dispatchEvent(new Event('change')); }
  });
});

function updateBiayaInfo(d) {
  const box = document.getElementById('biayaInfo');
  const det = document.getElementById('biayaDetail');
  const biayaDaftar = parseInt(d.biayaDaftar || 0);
  const biayaSpp   = parseInt(d.biayaSpp   || 0);
  box.style.display = 'block';
  det.innerHTML = `
    <div class="fw-600">${d.nama} <span class="badge bg-primary" style="font-size:.7rem">${d.jenjang}</span></div>
    <div class="mt-1 d-flex flex-wrap gap-3" style="font-size:.83rem">
      <span><i class="bi bi-receipt me-1 text-blue"></i>Biaya Daftar: <strong>Rp ${biayaDaftar.toLocaleString('id')}</strong></span>
      ${biayaSpp > 0 ? `<span><i class="bi bi-calendar-month me-1 text-gold"></i>SPP: <strong>Rp ${biayaSpp.toLocaleString('id')}/bln</strong></span>` : ''}
    </div>`;
}

function previewFile(input, previewId) {
  const prev = document.getElementById(previewId);
  if (!prev) return;
  const file = input.files[0];
  if (!file) { prev.innerHTML=''; return; }
  if (file.size > 5*1024*1024) {
    input.value=''; showToast('Ukuran file maksimal 5 MB','danger'); return;
  }
  const ext = file.name.split('.').pop().toLowerCase();
  if (!['pdf','jpg','jpeg','png'].includes(ext)) {
    input.value=''; showToast('Format tidak didukung. Gunakan PDF/JPG/PNG','danger'); return;
  }
  if (ext === 'pdf') {
    prev.innerHTML = `<div class="d-flex align-items-center gap-2 p-2 rounded mt-1" style="background:#fff1f2;border:1px solid #fecdd3;font-size:.75rem">
      <i class="bi bi-file-earmark-pdf text-danger"></i><span class="fw-600">${file.name}</span>
      <span class="text-muted ms-auto">${(file.size/1024/1024).toFixed(1)}MB</span></div>`;
  } else {
    const r = new FileReader();
    r.onload = e => {
      prev.innerHTML = `<img src="${e.target.result}" class="mt-1 rounded" style="max-height:70px;max-width:100%;border:1px solid var(--border)">`;
    };
    r.readAsDataURL(file);
  }
}

function handleDragOver(e, el) {
  e.preventDefault(); el.style.borderColor='var(--blue-main)'; el.style.background='var(--blue-pale)';
}
function handleDragLeave(el) {
  el.style.borderColor=''; el.style.background='';
}
function handleDrop(e, inputId) {
  e.preventDefault();
  const inp = document.getElementById(inputId);
  const file = e.dataTransfer.files[0];
  if (file && inp) {
    const dt = new DataTransfer(); dt.items.add(file);
    inp.files = dt.files;
    previewFile(inp, 'prev_' + inputId.replace('doc_',''));
  }
  handleDragLeave(e.currentTarget);
}

function buildReview() {
  const fd = new FormData(document.getElementById('regForm'));
  const radio = document.querySelector('.prodi-radio:checked');
  const biaya = parseInt(radio?.dataset.biayaDaftar || 0);
  document.getElementById('reviewContent').innerHTML = `
    <div class="col-md-6">
      <div class="p-3 rounded-3" style="background:var(--off-white);border:1px solid var(--border);font-size:.84rem">
        <div class="fw-700 mb-3" style="color:var(--blue-main)"><i class="bi bi-person-fill me-1"></i>Data Diri</div>
        <div class="d-flex flex-column gap-2">
          <div class="d-flex justify-content-between"><span class="text-muted">Nama</span><strong class="text-end" style="max-width:60%">${fd.get('nama_lengkap')||'-'}</strong></div>
          <div class="d-flex justify-content-between"><span class="text-muted">TTL</span><span>${fd.get('tempat_lahir')||''}, ${fd.get('tanggal_lahir')||''}</span></div>
          <div class="d-flex justify-content-between"><span class="text-muted">Jenis Kelamin</span><span>${fd.get('jenis_kelamin')==='L'?'Laki-laki':'Perempuan'}</span></div>
          <div class="d-flex justify-content-between"><span class="text-muted">No. HP</span><span>${fd.get('nomor_hp')||'-'}</span></div>
          <div class="d-flex justify-content-between"><span class="text-muted">Nama Ibu</span><span>${fd.get('nama_ibu_kandung')||'-'}</span></div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="p-3 rounded-3" style="background:var(--off-white);border:1px solid var(--border);font-size:.84rem">
        <div class="fw-700 mb-3" style="color:var(--blue-main)"><i class="bi bi-mortarboard me-1"></i>Program Studi</div>
        <div class="d-flex flex-column gap-2">
          <div class="fw-600">${radio?.dataset.nama||'-'}</div>
          <div><span class="badge ${radio?.dataset.jenjang==='S2'?'bg-warning text-dark':'bg-primary'}">${radio?.dataset.jenjang||''}</span></div>
          <div class="d-flex justify-content-between mt-1">
            <span class="text-muted">Biaya Pendaftaran</span>
            <strong>Rp ${biaya.toLocaleString('id')}</strong>
          </div>
        </div>
      </div>
    </div>`;
}

function togglePw(id, btn) {
  const inp = document.getElementById(id);
  const show = inp.type === 'password';
  inp.type = show ? 'text' : 'password';
  btn.innerHTML = `<i class="bi bi-eye${show?'-slash':''}"></i>`;
}

function submitForm() {
  const fd = new FormData(document.getElementById('regForm'));
  fetch(BASE_URL + '/index.php?page=daftar/submit', { method:'POST', body:fd })
    .then(r => r.json())
    .then(data => {
      document.getElementById('submitProgress').style.display = 'none';
      const res = document.getElementById('submitResult');
      res.style.display = 'block';
      if (data.success) {
        sessionStorage.removeItem(SAVE_KEY); // Hapus data setelah berhasil
        const nomor = data.nomor_pendaftaran || data.nomor || '';
        res.innerHTML = `
          <div class="py-3">
            <div style="font-size:3.5rem;color:#16a34a"><i class="bi bi-check-circle-fill"></i></div>
            <h4 class="fw-700 mt-3" style="color:var(--blue-main)">Pendaftaran Berhasil!</h4>
            <p class="text-muted mb-3">Nomor pendaftaran Anda:</p>
            <div class="d-inline-block px-4 py-3 rounded-3 mb-4"
                 style="background:var(--blue-pale);border:2px solid var(--blue-main)">
              <span style="font-size:1.4rem;font-weight:800;color:var(--blue-main);letter-spacing:.06em">${nomor}</span>
            </div>
            <p class="text-muted mb-4" style="font-size:.85rem;max-width:420px;margin:0 auto 1.5rem">
              <i class="bi bi-info-circle me-1"></i>
              Simpan nomor ini. Gunakan untuk login ke dashboard pendaftar.
            </p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
              <a href="${BASE_URL}/daftar/sukses/${encodeURIComponent(nomor)}" class="btn-primary-gold">
                <i class="bi bi-eye me-1"></i> Lihat Status Pendaftaran
              </a>
              <a href="${BASE_URL}/login" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-box-arrow-in-right me-1"></i> Login
              </a>
            </div>
          </div>`;
      } else {
        res.innerHTML = `
          <div class="py-3">
            <div style="font-size:3rem;color:#dc2626"><i class="bi bi-x-circle-fill"></i></div>
            <h5 class="fw-700 mt-3 text-danger">Pendaftaran Gagal</h5>
            <p class="text-muted">${data.message||'Terjadi kesalahan. Silakan coba lagi.'}</p>
            ${data.errors ? '<ul class="text-start text-danger small">'+Object.values(data.errors).map(e=>`<li>${e}</li>`).join('')+'</ul>' : ''}
            <button class="btn btn-outline-primary rounded-pill px-4 mt-2" onclick="showStep(4)">
              <i class="bi bi-arrow-left me-1"></i> Kembali
            </button>
          </div>`;
      }
    })
    .catch(() => {
      document.getElementById('submitProgress').style.display = 'none';
      document.getElementById('submitResult').style.display = 'block';
      document.getElementById('submitResult').innerHTML = `
        <div class="py-3">
          <div style="font-size:3rem;color:#dc2626"><i class="bi bi-wifi-off"></i></div>
          <h5 class="fw-700 mt-3 text-danger">Koneksi Terputus</h5>
          <p class="text-muted mb-3">Data Anda <strong>masih tersimpan</strong>. Periksa koneksi dan coba lagi.</p>
          <div class="d-flex gap-2 justify-content-center flex-wrap">
            <button class="btn-primary-blue" onclick="retrySubmit()">
              <i class="bi bi-arrow-clockwise me-1"></i> Coba Kirim Lagi
            </button>
            <button class="btn btn-outline-secondary rounded-pill px-4" onclick="showStep(4)">
              <i class="bi bi-arrow-left me-1"></i> Kembali ke Review
            </button>
          </div>
        </div>`;
    });
}

// ── Retry submit tanpa isi ulang ────────────────────────────────
function retrySubmit() {
  document.getElementById('submitResult').style.display = 'none';
  document.getElementById('submitProgress').style.display = 'block';
  submitForm();
}

// ── Restore data saat halaman dibuka kembali ───────────────────
window.addEventListener('DOMContentLoaded', () => {
  restoreFormData();
  // Tampilkan notif jika ada data tersimpan
  try {
    const raw = sessionStorage.getItem(SAVE_KEY);
    if (raw) {
      const data = JSON.parse(raw);
      if (data.nama_lengkap) {
        showToast('Data sebelumnya dipulihkan. Anda dapat melanjutkan dari langkah terakhir.', 'info');
      }
    }
  } catch(e) {}
});

function showToast(msg, type='info') {
  const colors = {success:'#16a34a',danger:'#dc2626',info:'#1a3a6b',warning:'#b45309'};
  const el = document.createElement('div');
  el.style.cssText = `position:fixed;bottom:1.5rem;right:1.5rem;background:#fff;padding:.8rem 1.2rem;border-radius:.6rem;box-shadow:0 4px 20px rgba(0,0,0,.15);border-left:4px solid ${colors[type]||colors.info};font-size:.84rem;font-weight:500;z-index:9999;max-width:320px;animation:slideIn .3s ease`;
  el.textContent = msg;
  document.body.appendChild(el);
  setTimeout(() => { el.style.opacity='0'; el.style.transform='translateX(100%)'; el.style.transition='.3s'; setTimeout(()=>el.remove(),300); }, 3500);
}
</script>