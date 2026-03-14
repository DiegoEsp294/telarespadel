<style>
.push-page { padding:24px 0 48px; }
.push-card { background:#fff; border-radius:12px; border:1px solid #e8eaed; padding:28px; max-width:560px; }
.push-stats { display:flex; align-items:center; gap:12px; background:#fff8f5; border:1px solid #ffe0cc; border-radius:8px; padding:14px 18px; margin-bottom:24px; }
.push-stats i { font-size:22px; color:#FF6600; }
.push-stats strong { font-size:20px; color:#FF6600; }
.push-stats span { font-size:13px; color:#888; }
.push-form label { display:block; font-size:13px; font-weight:600; color:#444; margin:14px 0 5px; }
.push-form input,
.push-form textarea,
.push-form select { width:100%; padding:9px 12px; border:1px solid #ddd; border-radius:7px; font-size:14px; box-sizing:border-box; font-family:inherit; }
.push-form input:focus,
.push-form textarea:focus,
.push-form select:focus { outline:none; border-color:#FF6600; }
.push-form textarea { resize:vertical; min-height:80px; }
.push-form .hint { font-size:12px; color:#aaa; margin-top:3px; }
.push-btn { display:inline-flex; align-items:center; gap:8px; padding:11px 22px; background:#FF6600; color:#fff; border:none; border-radius:7px; font-size:14px; font-weight:600; cursor:pointer; margin-top:20px; transition:background .15s; }
.push-btn:hover { background:#e55a00; }
.push-btn:disabled { background:#ccc; cursor:not-allowed; }
.push-alert { padding:12px 16px; border-radius:7px; font-size:14px; margin-bottom:16px; }
.push-alert.ok  { background:#d1e7dd; color:#0a5931; border:1px solid #a3cfbb; }
.push-alert.err { background:#fdecea; color:#c62828; border:1px solid #f5c2c7; }
.push-preview { background:#1a1a2e; border-radius:10px; padding:14px 16px; margin-top:20px; }
.push-preview-label { font-size:11px; color:rgba(255,255,255,.4); text-transform:uppercase; letter-spacing:1px; margin-bottom:10px; }
.push-preview-notif { background:#fff; border-radius:8px; padding:12px 14px; display:flex; gap:10px; align-items:flex-start; }
.push-preview-notif img { width:36px; height:36px; border-radius:8px; object-fit:cover; }
.push-preview-notif-body strong { font-size:13px; display:block; color:#1a1a2e; }
.push-preview-notif-body span   { font-size:12px; color:#666; }
</style>

<div class="admin-wrapper">
<div class="container push-page">

    <div class="admin-header-actions" style="margin-bottom:16px;">
        <a href="<?= base_url('admin/torneos/torneos') ?>" class="btn-back">← Volver a Torneos</a>
    </div>

    <h2 style="margin:0 0 20px;font-size:22px;color:#1a1a2e;">
        <i class="fas fa-bell" style="color:#FF6600;margin-right:8px;"></i> Notificaciones Push
    </h2>

    <?php if($this->session->flashdata('push_ok')): ?>
    <div class="push-alert ok"><i class="fas fa-check-circle"></i> <?= $this->session->flashdata('push_ok') ?></div>
    <?php endif; ?>
    <?php if($this->session->flashdata('push_err')): ?>
    <div class="push-alert err"><i class="fas fa-exclamation-circle"></i> <?= $this->session->flashdata('push_err') ?></div>
    <?php endif; ?>

    <div class="push-card">

        <div class="push-stats">
            <i class="fas fa-mobile-alt"></i>
            <div>
                <strong><?= $total_suscriptores ?></strong>
                <span> dispositivo<?= $total_suscriptores != 1 ? 's' : '' ?> suscripto<?= $total_suscriptores != 1 ? 's' : '' ?></span>
            </div>
        </div>

        <form class="push-form" method="post" action="<?= base_url('admin/Push/enviar') ?>" onsubmit="return validarForm()">

            <label>Título *</label>
            <input type="text" name="titulo" id="pTitulo" placeholder="Ej: Nuevo torneo disponible!" maxlength="60" required oninput="actualizarPreview()">

            <label>Mensaje *</label>
            <textarea name="cuerpo" id="pCuerpo" placeholder="Ej: Ya podés inscribirte al Torneo de Verano. ¡Anotate ahora!" maxlength="120" required oninput="actualizarPreview()"></textarea>
            <p class="hint">Máximo 120 caracteres.</p>

            <label>Destino (URL al tocar la notificación)</label>
            <select name="url" id="pUrl">
                <option value="<?= base_url() ?>">Inicio del sitio</option>
                <?php
                $this->load->model('Torneo_model');
                $torneos = $this->Torneo_model->obtener_por_usuario($this->session->userdata('usuario_id'));
                foreach ($torneos as $t): ?>
                <option value="<?= base_url('home/torneo/'.$t->id) ?>"><?= htmlspecialchars($t->nombre) ?></option>
                <?php endforeach; ?>
            </select>

            <!-- Preview -->
            <div class="push-preview">
                <div class="push-preview-label">Vista previa</div>
                <div class="push-preview-notif">
                    <img src="<?= base_url('logo_inicio.png') ?>" alt="logo">
                    <div class="push-preview-notif-body">
                        <strong id="prevTitulo">Telares Padel</strong>
                        <span id="prevCuerpo">El mensaje aparecerá aquí...</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="push-btn" id="btnEnviar">
                <i class="fas fa-paper-plane"></i> Enviar a todos los dispositivos
            </button>

        </form>
    </div>

</div>
</div>

<script>
function actualizarPreview() {
    const titulo = document.getElementById('pTitulo').value || 'Telares Padel';
    const cuerpo = document.getElementById('pCuerpo').value || 'El mensaje aparecerá aquí...';
    document.getElementById('prevTitulo').textContent = titulo;
    document.getElementById('prevCuerpo').textContent = cuerpo;
}
function validarForm() {
    const btn = document.getElementById('btnEnviar');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    return true;
}
</script>
