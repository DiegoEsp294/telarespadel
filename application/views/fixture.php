<div class="">
    <div class="">
        <hr>

        <div class="container">
            <label>Seleccionar categoría:</label>
            <form method="get" style="width: 100%;">
                <select name="categoria_id" class="select-categoria"
                        onchange="this.form.submit()"
                        style="width: 100%; padding: 8px 12px; box-sizing: border-box;">
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
                <?php if($torneo->zona_visible == "t"):?>
                    <button class="tab active" onclick="showTab(event,'zonas')">Zonas</button>
                <?php endif; ?>
                <?php if($torneo->resultados_visibles == "t"):?>
                    <button class="tab" onclick="showTab(event,'resultados')">Resultados</button>
                <?php endif; ?>
                <?php if($torneo->fixture_visible == "t"):?>
                    <button class="tab" onclick="showTab(event,'playoff')">Cruces</button>
                <?php endif; ?>
            </div>

            <!-- =============================
                    ZONAS
            ============================= -->

            <div id="tab-zonas" class="fixture-tab-content">

                <div class="">
                    <?php if($torneo->zona_visible == "t"): ?>
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
                                            <div class="nombre">
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
                                        <!-- <div>SETS</div> -->

                                    </div>

                                    <?php $rondaActual = null; foreach($zona['partidos'] as $partido): ?>

                                        <?php if ($partido['ronda'] && $partido['ronda'] != $rondaActual): ?>
                                            <?php $rondaActual = $partido['ronda']; ?>
                                            <!-- <div class="ronda-label">
                                                <?= $partido['ronda'] == 1 ? 'RONDA 1' : 'CRUCES' ?>
                                            </div> -->
                                        <?php endif; ?>

                                        <div class="duelo-row"
                                            data-partido-id="<?= $partido['partido_id'] ?>">

                                            <div><?= $partido['duelo'] ?></div>
                                            <div><?= strtoupper($partido['dia']) ?></div>
                                            <div class="hora"><?= $partido['hora'] ?></div>
                                            <div class="cancha"><?= $partido['cancha'] ?></div>
                                        </div>

                                    <?php endforeach; ?>

                                </div>

                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="bracket-empty">La fase de grupos no es visible para este torneo.</p>
                    <?php endif; ?>
                </div>

            </div>

            <div id="tab-resultados" class="fixture-tab-content" style="display:none;>

                <div class="resultados-wrapper container">
                    <div class="">
                        <?php if($torneo->resultados_visibles == "t"): ?>
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

                                                    <!-- Pareja 1 -->
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

                                                    <!-- Pareja 2 -->
                                                    <div class="pareja-block">
                                                        <div class="pareja-nombre"><?= $nombre2 ?></div>
                                                        <!-- <div class="sets">
                                                            <?php if($jugado): ?>
                                                                <span><?= $partido['set1_p2'] ?>-<?= $partido['set1_p1'] ?></span>
                                                                <?php if($partido['set2_p1'] !== null): ?>
                                                                    <span><?= $partido['set2_p2'] ?>-<?= $partido['set2_p1'] ?></span>
                                                                <?php endif; ?>
                                                                <?php if($partido['set3_p1'] !== null): ?>
                                                                    <span><?= $partido['set3_p2'] ?>-<?= $partido['set3_p1'] ?></span>
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                <span class="estado-pendiente">Pendiente</span>
                                                            <?php endif; ?>
                                                        </div> -->
                                                    </div>

                                                </div>

                                            </div>

                                        <?php endforeach; ?>

                                    </div>

                                </div>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="bracket-empty">Los resultados no son visibles para este torneo.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
            <!-- =============================
                    PLAYOFF
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
                                            <div class="match-card-bracket">

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


<script>
document.getElementById('formPartido')
.addEventListener('submit', function(e){

    e.preventDefault();

    const formData = new FormData(this);

    fetch("<?= base_url('admin/partidos/actualizar') ?>", {
        method: "POST",
        body: formData
    })
    .then(r => r.json())
    .then(resp => {

        if(resp.ok)
        {
            cerrarModal();

            // opción simple
            location.reload();

            // después podemos hacerlo realtime sin reload
        }
    });

});
</script>


<script>
function showTab(e, tab)
{
    // ocultar todos
    document.querySelectorAll('.fixture-tab-content')
        .forEach(el => el.style.display = 'none');

    // mostrar seleccionado
    document.getElementById('tab-' + tab).style.display = 'block';

    // quitar active
    document.querySelectorAll('.tab')
        .forEach(t => t.classList.remove('active'));

    // activar botón clickeado
    e.currentTarget.classList.add('active');
}
</script>