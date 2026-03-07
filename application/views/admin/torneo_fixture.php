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
                <button class="tab" onclick="showTab(event,'inscriptos')">Inscriptos</button>
                <button class="tab" onclick="showTab(event,'zonas')">Zonas</button>
                <button class="tab" onclick="showTab(event,'resultados')">Resultados</button>
                <button class="tab" onclick="showTab(event,'playoff')">Cruces</button>
                <button class="tab" onclick="showTab(event,'listado')">Partidos</button>
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
                    INSCRIPTOS
            ============================= -->

            <div id="tab-inscriptos" class="fixture-tab-content" style="display:none;">

                <div class="config-card">

                    <h3>Inscriptos</h3>
                    <p class="config-hint">Hacé clic en <strong>Editar</strong> para modificar el nombre de una pareja.</p>

                    <?php if (!empty($inscriptos)): ?>

                        <table class="config-tabla">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Jugador 1</th>
                                    <th>Jugador 2</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inscriptos as $idx => $insc): ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td><?= htmlspecialchars($insc->apellido1 . ' ' . $insc->nombre1) ?></td>
                                    <td><?= htmlspecialchars($insc->apellido2 . ' ' . $insc->nombre2) ?></td>
                                    <td>
                                        <button class="btn-editar-insc" type="button"
                                            onclick="abrirModalInscripto(
                                                <?= $insc->id ?>,
                                                '<?= addslashes($insc->nombre1) ?>','<?= addslashes($insc->apellido1) ?>','<?= addslashes($insc->telefono1) ?>',
                                                '<?= addslashes($insc->nombre2) ?>','<?= addslashes($insc->apellido2) ?>','<?= addslashes($insc->telefono2) ?>'
                                            )">Editar</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                    <?php else: ?>
                        <p class="config-hint">No hay inscriptos en esta categoría aún.</p>
                    <?php endif; ?>

                </div>

            </div>

            <!-- Modal editar inscripto -->
            <div id="modalInscripto" class="modal-overlay" style="display:none;">
                <div class="modal-box">
                    <h3 class="modal-title">Editar Pareja</h3>
                    <form id="formEditarInscripto">
                        <input type="hidden" id="ei_id" name="inscripcion_id">

                        <p class="modal-section-label">Jugador 1</p>
                        <div class="modal-row-2">
                            <div>
                                <label>Apellido</label>
                                <input type="text" id="ei_apellido1" name="apellido1" required>
                            </div>
                            <div>
                                <label>Nombre</label>
                                <input type="text" id="ei_nombre1" name="nombre1" required>
                            </div>
                        </div>
                        <label>Teléfono</label>
                        <input type="text" id="ei_telefono1" name="telefono1">

                        <p class="modal-section-label">Jugador 2</p>
                        <div class="modal-row-2">
                            <div>
                                <label>Apellido</label>
                                <input type="text" id="ei_apellido2" name="apellido2" required>
                            </div>
                            <div>
                                <label>Nombre</label>
                                <input type="text" id="ei_nombre2" name="nombre2" required>
                            </div>
                        </div>
                        <label>Teléfono</label>
                        <input type="text" id="ei_telefono2" name="telefono2">

                        <div class="modal-actions">
                            <button type="submit" class="btn-guardar-config">Guardar</button>
                            <button type="button" class="btn-cancelar" onclick="cerrarModalInscripto()">Cancelar</button>
                        </div>
                    </form>
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
                                        data-partido-id="<?= $partido['partido_id'] ?>"
                                        onclick="abrirModalPartido(this)"
                                        style="cursor:pointer;">

                                        <div><?= $partido['duelo'] ?></div>
                                        <div><?= strtoupper($partido['dia']) ?></div>
                                        <div class="hora"><?= $partido['hora'] ?></div>
                                        <div class="cancha"><?= $partido['cancha'] ?></div>
                                        <!-- <div class="sets">
                                            <?= $partido['set1_p1'] ?? '-' ?>-<?= $partido['set1_p2'] ?? '-' ?>
                                            <?= $partido['set2_p1'] ?? '-' ?>-<?= $partido['set2_p2'] ?? '-' ?>
                                            <?= $partido['set3_p1'] ?? '-' ?>-<?= $partido['set3_p2'] ?? '-' ?>
                                        </div> -->
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

                <?php foreach($zonas as $zona): ?>

                    <?php
                    $parejas_map = [];
                    foreach($zona['parejas'] as $par){
                        $parejas_map[$par['numero']] = $par['nombre'];
                    }
                    ?>

                    <div class="res-zona-bloque">
                        <div class="res-zona-titulo">Zona <?= chr(64 + $zona['grupo']) ?></div>

                        <div class="res-zona-partidos">
                            <?php foreach($zona['partidos'] as $partido): ?>
                                <?php
                                list($n1, $n2) = explode(' VS ', $partido['duelo']);
                                $nombre1 = $parejas_map[$n1] ?? '-';
                                $nombre2 = $parejas_map[$n2] ?? '-';
                                $jugado  = $partido['set1_p1'] !== null;

                                $sets_p1 = 0; $sets_p2 = 0;
                                if ($jugado) {
                                    if ($partido['set1_p1'] > $partido['set1_p2']) $sets_p1++; else $sets_p2++;
                                    if ($partido['set2_p1'] !== null) {
                                        if ($partido['set2_p1'] > $partido['set2_p2']) $sets_p1++; else $sets_p2++;
                                    }
                                    if ($partido['set3_p1'] !== null) {
                                        if ($partido['set3_p1'] > $partido['set3_p2']) $sets_p1++; else $sets_p2++;
                                    }
                                }
                                $p1_win = $jugado && $sets_p1 > $sets_p2;
                                $p2_win = $jugado && $sets_p2 > $sets_p1;
                                ?>

                                <div class="res-partido-card admin-clickable"
                                     data-partido-id="<?= $partido['partido_id'] ?>"
                                     onclick="abrirModalResultado(this)"
                                     title="Editar resultado">

                                    <?php if ($partido['hora'] || $partido['cancha']): ?>
                                    <div class="res-partido-meta">
                                        <?php if ($partido['hora']): ?><span><?= substr($partido['hora'], 0, 5) ?></span><?php endif; ?>
                                        <?php if ($partido['cancha']): ?><span>Cancha <?= $partido['cancha'] ?></span><?php endif; ?>
                                    </div>
                                    <?php endif; ?>

                                    <div class="match-team <?= $p1_win ? 'winner' : '' ?>">
                                        <span class="team-name"><?= htmlspecialchars($nombre1) ?></span>
                                        <?php if ($jugado): ?>
                                            <span class="team-score">
                                                <?= $partido['set1_p1'] ?>-<?= $partido['set1_p2'] ?>
                                                <?php if ($partido['set2_p1'] !== null): ?> / <?= $partido['set2_p1'] ?>-<?= $partido['set2_p2'] ?><?php endif; ?>
                                                <?php if ($partido['set3_p1'] !== null): ?> / <?= $partido['set3_p1'] ?>-<?= $partido['set3_p2'] ?><?php endif; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="team-score" style="color:#bbb;">Pendiente</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="match-divider"></div>
                                    <div class="match-team <?= $p2_win ? 'winner' : '' ?>">
                                        <span class="team-name"><?= htmlspecialchars($nombre2) ?></span>
                                        <?php if ($jugado): ?>
                                            <span class="team-score">
                                                <?= $partido['set1_p2'] ?>-<?= $partido['set1_p1'] ?>
                                                <?php if ($partido['set2_p1'] !== null): ?> / <?= $partido['set2_p2'] ?>-<?= $partido['set2_p1'] ?><?php endif; ?>
                                                <?php if ($partido['set3_p1'] !== null): ?> / <?= $partido['set3_p2'] ?>-<?= $partido['set3_p1'] ?><?php endif; ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                </div>

                            <?php endforeach; ?>
                        </div>
                    </div>

                <?php endforeach; ?>

            </div>

            <!-- =============================
                    CRUCES / PLAYOFF
            ============================= -->

            <div id="tab-playoff" class="fixture-tab-content" style="display:none;">

                <?php if (empty($playoff)): ?>
                    <p class="bracket-empty">No hay cruces generados aún.</p>
                <?php else: ?>

                <?php
                $rondas     = array_values($playoff);
                $total_cols = count($rondas);
                ?>

                <div class="playoff-grid">
                <?php foreach ($rondas as $r_idx => $rondaData):
                    $es_ultima = ($r_idx === $total_cols - 1);
                    $groups    = array_chunk($rondaData['partidos'], 2);
                ?>
                    <div class="pg-col">
                        <div class="pg-col-header"><?= htmlspecialchars($rondaData['nombre']) ?></div>

                        <?php foreach ($groups as $group):
                            $single = (count($group) === 1);
                        ?>
                        <div class="pg-pair <?= $single ? 'pg-single' : '' ?> <?= $es_ultima ? 'pg-last' : '' ?>">

                            <?php foreach ($group as $p):
                                $p1w    = $p->ganador_id && $p->ganador_id == $p->pareja1_id;
                                $p2w    = $p->ganador_id && $p->ganador_id == $p->pareja2_id;
                                $p1n    = $p->pareja1_nombre ?: ($p->referencia1 ?: '?');
                                $p2n    = $p->pareja2_nombre ?: ($p->referencia2 ?: '?');
                                $jugado = $p->set1_p1 !== null;
                                $footer = '';
                                if (!empty($p->cancha)) $footer .= 'C'.$p->cancha;
                                if (!empty($p->fecha))  $footer .= ($footer?' ':'').date('d/m/Y', strtotime($p->fecha));
                            ?>
                            <div class="pg-slot">
                                <div class="pg-match admin-clickable"
                                     data-partido-id="<?= $p->id ?>"
                                     onclick="abrirModalPartido(this)"
                                     title="Cargar resultado">
                                    <div class="pg-match-id"><?= $p->id ?></div>

                                    <div class="pg-team <?= $p1w ? 'pg-winner' : '' ?>">
                                        <span class="pg-seed"><?= htmlspecialchars($p->referencia1 ?: '-') ?></span>
                                        <span class="pg-name"><?= htmlspecialchars($p1n) ?></span>
                                        <div class="pg-scores">
                                            <?php if ($jugado): ?>
                                                <span class="pg-score-cell"><?= $p->set1_p1 ?></span>
                                                <?php if ($p->set2_p1 !== null): ?><span class="pg-score-cell"><?= $p->set2_p1 ?></span><?php endif; ?>
                                                <?php if ($p->set3_p1 !== null): ?><span class="pg-score-cell"><?= $p->set3_p1 ?></span><?php endif; ?>
                                            <?php else: ?>
                                                <span class="pg-score-cell"></span><span class="pg-score-cell"></span><span class="pg-score-cell"></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="pg-divider"></div>

                                    <div class="pg-team <?= $p2w ? 'pg-winner' : '' ?>">
                                        <span class="pg-seed"><?= htmlspecialchars($p->referencia2 ?: '-') ?></span>
                                        <span class="pg-name"><?= htmlspecialchars($p2n) ?></span>
                                        <div class="pg-scores">
                                            <?php if ($jugado): ?>
                                                <span class="pg-score-cell"><?= $p->set1_p2 ?></span>
                                                <?php if ($p->set2_p1 !== null): ?><span class="pg-score-cell"><?= $p->set2_p2 ?></span><?php endif; ?>
                                                <?php if ($p->set3_p1 !== null): ?><span class="pg-score-cell"><?= $p->set3_p2 ?></span><?php endif; ?>
                                            <?php else: ?>
                                                <span class="pg-score-cell"></span><span class="pg-score-cell"></span><span class="pg-score-cell"></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if ($footer): ?>
                                    <div class="pg-footer"><?= htmlspecialchars($footer) ?></div>
                                    <?php endif; ?>
                                </div>
                                <button class="btn-editar-pareja"
                                        data-id="<?= $p->id ?>"
                                        data-p1="<?= (int)$p->pareja1_id ?>"
                                        data-p2="<?= (int)$p->pareja2_id ?>"
                                        onclick="abrirModalParejaPlayoff(this)"
                                        title="Editar parejas del cruce">✏️ Parejas</button>
                            </div>
                            <?php endforeach; ?>

                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                </div>

                <?php endif; ?>

            </div>

            <!-- MODAL EDITAR PAREJAS PLAYOFF -->
            <div id="modal-pareja-playoff" class="modal-overlay" style="display:none;" onclick="cerrarModalParejaPlayoff(event)">
                <div class="modal-box">
                    <h3>Editar parejas del cruce</h3>
                    <input type="hidden" id="mpp-partido-id">

                    <label>Pareja 1</label>
                    <select id="mpp-pareja1">
                        <option value="">— Sin asignar —</option>
                        <?php foreach($inscriptos_con_seed as $ins): ?>
                            <option value="<?= $ins->id ?>">
                                <?php
                                $prefijo = $ins->codigo ? $ins->codigo.' - ' : '';
                                echo htmlspecialchars($prefijo.$ins->apellido1.' '.$ins->nombre1.' / '.$ins->apellido2.' '.$ins->nombre2);
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Pareja 2</label>
                    <select id="mpp-pareja2">
                        <option value="">— Sin asignar —</option>
                        <?php foreach($inscriptos_con_seed as $ins): ?>
                            <option value="<?= $ins->id ?>">
                                <?php
                                $prefijo = $ins->codigo ? $ins->codigo.' - ' : '';
                                echo htmlspecialchars($prefijo.$ins->apellido1.' '.$ins->nombre1.' / '.$ins->apellido2.' '.$ins->nombre2);
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="modal-actions" style="margin-top:12px;display:flex;gap:8px;">
                        <button class="btn btn-primary" onclick="guardarParejaPlayoff()">Guardar</button>
                        <button class="btn btn-secondary" onclick="cerrarModalParejaPlayoff()">Cancelar</button>
                    </div>
                    <p id="mpp-msg" style="margin-top:8px;font-size:13px;color:green;display:none;"></p>
                </div>
            </div>

            <script>
            function abrirModalParejaPlayoff(btn) {
                document.getElementById('mpp-partido-id').value = btn.dataset.id;
                document.getElementById('mpp-pareja1').value    = btn.dataset.p1 || '';
                document.getElementById('mpp-pareja2').value    = btn.dataset.p2 || '';
                document.getElementById('mpp-msg').style.display = 'none';
                document.getElementById('modal-pareja-playoff').style.display = 'flex';
            }
            function cerrarModalParejaPlayoff(e) {
                if (!e || e.target.id === 'modal-pareja-playoff') {
                    document.getElementById('modal-pareja-playoff').style.display = 'none';
                }
            }
            function guardarParejaPlayoff() {
                const id = document.getElementById('mpp-partido-id').value;
                const p1 = document.getElementById('mpp-pareja1').value;
                const p2 = document.getElementById('mpp-pareja2').value;
                const msg = document.getElementById('mpp-msg');

                fetch('<?= base_url('admin/Torneos/editar_pareja_playoff') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'partido_id='+id+'&pareja1_id='+p1+'&pareja2_id='+p2
                })
                .then(r => r.json())
                .then(data => {
                    if (data.ok) {
                        msg.textContent = 'Guardado correctamente.';
                        msg.style.color = 'green';
                        msg.style.display = 'block';
                        setTimeout(() => location.reload(), 800);
                    } else {
                        msg.textContent = data.msg || 'Error al guardar.';
                        msg.style.color = 'red';
                        msg.style.display = 'block';
                    }
                });
            }
            </script>

            <!-- =============================
                    LISTADO DE PARTIDOS
            ============================= -->

            <div id="tab-listado" class="fixture-tab-content" style="display:none;">

                <div class="config-card">

                    <h3>Listado de Partidos</h3>

                    <?php
                    $dias_es = ['Sunday'=>'Domingo','Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miércoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sábado'];

                    // Reunir fechas únicas (solo YYYY-MM-DD)
                    $fechas_unicas = [];
                    foreach ($todos_partidos as $p) {
                        if ($p->fecha) {
                            $fechas_unicas[substr($p->fecha, 0, 10)] = true;
                        }
                    }
                    ksort($fechas_unicas);
                    $fechas_unicas = array_keys($fechas_unicas);
                    ?>

                    <?php if (!empty($todos_partidos)): ?>

                        <div class="listado-filtro">
                            <label>Filtrar por día:</label>
                            <div class="listado-dias">
                                <button class="btn-dia active" data-fecha="">Todos</button>
                                <?php foreach ($fechas_unicas as $fecha): ?>
                                    <button class="btn-dia" data-fecha="<?= $fecha ?>">
                                        <?= date('d/m/Y', strtotime($fecha)) ?>
                                        <span><?= $dias_es[date('l', strtotime($fecha))] ?? '' ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="listado-cards" id="listado-tbody">
                                <?php foreach ($todos_partidos as $p):
                                    $jugado = $p->set1_p1 !== null;

                                    if ($p->zona_numero) {
                                        $zona_label = 'Zona ' . chr(64 + $p->zona_numero);
                                    } else {
                                        $nombres_ronda = [1 => 'Reclasificación', 2 => 'Cuartos', 3 => 'Semifinal', 4 => 'Final'];
                                        $zona_label = $nombres_ronda[$p->ronda] ?? 'Playoff';
                                    }

                                    $hora_display = $p->hora ? substr($p->hora, 0, 5) : '-';
                                    $fecha_attr   = $p->fecha ? substr($p->fecha, 0, 10) : '';
                                    $dia_display  = '';
                                    if ($fecha_attr) {
                                        $dia_en = date('l', strtotime($fecha_attr));
                                        $dia_display = ($dias_es[$dia_en] ?? $dia_en) . ' ' . date('d/m', strtotime($fecha_attr));
                                    }
                                ?>
                                <div class="listado-card listado-row" data-fecha="<?= $fecha_attr ?>">
                                    <div class="listado-card-header">
                                        <div class="listado-card-tiempo">
                                            <?php if ($dia_display): ?><span class="listado-card-dia"><?= $dia_display ?></span><?php endif; ?>
                                            <span class="listado-card-hora"><?= $hora_display ?></span>
                                        </div>
                                        <?php if ($p->cancha): ?><span class="listado-card-cancha">Cancha <?= $p->cancha ?></span><?php endif; ?>
                                    </div>
                                    <div class="listado-card-tags">
                                        <span class="listado-zona"><?= $zona_label ?></span>
                                        <?php if ($p->categoria_nombre): ?>
                                            <span class="listado-cat"><?= htmlspecialchars($p->categoria_nombre) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="listado-card-match">
                                        <span class="listado-card-pareja"><?= htmlspecialchars($p->pareja1_nombre ?? '-') ?></span>
                                        <div class="listado-card-result">
                                            <?php if ($jugado): ?>
                                                <span class="set-badge"><?= $p->set1_p1 ?>-<?= $p->set1_p2 ?></span>
                                                <?php if ($p->set2_p1 !== null): ?><span class="set-badge"><?= $p->set2_p1 ?>-<?= $p->set2_p2 ?></span><?php endif; ?>
                                                <?php if ($p->set3_p1 !== null): ?><span class="set-badge"><?= $p->set3_p1 ?>-<?= $p->set3_p2 ?></span><?php endif; ?>
                                            <?php else: ?>
                                                <span class="listado-vs">VS</span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="listado-card-pareja"><?= htmlspecialchars($p->pareja2_nombre ?? '-') ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                        <p class="listado-sin-resultados" id="listado-vacio" style="display:none;">
                            No hay partidos para el día seleccionado.
                        </p>

                    <?php else: ?>
                        <p class="config-hint">No hay partidos generados aún.</p>
                    <?php endif; ?>

                </div>

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
    // restaurar campos si estaban readonly
    ['dia','hora','cancha'].forEach(id => {
        document.getElementById(id).removeAttribute('readonly');
        document.getElementById(id).style.background = '';
        document.getElementById(id).style.color = '';
    });
    document.getElementById('modalPartido').style.display = 'none';
}

function abrirModalResultado(el)
{
    const partidoId = el.dataset.partidoId;

    fetch("<?= base_url('admin/torneos/obtener_partido') ?>/" + partidoId)
        .then(r => r.json())
        .then(data => {
            document.getElementById('partido_id').value = data.id;
            document.getElementById('dia').value    = data.fecha  ? data.fecha.slice(0, 10) : '';
            document.getElementById('hora').value   = data.hora   ? data.hora.slice(0, 5)   : '';
            document.getElementById('cancha').value = data.cancha ?? '';
            document.getElementById('set_1').value  = data.set_1  ?? '';
            document.getElementById('set_2').value  = data.set_2  ?? '';
            document.getElementById('set_3').value  = data.set_3  ?? '';

            // fecha/hora/cancha solo lectura
            ['dia','hora','cancha'].forEach(id => {
                const f = document.getElementById(id);
                f.setAttribute('readonly', 'readonly');
                f.style.background    = '#f4f6f8';
                f.style.color         = '#888';
                f.style.pointerEvents = 'none';
            });

            document.getElementById('modalPartido').style.display = 'flex';
        });
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

function abrirModalInscripto(id, n1, ap1, tel1, n2, ap2, tel2)
{
    document.getElementById('ei_id').value       = id;
    document.getElementById('ei_nombre1').value  = n1;
    document.getElementById('ei_apellido1').value = ap1;
    document.getElementById('ei_telefono1').value = tel1;
    document.getElementById('ei_nombre2').value  = n2;
    document.getElementById('ei_apellido2').value = ap2;
    document.getElementById('ei_telefono2').value = tel2;
    document.getElementById('modalInscripto').style.display = 'flex';
}

function cerrarModalInscripto()
{
    document.getElementById('modalInscripto').style.display = 'none';
}

document.getElementById('formEditarInscripto')
.addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fetch("<?= base_url('admin/torneos/editar_inscripcion') ?>", {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(resp => {
        if (resp.ok) {
            cerrarModalInscripto();
            location.reload();
        } else {
            alert('Error al guardar: ' + (resp.error ?? ''));
        }
    });
});

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

// ---- Listado de partidos: filtro por día ----
document.querySelectorAll('.btn-dia').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.btn-dia').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        var fechaFiltro = this.dataset.fecha;
        var rows        = document.querySelectorAll('#listado-tbody .listado-row');
        var visibles    = 0;

        rows.forEach(function(row) {
            var mostrar = !fechaFiltro || row.dataset.fecha === fechaFiltro;
            row.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        var vacio = document.getElementById('listado-vacio');
        if (vacio) vacio.style.display = visibles === 0 ? '' : 'none';
    });
});
</script>
