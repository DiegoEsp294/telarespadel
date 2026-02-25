<section class="hero">
    <div class="hero-content">
        <h2><?= $torneo->nombre ?></h2>

        <p>
            <?= date('d/m/Y', strtotime($torneo->fecha_inicio)) ?>
            <?php if(!empty($torneo->fecha_fin)): ?>
                - <?= date('d/m/Y', strtotime($torneo->fecha_fin)) ?>
            <?php endif; ?>
        </p>

        <span class="status-badge <?= $torneo->estado ?>">
            <?= ucfirst(str_replace('_',' ', $torneo->estado)) ?>
        </span>
    </div>
</section>


<section class="torneo-detalle-section">
<div class="container">

    <div class="admin-header-actions" style="margin-bottom:20px;">
        <a href="<?= base_url('admin/Torneos/torneos') ?>" class="btn btn-secondary">
            ← Volver al listado
        </a>
    </div>

    <div class="torneo-detalle-card">

        <!-- IMAGEN -->
        <?php if(!empty($torneo->imagen)): ?>
            <div class="torneo-detalle-image">
                <img src="data:image/jpeg;base64,<?= $torneo->imagen ?>">
            </div>
        <?php endif; ?>


        <!-- INFO -->
        <div class="torneo-detalle-info">

            <h3>Información del torneo</h3>

            <div class="torneo-info-item">
                <i>📅</i>
                <div>
                    <strong>Fecha</strong>
                    <p>
                        <?= date('d/m/Y', strtotime($torneo->fecha_inicio)) ?>
                        <?php if($torneo->fecha_fin): ?>
                            al <?= date('d/m/Y', strtotime($torneo->fecha_fin)) ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <div class="torneo-info-item">
                <i>🏆</i>
                <div>
                    <strong>Categorías</strong>
                    <p><?= $torneo->categoria ?></p>
                </div>
            </div>

            <div class="torneo-info-item">
                <i>👤</i>
                <div>
                    <strong>Organizador</strong>
                    <p><?= $torneo->nombre_organizador ?></p>
                </div>
            </div>

            <div class="torneo-info-item">
                <i>📞</i>
                <div>
                    <strong>Teléfono</strong>
                    <p><?= $torneo->telefono_organizador ?></p>
                </div>
            </div>

        </div>

    </div>

    <?php if(isset($torneo)): ?>

    <hr>
    <div class="torneo-detalle-card">
        <div class="admin-section-header ">
            <h3>Participantes por categoría</h3>

        </div>

        <div id="listaInscripciones" class="inscripciones-lista"></div>

        <?php endif; ?>
    </div>

    <!-- DESCRIPCION -->
    <?php if(!empty($torneo->descripcion)): ?>
        <div class="torneo-descripcion-box">
            <h3>Descripción</h3>
            <p><?= nl2br($torneo->descripcion) ?></p>
        </div>
    <?php endif; ?>

</div>
</section>

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
</script>