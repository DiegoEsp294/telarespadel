<style>
/* ===== LAYOUT ===== */
.cat-page { padding:24px 0 48px; }
.cat-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:24px; }
.cat-header h2 { margin:0; font-size:22px; color:#1a1a2e; }

/* ===== FILTROS ===== */
.cat-filters { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:20px; padding:14px 16px; background:#f7f8fa; border-radius:8px; border:1px solid #e8eaed; }
.cat-filters input,
.cat-filters select { padding:8px 12px; border:1px solid #ddd; border-radius:6px; font-size:13px; min-width:180px; }
.cat-filters input:focus,
.cat-filters select:focus { outline:none; border-color:#FF6600; }
.cat-count-badge { margin-left:auto; font-size:13px; color:#888; white-space:nowrap; }

/* ===== TABS ===== */
.cat-tabs-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; margin-bottom:0; }
.cat-tabs { display:flex; gap:4px; border-bottom:2px solid #e8eaed; min-width:max-content; }
.cat-tab { padding:9px 16px; font-size:13px; font-weight:600; color:#666; cursor:pointer; border:none; background:none; border-bottom:2px solid transparent; margin-bottom:-2px; white-space:nowrap; border-radius:6px 6px 0 0; transition:all .15s; }
.cat-tab:hover { background:#f5f5f5; color:#333; }
.cat-tab.active { color:#FF6600; border-bottom-color:#FF6600; background:#fff8f5; }
.cat-tab .tab-count { display:inline-block; background:#eee; color:#666; border-radius:10px; padding:1px 7px; font-size:11px; margin-left:5px; }
.cat-tab.active .tab-count { background:#ffe0cc; color:#c74400; }

/* ===== PANEL ===== */
.cat-panel { display:none; background:#fff; border:1px solid #e8eaed; border-top:none; border-radius:0 0 10px 10px; }
.cat-panel.active { display:block; }
.cat-panel-header { padding:14px 18px; background:#f7f8fa; border-bottom:1px solid #eee; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; }
.cat-panel-title { font-size:15px; font-weight:700; color:#1a1a2e; margin:0; }
.cat-panel-sub { font-size:12px; color:#999; }

/* ===== TABLA ===== */
.cat-table { width:100%; border-collapse:collapse; font-size:14px; }
.cat-table th { background:#f0f2f5; padding:9px 14px; text-align:left; font-size:12px; color:#555; font-weight:700; text-transform:uppercase; letter-spacing:.4px; border-bottom:2px solid #e0e0e0; }
.cat-table th:first-child { width:48px; text-align:center; }
.cat-table td { padding:9px 14px; border-bottom:1px solid #f0f0f0; vertical-align:middle; }
.cat-table td:first-child { text-align:center; color:#bbb; font-size:13px; font-weight:600; }
.cat-table tr:last-child td { border-bottom:none; }
.cat-table tr:hover td { background:#fafafa; }
.cat-table .nombre-cell { font-weight:600; color:#1a1a2e; }
.tag-lugar { display:inline-block; background:#fef9e7; color:#92400e; border-radius:4px; padding:2px 8px; font-size:12px; }
.tag-dni   { display:inline-block; background:#e8f4fd; color:#1565c0; border-radius:4px; padding:2px 7px; font-size:12px; font-weight:600; }
.no-data { text-align:center; color:#bbb; padding:28px; font-size:14px; }

/* ===== SIN CATEGORIZAR ===== */
.cat-sin { margin-top:28px; }
.cat-sin-header { display:flex; align-items:center; gap:10px; padding:12px 18px; background:#fff8e1; border:1px solid #ffe082; border-radius:8px 8px 0 0; }
.cat-sin-header span { font-size:14px; font-weight:700; color:#7c5200; }
.cat-sin-count { background:#ffe082; color:#7c5200; border-radius:10px; padding:1px 9px; font-size:12px; font-weight:700; }
.cat-sin .cat-table { border:1px solid #ffe082; border-top:none; border-radius:0 0 8px 8px; }

/* ===== RESPONSIVE ===== */
@media (max-width:600px) {
    .cat-table th:first-child { width:32px; }
    .cat-table td, .cat-table th { padding:8px 10px; }
}

/* fila oculta por filtros */
tr.cat-hidden { display:none; }

/* botón descargar */
.btn-descargar { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; background:#FF6600; color:#fff; border:none; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; margin-left:auto; transition:background .15s; }
.btn-descargar:hover { background:#e55a00; }
.btn-descargar:disabled { background:#ccc; cursor:not-allowed; }
</style>

<div class="admin-wrapper">
<div class="container cat-page">

<div class="admin-header-actions" style="margin-bottom:16px;">
    <a href="<?= base_url('admin/torneos/torneos') ?>" class="btn-back">← Volver a Torneos</a>
</div>

<div class="cat-header">
    <h2><i class="fas fa-layer-group" style="color:#FF6600;margin-right:8px;"></i> Categorización de Participantes</h2>
    <span style="font-size:13px;color:#999;"><?= array_sum(array_map('count', $por_categoria)) + count($sin_categoria) ?> jugadores en total</span>
</div>

<!-- Filtros -->
<div class="cat-filters">
    <input type="text" id="filtroBuscar" placeholder="Buscar jugador..." autocomplete="off">
    <select id="filtroLugar">
        <option value="">Todas las localidades</option>
        <?php foreach($lugares as $l): ?>
            <option value="<?= htmlspecialchars($l) ?>"><?= htmlspecialchars($l) ?></option>
        <?php endforeach; ?>
    </select>
    <span class="cat-count-badge" id="filtroCount"></span>
    <button onclick="descargarPDF()" class="btn-descargar" id="btnDescargar">
        <i class="fas fa-download"></i> Descargar PDF
    </button>
</div>

<!-- Tabs -->
<div class="cat-tabs-wrap">
    <div class="cat-tabs" id="catTabs">
        <?php $first = true; foreach($por_categoria as $cat => $jugadores): ?>
        <button class="cat-tab <?= $first ? 'active' : '' ?>"
                data-target="panel-<?= md5($cat) ?>"
                onclick="activarTab(this)">
            <?= htmlspecialchars($cat) ?>
            <span class="tab-count"><?= count($jugadores) ?></span>
        </button>
        <?php $first = false; endforeach; ?>
        <?php if (!empty($sin_categoria)): ?>
        <button class="cat-tab" data-target="panel-sin" onclick="activarTab(this)">
            Sin categoría
            <span class="tab-count"><?= count($sin_categoria) ?></span>
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- Paneles por categoría -->
<?php $first = true; foreach($por_categoria as $cat => $jugadores): ?>
<div class="cat-panel <?= $first ? 'active' : '' ?>" id="panel-<?= md5($cat) ?>">
    <div class="cat-panel-header">
        <p class="cat-panel-title"><?= htmlspecialchars($cat) ?></p>
        <span class="cat-panel-sub" id="sub-<?= md5($cat) ?>"><?= count($jugadores) ?> jugadores</span>
    </div>
    <table class="cat-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Apellido y Nombre</th>
                <th>Localidad</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($jugadores as $i => $p): ?>
            <tr data-nombre="<?= strtolower($p->apellido.' '.$p->nombre) ?>"
                data-lugar="<?= strtolower($p->lugar ?? '') ?>">
                <td><?= $i + 1 ?></td>
                <td class="nombre-cell"><?= htmlspecialchars($p->apellido) ?>, <?= htmlspecialchars($p->nombre) ?></td>
                <td><?= !empty($p->lugar) ? '<span class="tag-lugar">'.htmlspecialchars($p->lugar).'</span>' : '<span style="color:#ccc">—</span>' ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="no-data" style="display:none"><td colspan="3">No hay resultados para este filtro.</td></tr>
        </tbody>
    </table>
</div>
<?php $first = false; endforeach; ?>

<!-- Panel Sin categoría -->
<?php if (!empty($sin_categoria)): ?>
<div class="cat-panel" id="panel-sin">
    <div class="cat-panel-header" style="background:#fff8e1;border-color:#ffe082;">
        <p class="cat-panel-title" style="color:#7c5200;">Sin categoría</p>
        <span class="cat-panel-sub" id="sub-sin"><?= count($sin_categoria) ?> jugadores</span>
    </div>
    <table class="cat-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Apellido y Nombre</th>
                <th>Localidad</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($sin_categoria as $i => $p): ?>
            <tr data-nombre="<?= strtolower($p->apellido.' '.$p->nombre) ?>"
                data-lugar="<?= strtolower($p->lugar ?? '') ?>">
                <td><?= $i + 1 ?></td>
                <td class="nombre-cell"><?= htmlspecialchars($p->apellido) ?>, <?= htmlspecialchars($p->nombre) ?></td>
                <td><?= !empty($p->lugar) ? '<span class="tag-lugar">'.htmlspecialchars($p->lugar).'</span>' : '<span style="color:#ccc">—</span>' ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="no-data" style="display:none"><td colspan="3">No hay resultados para este filtro.</td></tr>
        </tbody>
    </table>
</div>
<?php endif; ?>

</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<script>
/* ===== TABS ===== */
function activarTab(btn) {
    document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.cat-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(btn.dataset.target).classList.add('active');
    aplicarFiltros();
}

/* ===== FILTROS ===== */
document.getElementById('filtroBuscar').addEventListener('input', aplicarFiltros);
document.getElementById('filtroLugar').addEventListener('change', aplicarFiltros);

function aplicarFiltros() {
    const q      = document.getElementById('filtroBuscar').value.trim().toLowerCase();
    const lugar  = document.getElementById('filtroLugar').value.toLowerCase();
    const panel  = document.querySelector('.cat-panel.active');
    if (!panel) return;

    const filas  = panel.querySelectorAll('tbody tr:not(.no-data)');
    let visibles = 0;

    filas.forEach(tr => {
        const nombre  = tr.dataset.nombre  || '';
        const trLugar = tr.dataset.lugar   || '';
        const matchQ  = !q     || nombre.includes(q);
        const matchL  = !lugar || trLugar === lugar;
        if (matchQ && matchL) { tr.classList.remove('cat-hidden'); visibles++; }
        else                  { tr.classList.add('cat-hidden'); }
    });

    // mostrar fila "sin resultados"
    const noData = panel.querySelector('tr.no-data');
    if (noData) noData.style.display = visibles === 0 ? '' : 'none';

    // contador
    const total = filas.length;
    document.getElementById('filtroCount').textContent =
        (q || lugar) ? `${visibles} de ${total} jugadores` : '';
}

/* ===== DESCARGA PDF ===== */
function descargarPDF() {
    const { jsPDF } = window.jspdf;
    const btn = document.getElementById('btnDescargar');

    // Nombre de la categoría activa
    const tabActivo = document.querySelector('.cat-tab.active');
    const catNombre = tabActivo
        ? Array.from(tabActivo.childNodes)
              .filter(n => n.nodeType === Node.TEXT_NODE)
              .map(n => n.textContent.trim())
              .join('').trim()
        : 'Categorización';

    // Filas visibles del panel activo
    const panel = document.querySelector('.cat-panel.active');
    const filas = Array.from(panel.querySelectorAll('tbody tr:not(.no-data):not(.cat-hidden)'));

    if (filas.length === 0) { alert('No hay jugadores para descargar.'); return; }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';

    const rows = filas.map((tr, i) => {
        const celdas = tr.querySelectorAll('td');
        const nombre = celdas[1] ? celdas[1].textContent.trim() : '';
        const lugar  = celdas[2] ? celdas[2].textContent.replace('—','').trim() : '';
        return [i + 1, nombre, lugar || '—'];
    });

    const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });

    // Encabezado
    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.setTextColor(255, 102, 0);
    doc.text('Telares Padel', 14, 16);

    doc.setFontSize(13);
    doc.setTextColor(30, 30, 30);
    doc.text('Categorización: ' + catNombre, 14, 24);

    doc.setFontSize(9);
    doc.setFont('helvetica', 'normal');
    doc.setTextColor(150, 150, 150);
    const filtroLugar = document.getElementById('filtroLugar').value;
    const sublinea = filtroLugar ? 'Localidad: ' + filtroLugar : rows.length + ' jugadores';
    doc.text(sublinea, 14, 30);

    // Tabla
    doc.autoTable({
        startY: 34,
        head: [['#', 'Apellido y Nombre', 'Localidad']],
        body: rows,
        styles: { fontSize: 10, cellPadding: 3 },
        headStyles: { fillColor: [255, 102, 0], textColor: 255, fontStyle: 'bold' },
        alternateRowStyles: { fillColor: [250, 250, 250] },
        columnStyles: { 0: { cellWidth: 12, halign: 'center' }, 1: { cellWidth: 110 }, 2: { cellWidth: 55 } },
        margin: { left: 14, right: 14 },
    });

    const fecha = new Date().toLocaleDateString('es-AR').replace(/\//g, '-');
    const filename = 'categorizacion_' + catNombre.replace(/\s+/g, '_') + '_' + fecha + '.pdf';
    doc.save(filename);

    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-download"></i> Descargar PDF';
}
</script>
