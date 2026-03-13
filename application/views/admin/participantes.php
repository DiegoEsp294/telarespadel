<style>
.part-toolbar { display:flex; gap:10px; align-items:center; margin-bottom:20px; flex-wrap:wrap; }
.part-search { display:flex; gap:8px; align-items:center; flex:1; min-width:200px; }
.part-search input { flex:1; padding:9px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px; }
.participantes-table { width:100%; border-collapse:collapse; font-size:14px; }
.participantes-table th { background:#f5f5f5; padding:10px 12px; text-align:left; border-bottom:2px solid #ddd; white-space:nowrap; }
.participantes-table td { padding:10px 12px; border-bottom:1px solid #eee; vertical-align:middle; }
.participantes-table tr:hover td { background:#fafafa; }
.tag-dni { display:inline-block; background:#e8f4fd; color:#2471a3; border-radius:4px; padding:2px 8px; font-size:12px; font-weight:600; }
.tag-cat { display:inline-block; background:#eafaf1; color:#1e8449; border-radius:4px; padding:2px 8px; font-size:12px; }
.btn-sm { padding:5px 12px; border-radius:5px; border:none; cursor:pointer; font-size:13px; }
.btn-edit { background:#3498db; color:#fff; }
.btn-del  { background:#e74c3c; color:#fff; }
.no-results { text-align:center; color:#aaa; padding:30px 0; font-size:14px; }
.table-count { color:#aaa; font-size:12px; margin-top:8px; }
/* Modal */
.modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1000; align-items:center; justify-content:center; }
.modal.open { display:flex; }
.modal-content { background:#fff; border-radius:10px; padding:28px; width:100%; max-width:440px; }
.modal-content h3 { margin:0 0 18px; }
.modal-content label { display:block; font-size:13px; font-weight:600; margin:10px 0 4px; color:#444; }
.modal-content input { width:100%; padding:8px 10px; border:1px solid #ddd; border-radius:6px; font-size:14px; box-sizing:border-box; }
.modal-content input:focus { outline:none; border-color:#FF6600; }
.modal-actions { display:flex; gap:10px; margin-top:20px; }
.modal-actions .btn-save   { flex:1; padding:10px; background:#FF6600; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:14px; font-weight:600; }
.modal-actions .btn-cancel { flex:1; padding:10px; background:#eee; color:#555; border:none; border-radius:6px; cursor:pointer; font-size:14px; }
.modal-actions .btn-save:disabled { background:#ccc; cursor:not-allowed; }
.toast { position:fixed; bottom:24px; right:24px; background:#2ecc71; color:#fff; padding:12px 20px; border-radius:8px; font-size:14px; z-index:2000; opacity:0; transition:opacity .3s; pointer-events:none; }
.toast.error { background:#e74c3c; }
.toast.show { opacity:1; }
</style>

<div class="admin-wrapper">
<div class="container">
<div class="admin-card">

<div class="admin-header-actions" style="margin-bottom:16px;">
    <a href="<?= base_url('admin/torneos/torneos') ?>" class="btn-back">← Volver a Torneos</a>
</div>

<h2>Participantes</h2>
<p style="color:#888;font-size:14px;margin-bottom:20px;">Registro global de jugadores. Buscalos y asignalos a cualquier torneo sin cargar sus datos de nuevo.</p>

<div class="part-toolbar">
    <div class="part-search">
        <input type="text" id="inputBuscar" placeholder="Buscar por nombre, apellido o DNI..." autocomplete="off">
    </div>
    <button class="btn-create" onclick="abrirModalNuevo()">+ Nuevo participante</button>
</div>

<table class="participantes-table" id="tablaParticipantes">
    <thead>
        <tr>
            <th>Apellido y Nombre</th>
            <th>DNI</th>
            <th>Categoría</th>
            <th>Teléfono</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="tbodyParticipantes"></tbody>
</table>
<p class="table-count" id="tableCount"></p>

</div>
</div>
</div>

<!-- Modal Nuevo / Editar (uno solo, reutilizable) -->
<div id="modalForm" class="modal">
    <div class="modal-content">
        <h3 id="modalTitulo">Nuevo participante</h3>
        <form id="formParticipante" novalidate>
            <input type="hidden" id="editId" value="">

            <label>Nombre *</label>
            <input type="text" id="fNombre" placeholder="Nombre" required>

            <label>Apellido *</label>
            <input type="text" id="fApellido" placeholder="Apellido" required>

            <label>DNI</label>
            <input type="text" id="fDni" placeholder="Ej: 30123456">

            <label>Categoría</label>
            <select id="fCategoria" style="width:100%;padding:8px 10px;border:1px solid #ddd;border-radius:6px;font-size:14px;box-sizing:border-box;">
                <option value="">— Sin categoría —</option>
                <?php foreach($categorias as $cat): ?>
                    <option value="<?= htmlspecialchars($cat->nombre) ?>">
                        <?= htmlspecialchars($cat->nombre) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Teléfono</label>
            <input type="text" id="fTelefono" placeholder="Ej: 3854123456">

            <div class="modal-actions">
                <button type="submit" class="btn-save" id="btnGuardar">Guardar</button>
                <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div class="toast" id="toast"></div>

<script>
const URL_LISTAR    = "<?= base_url('admin/Participantes/listar') ?>";
const URL_GUARDAR   = "<?= base_url('admin/Participantes/guardar') ?>";
const URL_ACTUALIZAR = "<?= base_url('admin/Participantes/actualizar') ?>/";
const URL_ELIMINAR  = "<?= base_url('admin/Participantes/eliminar') ?>/";

/* ===== DATOS INICIALES ===== */
let todosLosParticipantes = <?= json_encode($participantes) ?>;
renderTabla(todosLosParticipantes);

/* ===== BÚSQUEDA LOCAL + AJAX con debounce ===== */
let timerBuscar;
document.getElementById('inputBuscar').addEventListener('input', function() {
    clearTimeout(timerBuscar);
    const q = this.value.trim();

    if (q === '') {
        renderTabla(todosLosParticipantes);
        return;
    }

    timerBuscar = setTimeout(() => {
        fetch(URL_LISTAR + '?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => renderTabla(data));
    }, 250);
});

/* ===== RENDER TABLA ===== */
function renderTabla(lista) {
    const tbody = document.getElementById('tbodyParticipantes');
    const count = document.getElementById('tableCount');

    if (!lista || lista.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="no-results">No se encontraron participantes.</td></tr>';
        count.textContent = '';
        return;
    }

    tbody.innerHTML = lista.map(p => `
        <tr id="row-${p.id}">
            <td><strong>${esc(p.apellido)}, ${esc(p.nombre)}</strong></td>
            <td>${p.dni  ? `<span class="tag-dni">${esc(p.dni)}</span>`      : '<span style="color:#ccc">—</span>'}</td>
            <td>${p.categoria ? `<span class="tag-cat">${esc(p.categoria)}</span>` : '<span style="color:#ccc">—</span>'}</td>
            <td style="color:#666">${esc(p.telefono || '—')}</td>
            <td style="white-space:nowrap">
                <button class="btn-sm btn-edit" onclick="abrirModalEditar(${p.id},'${esc2(p.nombre)}','${esc2(p.apellido)}','${esc2(p.dni||'')}','${esc2(p.telefono||'')}','${esc2(p.categoria||'')}')">Editar</button>
                <button class="btn-sm btn-del"  onclick="eliminar(${p.id}, '${esc2(p.apellido)} ${esc2(p.nombre)}')">Eliminar</button>
            </td>
        </tr>
    `).join('');

    count.textContent = `Total: ${lista.length} participante(s)`;
}

function esc(t)  { return String(t ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function esc2(t) { return String(t ?? '').replace(/'/g,"\\'").replace(/"/g,'&quot;'); }

/* ===== MODAL ===== */
function abrirModalNuevo() {
    document.getElementById('modalTitulo').textContent = 'Nuevo participante';
    document.getElementById('editId').value = '';
    limpiarForm();
    document.getElementById('modalForm').classList.add('open');
    document.getElementById('fNombre').focus();
}

function abrirModalEditar(id, nombre, apellido, dni, telefono, categoria) {
    document.getElementById('modalTitulo').textContent = 'Editar participante';
    document.getElementById('editId').value    = id;
    document.getElementById('fNombre').value   = nombre;
    document.getElementById('fApellido').value = apellido;
    document.getElementById('fDni').value      = dni;
    document.getElementById('fTelefono').value = telefono;
    document.getElementById('fCategoria').value= categoria;
    document.getElementById('modalForm').classList.add('open');
    document.getElementById('fNombre').focus();
}

function cerrarModal() {
    document.getElementById('modalForm').classList.remove('open');
}

function limpiarForm() {
    ['fNombre','fApellido','fDni','fTelefono','fCategoria'].forEach(id => {
        document.getElementById(id).value = '';
    });
}

window.addEventListener('click', e => {
    if (e.target === document.getElementById('modalForm')) cerrarModal();
});

/* ===== SUBMIT (crear o editar) ===== */
document.getElementById('formParticipante').addEventListener('submit', function(e) {
    e.preventDefault();

    const nombre   = document.getElementById('fNombre').value.trim();
    const apellido = document.getElementById('fApellido').value.trim();

    if (!nombre || !apellido) {
        showToast('Nombre y Apellido son obligatorios.', true);
        return;
    }

    const id = document.getElementById('editId').value;
    const formData = new FormData();
    formData.append('nombre',    nombre);
    formData.append('apellido',  apellido);
    formData.append('dni',       document.getElementById('fDni').value.trim());
    formData.append('telefono',  document.getElementById('fTelefono').value.trim());
    formData.append('categoria', document.getElementById('fCategoria').value.trim());

    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    const url = id ? URL_ACTUALIZAR + id : URL_GUARDAR;

    fetch(url, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(resp => {
            if (!resp.ok) {
                showToast('Error al guardar.', true);
                return;
            }
            cerrarModal();
            showToast(id ? 'Participante actualizado.' : 'Participante creado.');
            recargarLista();
        })
        .catch(() => showToast('Error de conexión.', true))
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Guardar';
        });
});

/* ===== ELIMINAR ===== */
function eliminar(id, nombre) {
    if (!confirm('¿Eliminar a ' + nombre + '?\n(Solo se puede si no está inscripto en ningún torneo)')) return;

    fetch(URL_ELIMINAR + id, { method: 'POST' })
        .then(r => r.json())
        .then(resp => {
            if (!resp.ok) {
                showToast(resp.error || 'No se pudo eliminar.', true);
                return;
            }
            const row = document.getElementById('row-' + id);
            if (row) row.remove();
            todosLosParticipantes = todosLosParticipantes.filter(p => p.id != id);
            showToast('Participante eliminado.');
            const tbody = document.getElementById('tbodyParticipantes');
            if (!tbody.querySelector('tr[id]')) renderTabla([]);
            actualizarCount();
        })
        .catch(() => showToast('Error de conexión.', true));
}

function actualizarCount() {
    const filas = document.querySelectorAll('#tbodyParticipantes tr[id]').length;
    document.getElementById('tableCount').textContent = filas ? `Total: ${filas} participante(s)` : '';
}

/* ===== RECARGA LISTA ===== */
function recargarLista() {
    const q = document.getElementById('inputBuscar').value.trim();
    const url = q ? URL_LISTAR + '?q=' + encodeURIComponent(q) : URL_LISTAR;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            todosLosParticipantes = q ? todosLosParticipantes : data; // actualiza base si no hay filtro
            renderTabla(data);
        });
}

/* ===== TOAST ===== */
function showToast(msg, error = false) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast' + (error ? ' error' : '') + ' show';
    setTimeout(() => t.classList.remove('show'), 3000);
}
</script>
