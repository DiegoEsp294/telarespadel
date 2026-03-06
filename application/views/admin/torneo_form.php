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
            onclick="abrirModalParticipante(<?= isset($torneo) ? $torneo->id : 0 ?>,'1')">
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

<div id="modalParticipante" class="modal">
    <div class="modal-content">

        <h3>Nueva pareja</h3>

        <form id="formParticipante">

            <input type="hidden" name="torneo_id" id="torneo_id">

            <label>Categoría</label>

            <select name="categoria" id="categoria" required>
                <option value="">Seleccionar categoría</option>

                <?php foreach($categorias_torneo as $cat): ?>
                    <option value="<?= $cat->id ?>">
                        <?= $cat->nombre ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <h4>JUGADOR 1</h4>
            <input type="text" name="nombre1" placeholder="Nombre" required>
            <input type="text" name="apellido1" placeholder="Apellido" required>
            <input type="text" name="telefono1" placeholder="Teléfono">

            <h4>JUGADOR 2</h4>
            <input type="text" name="nombre2" placeholder="Nombre" required>
            <input type="text" name="apellido2" placeholder="Apellido" required>
            <input type="text" name="telefono2" placeholder="Teléfono">

            <button type="submit" class="btn-create">
                Guardar inscripción
            </button>

            <button type="button" onclick="cerrarModal()">Cancelar</button>

        </form>
    </div>
</div>

<script>
function renderInscripciones(inscripciones) {

    let html = '';

    /* =========================
       1. AGRUPAR POR CATEGORIA
    ========================= */

    const agrupadas = {};

    inscripciones.forEach(ins => {
        if (!agrupadas[ins.categoria]) {
            agrupadas[ins.categoria] = [];
        }
        agrupadas[ins.categoria].push(ins);
    });


    /* =========================
       2. ORDENAR CATEGORIAS
    ========================= */

    const categoriasOrdenadas = Object.keys(agrupadas).sort();


    /* =========================
       3. RENDERIZAR
    ========================= */

    categoriasOrdenadas.forEach(categoria => {

        // Título de categoría
        html += `
            <h3 class="categoria-titulo">
                Categoría ${categoria}
            </h3>
        `;

        // Parejas dentro de esa categoría
        agrupadas[categoria].forEach(ins => {

            html += `
                <div class="inscripcion-card">

                    <div class="inscripcion-info">
                        <strong>Pareja #${ins.id}</strong>

                        <p>
                            Jugador 1: ${ins.nombre1} ${ins.apellido1}<br>
                            Jugador 2: ${ins.nombre2} ${ins.apellido2}
                        </p>
                    </div>

                    <div class="inscripcion-actions">

                        <span class="categoria ${ins.categoria}">
                            ${ins.categoria}
                        </span>

                        <span class="estado ${ins.estado}">
                            ${ins.estado}
                        </span>

                        <button
                            class="btn-delete"
                            onclick="eliminarInscripcion(${ins.id})">
                            🗑
                        </button>

                    </div>

                </div>
            `;
        });
    });

    document.getElementById('listaInscripciones').innerHTML = html;
}

const inscripcionesIniciales = <?= json_encode($inscripciones ?? []) ?>;
renderInscripciones(inscripcionesIniciales);

function abrirModalParticipante(torneoId, categoria)
{
    document.getElementById('torneo_id').value = torneoId;
    document.getElementById('categoria').value = categoria;

    document.getElementById('modalParticipante').style.display = 'flex';
}

function cerrarModal()
{
    document.getElementById('modalParticipante').style.display = 'none';
}
</script>

<script>
document.getElementById('formParticipante')
.addEventListener('submit', function(e){

    e.preventDefault();

    const formData = new FormData(this);

    fetch("<?= base_url('admin/torneos/guardar_inscripcion') ?>", {
        method: "POST",
        body: formData
    })
    .then(r => r.json())
    .then(resp => {
        renderInscripciones(resp);
        cerrarModal();
    });

});
</script>

<script>
window.onclick = function(e){
    const modal = document.getElementById('modalParticipante');
    if(e.target === modal){
        cerrarModal();
    }
}

function eliminarInscripcion(id)
{
    if(!confirm('¿Eliminar esta pareja?')) return;

    fetch("<?= base_url('admin/Torneos/eliminar_inscripcion') ?>", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ id: id })
    })
    .then(r => r.json())
    .then(resp => {
        renderInscripciones(resp);
    })
    .catch(err => {
        alert("Error al eliminar");
        console.error(err);
    });
}

</script>