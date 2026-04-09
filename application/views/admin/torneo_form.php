<div class="admin-wrapper">
<div class="container">

<div class="admin-card">

<h2><?= isset($torneo) ? 'Editar Torneo' : 'Crear Torneo' ?></h2>

<div class="admin-header-actions">
    <a href="<?= base_url('admin/torneos/torneos') ?>" class="btn-back">
        ← Volver al listado
    </a>

    <?php if(isset($torneo)): ?>
        <a href="<?= site_url('admin/torneos/fixture/'.$torneo->id) ?>"
           class="btn-generar-fixture">
            ⚙ Configurar Fixture
        </a>
    <?php endif; ?>
</div>

<form method="post"
      enctype="multipart/form-data"
      action="<?= isset($torneo)
          ? base_url('admin/torneos/actualizar/'.$torneo->id)
          : base_url('admin/torneos/guardar') ?>"
      class="admin-form">

    <label>Nombre del torneo</label>
    <input type="text" name="nombre"
           placeholder="Ej: Torneo Aniversario 2026"
           value="<?= $torneo->nombre ?? '' ?>"
           required>

    <label>Fecha de inicio</label>
    <input type="date" name="fecha_inicio"
           value="<?= $torneo->fecha_inicio ?? '' ?>"
           required>

    <label>Fecha de fin</label>
    <input type="date" name="fecha_fin"
           value="<?= $torneo->fecha_fin ?? '' ?>">

    <label>Fecha de cierre de inscripciones</label>
    <input type="date" name="fecha_cierre_inscripcion"
           value="<?= $torneo->fecha_cierre_inscripcion ?? '' ?>">

    <label>Categorías <span style="font-weight:400;color:#888;">(Ej: 7ma Masculina, 8va Femenina)</span></label>
    <input type="text"
           name="categoria"
           placeholder="Ej: 7ma Masculina, 8va Femenina"
           value="<?= $torneo->categoria ?? '' ?>"
           required>

    <label>Nombre del organizador</label>
    <input type="text"
           name="organizador"
           placeholder="Ej: Club Telares Padel"
           value="<?= $torneo->nombre_organizador ?? '' ?>"
           required>

    <label>Teléfono del organizador <span style="font-weight:400;color:#888;">(solo números)</span></label>
    <input type="tel"
           name="organizador_telefono"
           placeholder="Ej: 5491112345678"
           value="<?= $torneo->telefono_organizador ?? '' ?>"
           required>

    <label>Precio de inscripción <span style="font-weight:400;color:#888;">(por pareja, en $)</span></label>
    <input type="number"
           name="precio_inscripcion"
           placeholder="Ej: 5000"
           min="0"
           value="<?= $torneo->precio_inscripcion ?? '' ?>">

    <label>Alias de pago <span style="font-weight:400;color:#888;">(para transferencias de inscripción)</span></label>
    <input type="text"
           name="alias_pago"
           placeholder="Ej: telares.padel"
           value="<?= htmlspecialchars($torneo->alias_pago ?? '') ?>">

    <label>Premios</label>
    <textarea name="premios"
              placeholder="Ej: 1° puesto: trofeo + bolsón de productos&#10;2° puesto: medalla"
              rows="3"
              style="resize:vertical;"><?= $torneo->premios ?? '' ?></textarea>

    <?php if(isset($torneo)): ?>
        <label>Estado del torneo</label>
        <select name="estado">
            <option value="proxima"    <?= $torneo->estado=='proxima'   ?'selected':'' ?>>Próxima</option>
            <option value="en_curso"   <?= $torneo->estado=='en_curso'  ?'selected':'' ?>>En curso</option>
            <option value="finalizado" <?= $torneo->estado=='finalizado'?'selected':'' ?>>Finalizado</option>
        </select>
    <?php endif; ?>

    <hr>
    <h3>Visibilidad</h3>

    <?php
    $chk_visible       = isset($torneo->visible) && $torneo->visible == "t" ? true : false;
    $chk_inscripciones = isset($torneo->inscripciones_visibles) && $torneo->inscripciones_visibles == "t" ? true : false;
    $chk_fixture       = isset($torneo->fixture_visible) && $torneo->fixture_visible == "t" ? true : false;
    $chk_zona          = isset($torneo->zona_visible) && $torneo->zona_visible == "t" ? true : false;
    $chk_resultados    = isset($torneo->resultados_visibles) && $torneo->resultados_visibles == "t" ? true : false;
    $chk_partidos      = isset($torneo->partidos_visibles) && $torneo->partidos_visibles == "t" ? true : false;
    ?>

    <label class="toggle-label">
        <input type="checkbox" name="visible" value="1" <?= $chk_visible ? 'checked' : '' ?>>
        <span>Torneo visible para usuarios</span>
        <small style="color:#888;display:block;margin-top:2px;">Si está desactivado, solo el admin puede ver este torneo</small>
    </label>

    <label class="toggle-label">
        <input type="checkbox" name="inscripciones_visibles" value="1" <?= $chk_inscripciones ? 'checked' : '' ?>>
        <span>Inscripciones visibles para usuarios</span>
    </label>

    <label class="toggle-label">
        <input type="checkbox" name="fixture_visible" value="1" <?= $chk_fixture ? 'checked' : '' ?>>
        <span>Fixture/Cruces visible para usuarios</span>
    </label>

    <label class="toggle-label">
        <input type="checkbox" name="zona_visible" value="1" <?= $chk_zona ? 'checked' : '' ?>>
        <span>Zonas visibles para usuarios</span>
    </label>

    <label class="toggle-label">
        <input type="checkbox" name="resultados_visibles" value="1" <?= $chk_resultados ? 'checked' : '' ?>>
        <span>Resultados visibles para usuarios</span>
    </label>

    <label class="toggle-label">
        <input type="checkbox" name="partidos_visibles" value="1" <?= $chk_partidos ? 'checked' : '' ?>>
        <span>Listado de partidos visible para usuarios</span>
    </label>

    <hr>
    <label>Flyer del torneo</label>

    <input type="file" name="imagen" accept="image/*">

    <?php if(isset($torneo) && !empty($torneo->imagen)): ?>
        <p>Imagen actual:</p>
        <img src="data:image/jpeg;base64,<?= $torneo->imagen ?>"
             style="max-width:200px;">
    <?php endif; ?>

    <hr>

    <h3>Categorías del torneo</h3>

    <div class="categorias-grid">

    <?php foreach($categorias as $cat): ?>

    <label class="categoria-check">
        <input type="checkbox"
            name="categorias[]"
            value="<?= $cat->id ?>"
            <?= (isset($categorias_torneo_ids) && in_array($cat->id, $categorias_torneo_ids))
                    ? 'checked' : '' ?>>

        <span><?= $cat->nombre ?></span>
    </label>

    <?php endforeach; ?>

    </div>

    <?php if(isset($torneo)): ?>

    <hr>
    <div class="admin-section-header">
        <h3>Participantes por categoría</h3>

        <a
            class="btn-create"
            onclick="abrirModalParticipante(<?= isset($torneo) ? $torneo->id : 0 ?>)">
            + Nueva pareja
        </a>
    </div>

    <div id="listaInscripciones" class="inscripciones-lista"></div>

    <?php endif; ?>

    <button class="btn-create">
        Guardar Torneo
    </button>

</form>

</div>
</div>
</div>

<?php if(isset($torneo)): ?>
<script>const TORNEO_ID_STATIC = <?= $torneo->id ?>;</script>
<?php endif; ?>

<style>
/* Buscador de participantes en modal */
.player-slot { margin:12px 0 18px; }
.player-slot h4 { margin:0 0 8px; font-size:14px; color:#555; text-transform:uppercase; letter-spacing:.5px; }
.player-search-wrap { position:relative; }
.player-search-input { width:100%; padding:9px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px; box-sizing:border-box; }
.player-dropdown {
    position:absolute; top:100%; left:0; right:0; background:#fff;
    border:1px solid #ddd; border-top:none; border-radius:0 0 6px 6px;
    z-index:200; max-height:220px; overflow-y:auto; display:none;
    box-shadow:0 4px 12px rgba(0,0,0,.1);
}
.player-dropdown .dp-item {
    padding:10px 14px; cursor:pointer; font-size:13px; border-bottom:1px solid #f0f0f0;
}
.player-dropdown .dp-item:hover { background:#fff5ee; }
.player-dropdown .dp-item strong { display:block; }
.player-dropdown .dp-item small { color:#888; }
.player-dropdown .dp-no-results { padding:12px 14px; font-size:13px; color:#999; font-style:italic; }
.player-selected-card {
    display:none; align-items:center; justify-content:space-between;
    background:#eafaf1; border:1px solid #a9dfbf; border-radius:6px;
    padding:9px 14px; font-size:13px;
}
.player-selected-card .pc-name { font-weight:600; color:#1e8449; }
.player-selected-card .pc-info { color:#555; font-size:12px; }
.btn-clear-player { background:none; border:none; cursor:pointer; color:#999; font-size:16px; padding:0 4px; }
.btn-clear-player:hover { color:#e74c3c; }
.new-player-divider { font-size:12px; color:#aaa; margin:8px 0 6px; }
.new-player-fields { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.new-player-fields input { padding:7px 10px; border:1px solid #ddd; border-radius:5px; font-size:13px; box-sizing:border-box; width:100%; }
.new-player-fields input.full { grid-column:1/-1; }

/* ===== Lista inscripciones compacta ===== */
.cat-section { margin-bottom:10px; border:1px solid #e0e0e0; border-radius:8px; overflow:hidden; }
.cat-header {
    display:flex; align-items:center; gap:10px;
    padding:10px 14px; background:#f0f6ff; cursor:pointer;
    user-select:none; border-left:4px solid #1976D2;
}
.cat-header:hover { background:#e3efff; }
.cat-header .cat-title { font-weight:700; font-size:14px; color:#1565C0; flex:1; }
.cat-header .cat-count { font-size:12px; color:#666; background:#fff; border-radius:12px; padding:2px 10px; border:1px solid #ccc; }
.cat-header .cat-arrow { font-size:11px; color:#999; transition:transform .2s; }
.cat-header.collapsed .cat-arrow { transform:rotate(-90deg); }
.cat-body { padding:8px; background:#fafafa; }
.ins-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:6px; }
.ins-row {
    display:flex; align-items:center; gap:8px;
    background:#fff; border:1px solid #eee; border-radius:6px;
    padding:7px 10px; font-size:13px;
}
.ins-num { font-size:11px; color:#aaa; min-width:32px; }
.ins-players { flex:1; line-height:1.5; }
.ins-players .p-name { font-weight:600; color:#333; }
.ins-players .p-dni { color:#aaa; font-size:11px; margin-left:4px; }
.ins-del { background:none; border:none; cursor:pointer; font-size:14px; color:#ccc; padding:2px 4px; border-radius:4px; }
.ins-del:hover { color:#e74c3c; background:#fef2f2; }
.ins-estado { font-size:11px; padding:2px 7px; border-radius:10px; background:#fff3cd; color:#856404; }
</style>

<div id="modalParticipante" class="modal">
    <div class="modal-content" style="max-width:520px; max-height:90vh; overflow-y:auto;">

        <h3>Nueva pareja</h3>

        <form id="formParticipante" action="javascript:void(0)">

            <input type="hidden" name="torneo_id" id="torneo_id">

            <label>Categoría</label>
            <select name="categoria" id="categoria" required style="width:100%;padding:8px 10px;border:1px solid #ddd;border-radius:6px;font-size:14px;margin-bottom:6px;">
                <option value="">Seleccionar categoría</option>
                <?php foreach($categorias_torneo as $cat): ?>
                    <option value="<?= $cat->id ?>"><?= $cat->nombre ?></option>
                <?php endforeach; ?>
            </select>

            <!-- ===== JUGADOR 1 ===== -->
            <div class="player-slot">
                <h4>Jugador 1</h4>
                <input type="hidden" name="participante1_id" id="p1_id" value="">

                <!-- Card cuando está seleccionado -->
                <div class="player-selected-card" id="p1_card">
                    <div>
                        <div class="pc-name" id="p1_card_name"></div>
                        <div class="pc-info" id="p1_card_info"></div>
                    </div>
                    <button type="button" class="btn-clear-player" onclick="clearPlayer(1)" title="Cambiar">✕</button>
                </div>

                <!-- Área de búsqueda y creación nueva -->
                <div id="p1_search_area">
                    <div class="player-search-wrap">
                        <input type="text" id="p1_search" class="player-search-input"
                               placeholder="Buscar por nombre, apellido o DNI..." autocomplete="off">
                        <div class="player-dropdown" id="p1_dropdown"></div>
                    </div>
                    <div class="new-player-divider">— o completá los datos para registrar nuevo —</div>
                    <div class="new-player-fields">
                        <input type="text" name="nombre1"      id="nombre1"      placeholder="Nombre *">
                        <input type="text" name="apellido1"    id="apellido1"    placeholder="Apellido *">
                        <input type="text" name="dni1"         id="dni1"         placeholder="DNI">
                        <input type="text" name="telefono1"    id="telefono1"    placeholder="Teléfono">
                        <input type="text" name="categoria_p1" id="categoria_p1" placeholder="Categoría jugador" class="full">
                    </div>
                </div>
            </div>

            <!-- ===== JUGADOR 2 ===== -->
            <div class="player-slot">
                <h4>Jugador 2</h4>
                <input type="hidden" name="participante2_id" id="p2_id" value="">

                <div class="player-selected-card" id="p2_card">
                    <div>
                        <div class="pc-name" id="p2_card_name"></div>
                        <div class="pc-info" id="p2_card_info"></div>
                    </div>
                    <button type="button" class="btn-clear-player" onclick="clearPlayer(2)" title="Cambiar">✕</button>
                </div>

                <div id="p2_search_area">
                    <div class="player-search-wrap">
                        <input type="text" id="p2_search" class="player-search-input"
                               placeholder="Buscar por nombre, apellido o DNI..." autocomplete="off">
                        <div class="player-dropdown" id="p2_dropdown"></div>
                    </div>
                    <div class="new-player-divider">— o completá los datos para registrar nuevo —</div>
                    <div class="new-player-fields">
                        <input type="text" name="nombre2"      id="nombre2"      placeholder="Nombre *">
                        <input type="text" name="apellido2"    id="apellido2"    placeholder="Apellido *">
                        <input type="text" name="dni2"         id="dni2"         placeholder="DNI">
                        <input type="text" name="telefono2"    id="telefono2"    placeholder="Teléfono">
                        <input type="text" name="categoria_p2" id="categoria_p2" placeholder="Categoría jugador" class="full">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-create" style="width:100%;margin-top:8px;">
                Guardar inscripción
            </button>
            <button type="button" onclick="cerrarModal()" style="width:100%;margin-top:8px;padding:10px;background:#eee;border:none;border-radius:6px;cursor:pointer;">
                Cancelar
            </button>

        </form>
    </div>
</div>

<script>
const BUSCAR_URL = "<?= base_url('admin/torneos/buscar_participantes') ?>";

function renderInscripciones(inscripciones) {
    const agrupadas = {};

    inscripciones.forEach(ins => {
        const cat = ins.categoria || 'Sin categoría';
        if (!agrupadas[cat]) agrupadas[cat] = [];
        agrupadas[cat].push(ins);
    });

    let html = '';
    Object.keys(agrupadas).sort().forEach(categoria => {
        const lista = agrupadas[categoria];
        const rows = lista.map(ins => {
            const p1 = `${ins.apellido1 || ''} ${ins.nombre1 || ''}`.trim();
            const p2 = `${ins.apellido2 || ''} ${ins.nombre2 || ''}`.trim();
            const d1 = ins.dni1 ? `<span class="p-dni">${ins.dni1}</span>` : '';
            const d2 = ins.dni2 ? `<span class="p-dni">${ins.dni2}</span>` : '';
            return `
                <div class="ins-row">
                    <span class="ins-num">#${ins.id}</span>
                    <div class="ins-players">
                        <div class="p-name">${p1}${d1}</div>
                        <div class="p-name">${p2}${d2}</div>
                    </div>
                    <button class="ins-del" onclick="eliminarInscripcion(${ins.id})" title="Eliminar">🗑</button>
                </div>`;
        }).join('');

        html += `
            <div class="cat-section">
                <div class="cat-header" onclick="toggleCat(this)">
                    <span class="cat-title">${categoria}</span>
                    <span class="cat-count">${lista.length} pareja${lista.length !== 1 ? 's' : ''}</span>
                    <span class="cat-arrow">▼</span>
                </div>
                <div class="cat-body">
                    <div class="ins-grid">${rows}</div>
                </div>
            </div>`;
    });

    document.getElementById('listaInscripciones').innerHTML = html || '<p style="color:#aaa;font-size:14px;padding:10px 0;">Sin inscripciones aún.</p>';
}

function toggleCat(header) {
    const body = header.nextElementSibling;
    const collapsed = header.classList.toggle('collapsed');
    body.style.display = collapsed ? 'none' : '';
}

const inscripcionesIniciales = <?= json_encode($inscripciones ?? []) ?>;
renderInscripciones(inscripcionesIniciales);

/* ---- Player search ---- */
function setupPlayerSearch(n) {
    const searchInput = document.getElementById('p' + n + '_search');
    const dropdown    = document.getElementById('p' + n + '_dropdown');
    let timer;

    searchInput.addEventListener('input', function() {
        clearTimeout(timer);
        const q = this.value.trim();

        if (q.length < 2) {
            dropdown.innerHTML = '';
            dropdown.style.display = 'none';
            return;
        }

        timer = setTimeout(() => {
            fetch(BUSCAR_URL + '?q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(results => {
                    if (results.length === 0) {
                        dropdown.innerHTML = '<div class="dp-no-results">Sin resultados — completá los datos abajo para crear nuevo.</div>';
                    } else {
                        dropdown.innerHTML = results.map(p => {
                            const info = [p.dni ? 'DNI: ' + p.dni : '', p.categoria || ''].filter(Boolean).join(' · ');
                            const nom  = (p.apellido + ', ' + p.nombre).replace(/'/g, "\\'");
                            return `<div class="dp-item"
                                         onclick="selectPlayer(${n}, ${p.id}, '${nom}', '${(p.dni||'').replace(/'/g,"\\'")}', '${(p.categoria||'').replace(/'/g,"\\'")}')">
                                        <strong>${p.apellido}, ${p.nombre}</strong>
                                        ${info ? '<small>' + info + '</small>' : ''}
                                    </div>`;
                        }).join('');
                    }
                    dropdown.style.display = 'block';
                });
        }, 280);
    });

    // Cerrar dropdown al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
}

function selectPlayer(n, id, nombre, dni, categoria) {
    document.getElementById('p' + n + '_id').value = id;
    document.getElementById('p' + n + '_card_name').textContent = nombre;
    document.getElementById('p' + n + '_card_info').textContent =
        [dni ? 'DNI: ' + dni : '', categoria].filter(Boolean).join(' · ');

    document.getElementById('p' + n + '_card').style.display = 'flex';
    document.getElementById('p' + n + '_search_area').style.display = 'none';
}

function clearPlayer(n) {
    document.getElementById('p' + n + '_id').value = '';
    document.getElementById('p' + n + '_card').style.display = 'none';
    document.getElementById('p' + n + '_search_area').style.display = 'block';
    document.getElementById('p' + n + '_search').value = '';
    document.getElementById('p' + n + '_dropdown').style.display = 'none';
    ['nombre', 'apellido', 'dni', 'telefono', 'categoria_p'].forEach(f => {
        const el = document.getElementById(f + n);
        if (el) el.value = '';
    });
}

setupPlayerSearch(1);
setupPlayerSearch(2);

/* ---- Abrir / cerrar modal ---- */
function abrirModalParticipante(torneoId) {
    document.getElementById('torneo_id').value = torneoId;
    clearPlayer(1);
    clearPlayer(2);
    document.getElementById('formParticipante').reset();
    document.getElementById('torneo_id').value = torneoId;
    document.getElementById('modalParticipante').style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modalParticipante').style.display = 'none';
}

window.onclick = function(e) {
    const modal = document.getElementById('modalParticipante');
    if (e.target === modal) cerrarModal();
};

/* ---- Submit ---- */
document.getElementById('formParticipante').addEventListener('submit', function(e) {
    e.preventDefault();

    const p1Id = document.getElementById('p1_id').value;
    const p2Id = document.getElementById('p2_id').value;

    if (!p1Id) {
        if (!document.getElementById('nombre1').value.trim() || !document.getElementById('apellido1').value.trim()) {
            alert('Buscá un jugador existente o completá Nombre y Apellido del Jugador 1.');
            return;
        }
    }
    if (!p2Id) {
        if (!document.getElementById('nombre2').value.trim() || !document.getElementById('apellido2').value.trim()) {
            alert('Buscá un jugador existente o completá Nombre y Apellido del Jugador 2.');
            return;
        }
    }

    const formData = new FormData(this);

    fetch("<?= base_url('admin/torneos/guardar_inscripcion') ?>", {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(resp => {
        renderInscripciones(resp);
        cerrarModal();
    })
    .catch(err => {
        console.error('Error al guardar inscripción:', err);
        alert('Error al guardar la inscripción. Revisá la consola para más detalles.');
    });
});

/* ---- Eliminar ---- */
function eliminarInscripcion(id) {
    if (!confirm('¿Eliminar esta pareja?')) return;

    const formData = new FormData();
    formData.append('id', id);
    formData.append('torneo_id', typeof TORNEO_ID_STATIC !== 'undefined' ? TORNEO_ID_STATIC : document.getElementById('torneo_id').value);

    fetch("<?= base_url('admin/Torneos/eliminar_inscripcion') ?>", {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(resp => {
        renderInscripciones(resp);
    })
    .catch(err => {
        alert('Error al eliminar');
        console.error(err);
    });
}
</script>