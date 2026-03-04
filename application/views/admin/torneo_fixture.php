<div class="">
    <div class="">

        <div class="container">

            <!-- =============================
                SELECTOR CATEGORIA
            ============================= -->

            <form method="get">
                <select name="categoria_id" class="select-categoria"
                        onchange="this.form.submit()">

                    <?php foreach($categorias as $cat): ?>
                        <option value="<?= $cat->id ?>"
                            <?= $categoria_id == $cat->id ? 'selected':'' ?>>
                            <?= $cat->nombre ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </form>

            <br>

            <!-- =============================
                    TABS
            ============================= -->

            <div class="fixture-tabs">
                <button class="tab active" onclick="showTab(event,'config')">Configuración</button>
                <button class="tab" onclick="showTab(event,'zonas')">Zonas</button>
                <button class="tab" onclick="showTab(event,'resultados')">Resultados</button>
                <button class="tab" onclick="showTab(event,'playoff')">Cruces</button>
            </div>

            <!-- =============================
                    CONFIGURACIÓN DE ZONAS
            ============================= -->

            <div id="tab-config" class="fixture-tab-content">

                <div class="config-card">

                    <h3>Asignación de Zonas</h3>
                    <p class="config-hint">Seleccioná la cantidad de zonas y asigná cada pareja a su zona. Luego guardá y generá los partidos.</p>

                    <form method="post" action="<?= base_url('admin/Torneos/guardar_zonas/'.$torneo->id) ?>">
                        <input type="hidden" name="categoria_id" value="<?= $categoria_id ?>">

                        <div class="config-num-zonas">
                            <label>Número de Zonas</label>
                            <select name="num_zonas" id="num_zonas" onchange="actualizarSelectsZona(this.value)">
                                <?php
                                $nZonasActual = !empty($zonas_db) ? count($zonas_db) : 2;
                                foreach ([2,3,4,5,6,8] as $opcion): ?>
                                    <option value="<?= $opcion ?>" <?= $nZonasActual == $opcion ? 'selected' : '' ?>>
                                        <?= $opcion ?> Zonas
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if (!empty($inscripciones_zona)): ?>

                            <table class="config-tabla">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Pareja</th>
                                        <th>Zona</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inscripciones_zona as $idx => $insc): ?>
                                    <tr>
                                        <td><?= $idx + 1 ?></td>
                                        <td><?= htmlspecialchars($insc->pareja_nombre) ?></td>
                                        <td>
                                            <select name="zona[<?= $insc->id ?>]" class="zona-select">
                                                <option value="">-- Sin asignar --</option>
                                                <?php for ($z = 1; $z <= $nZonasActual; $z++): ?>
                                                    <option value="<?= $z ?>" <?= $insc->zona_numero == $z ? 'selected' : '' ?>>
                                                        Zona <?= chr(64 + $z) ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        <?php else: ?>
                            <p class="config-hint">No hay inscriptos en esta categoría aún.</p>
                        <?php endif; ?>

                        <div class="config-actions">
                            <button type="submit" class="btn-guardar-config">Guardar Configuración</button>
                        </div>

                    </form>

                    <?php if (!empty($zonas_db)): ?>
                    <form method="post"
                          action="<?= base_url('admin/Torneos/generar_partidos/'.$torneo->id) ?>"
                          onsubmit="return confirm('¿Generar partidos? Se eliminarán los partidos actuales.')">
                        <input type="hidden" name="categoria_id" value="<?= $categoria_id ?>">
                        <div class="config-actions" style="margin-top:10px;">
                            <button type="submit" class="btn-generar-partidos">⚙ Generar Partidos</button>
                        </div>
                    </form>
                    <?php endif; ?>

                </div>

            </div>

            <!-- =============================
                    ZONAS
            ============================= -->

            <div id="tab-zonas" class="fixture-tab-content" style="display:none;">

                <div class="fixture-container">

                    <?php foreach($zonas as $zona): ?>

                        <div class="zona-card">

                            <!-- HEADER -->
                            <div class="zona-header">
                                <h2>FASE DE GRUPOS</h2>
                            </div>

                            <!-- GRUPO -->
                            <div class="grupo-title">
                                ZONA <?= chr(64 + $zona['grupo']) ?>
                            </div>

                            <!-- PAREJAS -->
                            <div class="parejas-box">

                                <div class="parejas-header">
                                    <div></div>
                                    <div>PAREJA</div>
                                </div>

                                <?php foreach($zona['parejas'] as $pareja): ?>

                                    <div class="pareja-row">
                                        <div class="numero">
                                            <?= $pareja['numero'] ?>
                                        </div>

                                        <div class="nombre">
                                            <?= strtoupper($pareja['nombre']) ?>
                                        </div>
                                    </div>

                                <?php endforeach; ?>

                            </div>

                            <!-- DUELOS -->
                            <div class="duelos-box">

                                <div class="duelos-header">
                                    <div>DUELO</div>
                                    <div>DIA</div>
                                    <div>HORA</div>
                                    <div>CANCHA</div>
                                    <div>SETS</div>
                                </div>

                                <?php foreach($zona['partidos'] as $partido): ?>

                                    <div class="duelo-row"
                                        data-partido-id="<?= $partido['partido_id'] ?>"
                                        onclick="abrirModalPartido(this)"
                                        style="cursor:pointer;">

                                        <div><?= $partido['duelo'] ?></div>
                                        <div><?= strtoupper($partido['dia']) ?></div>
                                        <div class="hora"><?= $partido['hora'] ?></div>
                                        <div class="cancha"><?= $partido['cancha'] ?></div>
                                        <div class="sets">
                                            <?= $partido['set1_p1'] ?? '-' ?>-<?= $partido['set1_p2'] ?? '-' ?>
                                            <?= $partido['set2_p1'] ?? '-' ?>-<?= $partido['set2_p2'] ?? '-' ?>
                                            <?= $partido['set3_p1'] ?? '-' ?>-<?= $partido['set3_p2'] ?? '-' ?>
                                        </div>
                                    </div>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

            <!-- =============================
                    RESULTADOS
            ============================= -->

            <div id="tab-resultados" class="fixture-tab-content" style="display:none;">

                <div class="resultados-wrapper container">
                    <div class="">
                        <?php foreach($zonas as $zona): ?>

                            <div class="resultados-card">

                                <div class="resultados-header">
                                    <h3>Zona <?= chr(64 + $zona['grupo']) ?></h3>
                                </div>

                                <?php
                                $parejas_map = [];
                                foreach($zona['parejas'] as $p){
                                    $parejas_map[$p['numero']] = strtoupper($p['nombre']);
                                }
                                ?>

                                <div class="resultados-body">

                                    <?php foreach($zona['partidos'] as $partido): ?>

                                        <?php
                                        list($p1,$p2) = explode(' VS ', $partido['duelo']);

                                        $nombre1 = $parejas_map[$p1] ?? '';
                                        $nombre2 = $parejas_map[$p2] ?? '';

                                        $jugado = $partido['set1_p1'] !== null;
                                        ?>

                                        <div class="resultado-item">

                                            <div class="resultado-meta">
                                                <span><?= $partido['dia'] ?></span>
                                                <span><?= $partido['hora'] ?></span>
                                                <span>Cancha <?= $partido['cancha'] ?? '-' ?></span>
                                            </div>

                                            <div class="resultado-match">

                                                <div class="pareja-block">
                                                    <div class="pareja-nombre"><?= $nombre1 ?></div>
                                                    <div class="sets">
                                                        <?php if($jugado): ?>
                                                            <span><?= $partido['set1_p1'] ?>-<?= $partido['set1_p2'] ?></span>
                                                            <?php if($partido['set2_p1'] !== null): ?>
                                                                <span><?= $partido['set2_p1'] ?>-<?= $partido['set2_p2'] ?></span>
                                                            <?php endif; ?>
                                                            <?php if($partido['set3_p1'] !== null): ?>
                                                                <span><?= $partido['set3_p1'] ?>-<?= $partido['set3_p2'] ?></span>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="estado-pendiente">Pendiente</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="pareja-block">
                                                    <div class="pareja-nombre"><?= $nombre2 ?></div>
                                                </div>

                                            </div>

                                        </div>

                                    <?php endforeach; ?>

                                </div>

                            </div>

                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <!-- =============================
                    CRUCES / PLAYOFF
            ============================= -->

            <div id="tab-playoff" class="fixture-tab-content" style="display:none;">

                <?php if (empty($playoff)): ?>
                    <p class="bracket-empty">No hay cruces generados aún.</p>
                <?php else: ?>

                    <div class="bracket-wrapper">

                        <?php foreach ($playoff as $ronda => $rondaData): ?>

                            <div class="bracket-round">

                                <div class="round-label"><?= htmlspecialchars($rondaData['nombre']) ?></div>

                                <div class="round-matches">

                                    <?php foreach ($rondaData['partidos'] as $partido): ?>

                                        <?php
                                            $p1_winner = $partido->ganador_id && $partido->ganador_id == $partido->pareja1_id;
                                            $p2_winner = $partido->ganador_id && $partido->ganador_id == $partido->pareja2_id;

                                            $p1_name = $partido->pareja1_nombre ?: ($partido->referencia1 ? '['.$partido->referencia1.']' : 'Por definir');
                                            $p2_name = $partido->pareja2_nombre ?: ($partido->referencia2 ? '['.$partido->referencia2.']' : 'Por definir');

                                            $p1_tbd = !$partido->pareja1_id;
                                            $p2_tbd = !$partido->pareja2_id;
                                        ?>

                                        <?php
                                            $jugado_playoff = $partido->set1_p1 !== null;
                                        ?>
                                        <div class="match-wrapper">
                                            <div class="match-card-bracket admin-clickable"
                                                data-partido-id="<?= $partido->id ?>"
                                                onclick="abrirModalPartido(this)"
                                                title="Cargar resultado">

                                                <div class="match-team <?= $p1_winner ? 'winner' : ($p1_tbd ? 'tbd' : '') ?>">
                                                    <span class="team-name"><?= htmlspecialchars($p1_name) ?></span>
                                                    <?php if ($jugado_playoff): ?>
                                                        <span class="team-score">
                                                            <?= $partido->set1_p1 ?>-<?= $partido->set1_p2 ?>
                                                            <?php if ($partido->set2_p1 !== null): ?> / <?= $partido->set2_p1 ?>-<?= $partido->set2_p2 ?><?php endif; ?>
                                                            <?php if ($partido->set3_p1 !== null): ?> / <?= $partido->set3_p1 ?>-<?= $partido->set3_p2 ?><?php endif; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="match-divider"></div>

                                                <div class="match-team <?= $p2_winner ? 'winner' : ($p2_tbd ? 'tbd' : '') ?>">
                                                    <span class="team-name"><?= htmlspecialchars($p2_name) ?></span>
                                                    <?php if ($jugado_playoff): ?>
                                                        <span class="team-score">
                                                            <?= $partido->set1_p2 ?>-<?= $partido->set1_p1 ?>
                                                            <?php if ($partido->set2_p1 !== null): ?> / <?= $partido->set2_p2 ?>-<?= $partido->set2_p1 ?><?php endif; ?>
                                                            <?php if ($partido->set3_p1 !== null): ?> / <?= $partido->set3_p2 ?>-<?= $partido->set3_p1 ?><?php endif; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>

                                            </div>
                                        </div>

                                    <?php endforeach; ?>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>

        </div>
    </div>
</div>

<!-- =============================
        MODAL EDITAR PARTIDO
============================= -->

<div id="modalPartido" class="modal" style="display:none;">

    <div class="modal-content">

        <h3>Editar Partido</h3>

        <form id="formPartido">

            <input type="hidden" name="id" id="partido_id">

            <label>Día</label>
            <input type="date" name="dia" id="dia">

            <label>Hora</label>
            <input type="time" name="hora" id="hora" step="1800">

            <label>Cancha</label>
            <input type="text" name="cancha" id="cancha">

            <label>Primer set</label>
            <input type="text" name="set_1" id="set_1" placeholder="N-N" pattern="^\d{1,2}-\d{1,2}$">

            <label>Segundo set</label>
            <input type="text" name="set_2" id="set_2" placeholder="N-N" pattern="^\d{1,2}-\d{1,2}$">

            <label>Tercer set</label>
            <input type="text" name="set_3" id="set_3" placeholder="N-N" pattern="^\d{1,2}-\d{1,2}$">

            <button type="submit">Guardar</button>
            <button type="button" onclick="cerrarModal()">Cancelar</button>

        </form>

    </div>
</div>


<script>
function abrirModalPartido(el)
{
    const partidoId = el.dataset.partidoId;

    fetch("<?= base_url('admin/torneos/obtener_partido') ?>/" + partidoId)
        .then(r => r.json())
        .then(data => {

            document.getElementById('partido_id').value = data.id;

            let fechaCompleta = data.fecha;
            if (fechaCompleta) {
                document.getElementById('dia').value = fechaCompleta.slice(0, 10);
            } else {
                document.getElementById('dia').value = '';
            }

            document.getElementById('hora').value    = data.hora   ?? '';
            document.getElementById('cancha').value  = data.cancha ?? '';
            document.getElementById('set_1').value   = data.set_1  ?? '';
            document.getElementById('set_2').value   = data.set_2  ?? '';
            document.getElementById('set_3').value   = data.set_3  ?? '';

            document.getElementById('modalPartido').style.display = 'flex';
        });
}

function cerrarModal()
{
    document.getElementById('modalPartido').style.display = 'none';
}
</script>

<script>
document.getElementById('formPartido')
.addEventListener('submit', function(e){

    e.preventDefault();

    const formData = new FormData(this);

    fetch("<?= base_url('admin/torneos/actualizar_partido') ?>", {
        method: "POST",
        body: formData
    })
    .then(r => r.json())
    .then(resp => {
        cerrarModal();
        location.reload();
    });

});
</script>

<script>
function showTab(e, tab)
{
    document.querySelectorAll('.fixture-tab-content')
        .forEach(el => el.style.display = 'none');

    document.getElementById('tab-' + tab).style.display = 'block';

    document.querySelectorAll('.tab')
        .forEach(t => t.classList.remove('active'));

    e.currentTarget.classList.add('active');
}

function actualizarSelectsZona(numZonas)
{
    numZonas = parseInt(numZonas);

    document.querySelectorAll('.zona-select').forEach(function(select) {
        var valorActual = parseInt(select.value) || 0;
        select.innerHTML = '<option value="">-- Sin asignar --</option>';

        for (var i = 1; i <= numZonas; i++) {
            var letra = String.fromCharCode(64 + i);
            var opt   = document.createElement('option');
            opt.value       = i;
            opt.textContent = 'Zona ' + letra;
            if (valorActual === i) opt.selected = true;
            select.appendChild(opt);
        }
    });
}
</script>
