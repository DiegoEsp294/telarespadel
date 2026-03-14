<style>
/* ===== PAGE ===== */
.tl-page { padding: 24px 0 48px; }
.tl-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:28px; }
.tl-header h2 { margin:0; font-size:22px; color:#1a1a2e; }
.tl-actions { display:flex; gap:10px; flex-wrap:wrap; }

/* ===== BTN ===== */
.tl-btn { display:inline-flex; align-items:center; gap:7px; padding:9px 18px; border-radius:7px; font-size:13px; font-weight:600; text-decoration:none; border:none; cursor:pointer; transition: all .18s; }
.tl-btn-primary { background:#FF6600; color:#fff; }
.tl-btn-primary:hover { background:#e55a00; color:#fff; }
.tl-btn-secondary { background:#f0f2f5; color:#444; }
.tl-btn-secondary:hover { background:#e2e5ea; color:#222; }

/* ===== GRID DE CARDS ===== */
.tl-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(310px, 1fr)); gap:18px; }

/* ===== CARD ===== */
.tl-card { background:#fff; border-radius:12px; border:1px solid #e8eaed; overflow:hidden; display:flex; flex-direction:column; transition: box-shadow .2s, transform .2s; }
.tl-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.10); transform: translateY(-2px); }

/* badges en el header de la card (sin imagen) */
.tl-card-badges { padding:14px 18px 0; display:flex; align-items:center; justify-content:space-between; gap:8px; }
.tl-badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; letter-spacing:.4px; text-transform:uppercase; }
.tl-badge-proxima    { background:#fff3cd; color:#856404; }
.tl-badge-en_curso   { background:#d1e7dd; color:#0a5931; }
.tl-badge-finalizado { background:#dee2e6; color:#555; }
.tl-vis { display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:20px; font-size:11px; font-weight:600; }
.tl-vis-on  { background:rgba(0,180,80,.10); color:#0a7a38; border:1px solid rgba(0,180,80,.25); }
.tl-vis-off { background:rgba(0,0,0,.07); color:#999; border:1px solid rgba(0,0,0,.12); }

/* ===== MODAL ELIMINAR ===== */
.tl-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:1000; align-items:center; justify-content:center; padding:16px; }
.tl-modal-overlay.open { display:flex; }
.tl-modal { background:#fff; border-radius:14px; padding:28px 28px 22px; width:100%; max-width:400px; text-align:center; }
.tl-modal-icon { font-size:36px; color:#e53e3e; margin-bottom:12px; }
.tl-modal h3 { margin:0 0 8px; font-size:18px; color:#1a1a2e; }
.tl-modal p  { margin:0 0 22px; font-size:14px; color:#666; line-height:1.5; }
.tl-modal-nombre { font-weight:700; color:#1a1a2e; }
.tl-modal-btns { display:flex; gap:10px; }
.tl-modal-btns .btn-confirmar { flex:1; padding:10px; background:#e53e3e; color:#fff; border:none; border-radius:7px; font-size:14px; font-weight:600; cursor:pointer; transition:background .15s; }
.tl-modal-btns .btn-confirmar:hover { background:#c53030; }
.tl-modal-btns .btn-cancelar  { flex:1; padding:10px; background:#f0f2f5; color:#444; border:none; border-radius:7px; font-size:14px; font-weight:600; cursor:pointer; }

.tl-card-body { padding:16px 18px; flex:1; display:flex; flex-direction:column; gap:10px; }
.tl-card-nombre { font-size:16px; font-weight:700; color:#1a1a2e; line-height:1.3; margin:0; }
.tl-card-meta { display:flex; flex-direction:column; gap:5px; }
.tl-meta-row { display:flex; align-items:center; gap:7px; font-size:12px; color:#666; }
.tl-meta-row i { width:13px; color:#aaa; flex-shrink:0; }
.tl-cat-tag { display:inline-block; background:#eef2ff; color:#3730a3; border-radius:4px; padding:2px 8px; font-size:11px; font-weight:600; }
.tl-precio-tag { display:inline-block; background:#fef3c7; color:#92400e; border-radius:4px; padding:2px 8px; font-size:11px; font-weight:600; }

/* ===== CARD FOOTER (acciones) ===== */
.tl-card-footer { padding:12px 18px; border-top:1px solid #f0f0f0; display:flex; gap:8px; flex-wrap:wrap; background:#fafafa; }
.tl-act { display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none; border:none; cursor:pointer; transition: background .15s; }
.tl-act-ver      { background:#e8f4fd; color:#1565c0; }
.tl-act-ver:hover{ background:#d0eaf9; color:#1565c0; }
.tl-act-edit     { background:#fff3e0; color:#e65100; }
.tl-act-edit:hover{ background:#ffe0b2; color:#e65100; }
.tl-act-fix      { background:#e8f5e9; color:#1b5e20; }
.tl-act-fix:hover{ background:#c8e6c9; color:#1b5e20; }
.tl-act-del      { background:#fdecea; color:#c62828; margin-left:auto; }
.tl-act-del:hover{ background:#f9d3d0; color:#c62828; }

/* ===== EMPTY STATE ===== */
.tl-empty { text-align:center; padding:60px 20px; color:#aaa; }
.tl-empty i { font-size:48px; margin-bottom:14px; display:block; }
.tl-empty p { font-size:15px; margin:0 0 18px; }
</style>

<section class="torneos-section tl-page">
<div class="container container-admin">

    <div class="tl-header">
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <h2><i class="fas fa-trophy" style="color:#FF6600;margin-right:8px;"></i> Mis Torneos</h2>
            <a href="<?= base_url('admin/torneos/crear') ?>" class="tl-btn tl-btn-primary">
                <i class="fas fa-plus"></i> Crear Torneo
            </a>
        </div>
        <div class="tl-actions">
            <a href="<?= base_url('admin/Participantes/index') ?>" class="tl-btn tl-btn-secondary">
                <i class="fas fa-users"></i> Participantes
            </a>
            <a href="<?= base_url('admin/Participantes/categorizacion') ?>" class="tl-btn tl-btn-secondary">
                <i class="fas fa-layer-group"></i> Categorización
            </a>
            <a href="<?= base_url('admin/Push/index') ?>" class="tl-btn tl-btn-secondary">
                <i class="fas fa-bell"></i> Notificaciones
            </a>
        </div>
    </div>

    <?php if (empty($torneos)): ?>
        <div class="tl-empty">
            <i class="fas fa-trophy"></i>
            <p>Todavía no creaste ningún torneo.</p>
            <a href="<?= base_url('admin/torneos/crear') ?>" class="tl-btn tl-btn-primary">
                <i class="fas fa-plus"></i> Crear mi primer torneo
            </a>
        </div>
    <?php else: ?>
    <div class="tl-grid">

    <?php foreach($torneos as $t):
        $estados = ['proxima' => 'Próximo', 'en_curso' => 'En curso', 'finalizado' => 'Finalizado'];
        $estado_label = $estados[$t->estado] ?? ucfirst($t->estado ?? '—');
        $badge_class  = 'tl-badge-' . ($t->estado ?? 'proxima');
        $fi = (!empty($t->fecha_inicio) && $t->fecha_inicio !== '0000-00-00') ? date('d/m/Y', strtotime($t->fecha_inicio)) : null;
        $ff = (!empty($t->fecha_fin)    && $t->fecha_fin    !== '0000-00-00') ? date('d/m/Y', strtotime($t->fecha_fin))    : null;
    ?>
    <div class="tl-card">

        <div class="tl-card-badges">
            <span class="tl-badge <?= $badge_class ?>"><?= $estado_label ?></span>
            <?php $es_visible = (bool)$t->visible; ?>
            <span class="tl-vis <?= $es_visible ? 'tl-vis-on' : 'tl-vis-off' ?>">
                <i class="fas <?= $es_visible ? 'fa-eye' : 'fa-eye-slash' ?>"></i>
                <?= $es_visible ? 'Visible' : 'Oculto' ?>
            </span>
        </div>

        <div class="tl-card-body">
            <p class="tl-card-nombre"><?= htmlspecialchars($t->nombre) ?></p>

            <div class="tl-card-meta">
                <?php if ($fi || $ff): ?>
                <div class="tl-meta-row">
                    <i class="fas fa-calendar-alt"></i>
                    <span><?= $fi ?? '?' ?> — <?= $ff ?? '?' ?></span>
                </div>
                <?php endif; ?>

                <?php if (!empty($t->categoria)): ?>
                <div class="tl-meta-row">
                    <i class="fas fa-tag"></i>
                    <span class="tl-cat-tag"><?= htmlspecialchars($t->categoria) ?></span>
                </div>
                <?php endif; ?>

                <?php if (!empty($t->precio_inscripcion)): ?>
                <div class="tl-meta-row">
                    <i class="fas fa-dollar-sign"></i>
                    <span class="tl-precio-tag">$<?= number_format($t->precio_inscripcion, 0, ',', '.') ?></span>
                </div>
                <?php endif; ?>

                <?php if (!empty($t->nombre_organizador)): ?>
                <div class="tl-meta-row">
                    <i class="fas fa-user"></i>
                    <span><?= htmlspecialchars($t->nombre_organizador) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="tl-card-footer">
            <a href="<?= base_url('admin/torneos/ver/'.$t->id) ?>" class="tl-act tl-act-ver">
                <i class="fas fa-eye"></i> Ver
            </a>
            <a href="<?= base_url('admin/torneos/editar/'.$t->id) ?>" class="tl-act tl-act-edit">
                <i class="fas fa-pen"></i> Editar
            </a>
            <a href="<?= base_url('admin/torneos/fixture/'.$t->id) ?>" class="tl-act tl-act-fix">
                <i class="fas fa-sitemap"></i> Fixture
            </a>
            <button class="tl-act tl-act-del"
                    onclick="confirmarEliminar(<?= $t->id ?>, '<?= addslashes(htmlspecialchars($t->nombre)) ?>')">
                <i class="fas fa-trash"></i>
            </button>
        </div>

    </div>
    <?php endforeach; ?>

    </div>
    <?php endif; ?>

</div>
</section>

<!-- Modal confirmar eliminación -->
<div class="tl-modal-overlay" id="modalEliminar">
    <div class="tl-modal">
        <div class="tl-modal-icon"><i class="fas fa-triangle-exclamation"></i></div>
        <h3>¿Eliminar torneo?</h3>
        <p>Vas a eliminar <span class="tl-modal-nombre" id="modalNombre"></span>.<br>Esta acción no se puede deshacer.</p>
        <div class="tl-modal-btns">
            <button class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
            <button class="btn-confirmar" id="btnConfirmar">Sí, eliminar</button>
        </div>
    </div>
</div>

<script>
function confirmarEliminar(id, nombre) {
    document.getElementById('modalNombre').textContent = '«' + nombre + '»';
    document.getElementById('btnConfirmar').onclick = function() {
        window.location.href = '<?= base_url('admin/torneos/eliminar/') ?>' + id;
    };
    document.getElementById('modalEliminar').classList.add('open');
}
function cerrarModal() {
    document.getElementById('modalEliminar').classList.remove('open');
}
document.getElementById('modalEliminar').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
</script>
