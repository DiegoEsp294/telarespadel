<style>
.sponsor-form-wrap {
    max-width: 680px;
    margin: 40px auto;
    padding: 0 20px 60px;
}
.sponsor-form-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 16px rgba(0,0,0,.09);
    padding: 36px 32px;
}
.sponsor-form-card h2 {
    font-size: 22px;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0 0 6px;
}
.sponsor-form-card .subtitle {
    color: #888;
    font-size: 14px;
    margin-bottom: 28px;
}
.form-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #555;
    font-size: 14px;
    text-decoration: none;
    margin-bottom: 20px;
    transition: color .2s;
}
.form-back:hover { color: #FF6600; }

.sp-form label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #444;
    margin-bottom: 5px;
    margin-top: 18px;
}
.sp-form label:first-of-type { margin-top: 0; }
.sp-form input[type="text"],
.sp-form input[type="url"],
.sp-form input[type="number"],
.sp-form select {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    color: #1a1a2e;
    background: #fafafa;
    transition: border-color .2s;
    box-sizing: border-box;
}
.sp-form input:focus,
.sp-form select:focus {
    outline: none;
    border-color: #FF6600;
    background: #fff;
}
.sp-form .hint {
    font-size: 11px;
    color: #aaa;
    margin-top: 4px;
}

.logo-preview-wrap {
    margin-top: 10px;
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}
.logo-preview-img {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    object-fit: contain;
    border: 1.5px solid #eee;
    background: #f5f5f5;
}
.logo-preview-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ccc;
    font-size: 32px;
    border: 1.5px dashed #ddd;
}
#logo-preview-live {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    object-fit: contain;
    border: 1.5px solid #FF6600;
    background: #f5f5f5;
    display: none;
}

.toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8f8f8;
    border-radius: 10px;
    padding: 12px 16px;
    margin-top: 18px;
    gap: 16px;
}
.toggle-row-info strong {
    font-size: 14px;
    color: #1a1a2e;
    display: block;
}
.toggle-row-info span {
    font-size: 12px;
    color: #999;
}
/* Toggle switch */
.toggle-switch {
    position: relative;
    width: 46px;
    height: 24px;
    flex-shrink: 0;
}
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background: #ccc;
    border-radius: 24px;
    transition: background .2s;
}
.toggle-slider:before {
    content: '';
    position: absolute;
    width: 18px;
    height: 18px;
    left: 3px;
    bottom: 3px;
    background: #fff;
    border-radius: 50%;
    transition: transform .2s;
}
.toggle-switch input:checked + .toggle-slider { background: #FF6600; }
.toggle-switch input:checked + .toggle-slider:before { transform: translateX(22px); }

.sp-form-actions {
    display: flex;
    gap: 12px;
    margin-top: 30px;
    flex-wrap: wrap;
}
.btn-guardar {
    flex: 1;
    min-width: 140px;
    padding: 12px 20px;
    background: #FF6600;
    color: #fff;
    border: none;
    border-radius: 9px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: background .2s;
}
.btn-guardar:hover { background: #e55a00; }
.btn-cancelar {
    padding: 12px 20px;
    background: #f0f0f0;
    color: #555;
    border: none;
    border-radius: 9px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
    transition: background .2s;
}
.btn-cancelar:hover { background: #e0e0e0; color: #333; }

@media (max-width: 520px) {
    .sponsor-form-card { padding: 24px 16px; }
}
</style>

<div class="sponsor-form-wrap">

    <a href="<?= base_url('admin/Sponsors/index') ?>" class="form-back">
        <i class="fas fa-arrow-left"></i> Volver al listado
    </a>

    <div class="sponsor-form-card">
        <h2>
            <i class="fas fa-handshake"></i>
            <?= isset($sponsor) && $sponsor ? 'Editar Sponsor' : 'Nuevo Sponsor' ?>
        </h2>
        <p class="subtitle">
            <?= isset($sponsor) && $sponsor
                ? 'Modificá los datos del auspiciante.'
                : 'Completá los datos para agregar un nuevo auspiciante al ticker y la web.' ?>
        </p>

        <form class="sp-form"
              method="post"
              enctype="multipart/form-data"
              action="<?= isset($sponsor) && $sponsor
                  ? base_url('admin/Sponsors/actualizar/' . $sponsor->id)
                  : base_url('admin/Sponsors/guardar') ?>">

            <label>Nombre del sponsor *</label>
            <input type="text"
                   name="nombre"
                   placeholder="Ej: Farmacia Del Centro"
                   value="<?= htmlspecialchars($sponsor->nombre ?? '') ?>"
                   required>

            <label>Logo del sponsor</label>
            <input type="file"
                   name="logo"
                   id="logo-input"
                   accept="image/*"
                   style="padding:8px 0;">
            <p class="hint">PNG, JPG o SVG. Fondo transparente queda mejor. Máx. 2 MB.</p>

            <div class="logo-preview-wrap">
                <?php if (isset($sponsor) && $sponsor && $sponsor->logo): ?>
                    <img class="logo-preview-img"
                         src="data:image/png;base64,<?= $sponsor->logo ?>"
                         alt="Logo actual">
                    <span style="font-size:12px;color:#888;">Logo actual</span>
                <?php else: ?>
                    <div class="logo-placeholder-wrap" id="placeholder-icon">
                        <div class="logo-preview-placeholder"><i class="fas fa-image"></i></div>
                        <span style="font-size:12px;color:#bbb;margin-left:10px;">Sin logo</span>
                    </div>
                <?php endif; ?>
                <img id="logo-preview-live" src="" alt="Vista previa">
            </div>

            <label>Sitio web</label>
            <input type="url"
                   name="sitio_web"
                   placeholder="https://www.ejemplo.com"
                   value="<?= htmlspecialchars($sponsor->sitio_web ?? '') ?>">
            <p class="hint">Si el usuario hace clic en el logo será redirigido aquí (opcional).</p>

            <label>Orden en el ticker</label>
            <input type="number"
                   name="orden"
                   min="0"
                   max="999"
                   value="<?= (int)($sponsor->orden ?? 0) ?>">
            <p class="hint">Menor número aparece primero. 0 = sin prioridad especial.</p>

            <label>Asociar a un torneo (opcional)</label>
            <select name="torneo_id">
                <option value="">— Global (aparece en todos lados) —</option>
                <?php foreach ($torneos as $t): ?>
                    <option value="<?= $t->id ?>"
                        <?= (isset($sponsor) && $sponsor && (int)$sponsor->torneo_id === (int)$t->id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t->nombre) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="hint">Si elegís un torneo, el sponsor se muestra también en la página de ese torneo.</p>

            <!-- Toggle: Activo -->
            <div class="toggle-row">
                <div class="toggle-row-info">
                    <strong>Activo</strong>
                    <span>Si está desactivado, el sponsor no se muestra en ningún lado.</span>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="activo" value="1"
                           <?= (!isset($sponsor) || !$sponsor || $sponsor->activo) ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <!-- Toggle: Global -->
            <div class="toggle-row">
                <div class="toggle-row-info">
                    <strong>Mostrar en ticker global</strong>
                    <span>Aparece en la barra de sponsors de todas las páginas.</span>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="es_global" value="1"
                           <?= (!isset($sponsor) || !$sponsor || $sponsor->es_global) ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <div class="sp-form-actions">
                <button type="submit" class="btn-guardar">
                    <i class="fas fa-save"></i>
                    <?= isset($sponsor) && $sponsor ? 'Guardar cambios' : 'Crear sponsor' ?>
                </button>
                <a href="<?= base_url('admin/Sponsors/index') ?>" class="btn-cancelar">
                    Cancelar
                </a>
            </div>

        </form>
    </div>
</div>

<script>
// Preview del logo antes de subir
document.getElementById('logo-input').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('logo-preview-live');
        preview.src = e.target.result;
        preview.style.display = 'block';
        const placeholder = document.getElementById('placeholder-icon');
        if (placeholder) placeholder.style.display = 'none';
    };
    reader.readAsDataURL(file);
});
</script>
