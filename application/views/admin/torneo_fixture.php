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
                                        <th>Disponibilidad</th>
                                        <th>Zona</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inscripciones_zona as $idx => $insc): ?>
                                    <tr>
                                        <td><?= $idx + 1 ?></td>
                                        <td><?= htmlspecialchars($insc->pareja_nombre) ?></td>
                                        <td>
                                            <?php if (!empty($insc->disponibilidad)): ?>
                                                <span class="badge-disponibilidad"><?= htmlspecialchars($insc->disponibilidad) ?></span>
                                            <?php else: ?>
                                                <span class="sin-disponibilidad">—</span>
                                            <?php endif; ?>
                                        </td>
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

                        <!-- Paso 1: submit vive dentro del form guardar_zonas -->
                        <div class="config-pasos">
                            <div class="config-paso">
                                <div class="config-paso-num">1</div>
                                <div class="config-paso-body">
                                    <div class="config-paso-label">Guardar zonas</div>
                                    <div class="config-paso-hint">Asigná cada pareja a su zona y guardá.</div>
                                    <button type="submit" class="btn-guardar-config">Guardar configuración</button>
                                </div>
                            </div>

                            <div class="config-paso-arrow">→</div>

                            <!-- Paso 2: referencia form externo via HTML5 form= -->
                            <div class="config-paso <?= empty($zonas_db) ? 'config-paso-disabled' : '' ?>">
                                <div class="config-paso-num">2</div>
                                <div class="config-paso-body">
                                    <div class="config-paso-label">Generar partidos</div>
                                    <div class="config-paso-hint">Crea todos los partidos de zona automáticamente.</div>
                                    <button type="submit" form="form-generar-partidos"
                                            class="btn-generar-partidos"
                                            <?= empty($zonas_db) ? 'disabled' : '' ?>>⚙ Generar partidos</button>
                                </div>
                            </div>
                        </div>

                    </form>

                    <!-- Form 2 sin botón propio, el botón del Paso 2 lo referencia con form= -->
                    <form id="form-generar-partidos"
                          method="post"
                          action="<?= base_url('admin/Torneos/generar_partidos/'.$torneo->id) ?>"
                          onsubmit="return confirm('¿Generar partidos? Se eliminarán los partidos actuales.')">
                        <input type="hidden" name="categoria_id" value="<?= $categoria_id ?>">
                    </form>

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
                                    <th>Disponibilidad</th>
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
                                        <input type="text"
                                               class="input-disponibilidad"
                                               data-id="<?= $insc->id ?>"
                                               value="<?= htmlspecialchars($insc->disponibilidad ?? '') ?>"
                                               placeholder="Ej: Sábados tarde">
                                    </td>
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
                                            <div class="set-boxes">
                                                <span class="set-box <?= $partido['set1_p1'] > $partido['set1_p2'] ? 'win' : '' ?>"><?= $partido['set1_p1'] ?></span>
                                                <?php if ($partido['set2_p1'] !== null): ?>
                                                    <span class="set-box <?= $partido['set2_p1'] > $partido['set2_p2'] ? 'win' : '' ?>"><?= $partido['set2_p1'] ?></span>
                                                <?php endif; ?>
                                                <?php if ($partido['set3_p1'] !== null): ?>
                                                    <span class="set-box <?= $partido['set3_p1'] > $partido['set3_p2'] ? 'win' : '' ?>"><?= $partido['set3_p1'] ?></span>
                                                <?php endif; ?>
                                                <span class="set-box set-box-final set-box-final-<?= $sets_p1 ?> <?= $p1_win ? 'win' : '' ?>"><?= $sets_p1 ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="team-score" style="color:#bbb;">Pendiente</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="match-divider"></div>
                                    <div class="match-team <?= $p2_win ? 'winner' : '' ?>">
                                        <span class="team-name"><?= htmlspecialchars($nombre2) ?></span>
                                        <?php if ($jugado): ?>
                                            <div class="set-boxes">
                                                <span class="set-box <?= $partido['set1_p2'] > $partido['set1_p1'] ? 'win' : '' ?>"><?= $partido['set1_p2'] ?></span>
                                                <?php if ($partido['set2_p1'] !== null): ?>
                                                    <span class="set-box <?= $partido['set2_p2'] > $partido['set2_p1'] ? 'win' : '' ?>"><?= $partido['set2_p2'] ?></span>
                                                <?php endif; ?>
                                                <?php if ($partido['set3_p1'] !== null): ?>
                                                    <span class="set-box <?= $partido['set3_p2'] > $partido['set3_p1'] ? 'win' : '' ?>"><?= $partido['set3_p2'] ?></span>
                                                <?php endif; ?>
                                                <span class="set-box set-box-final set-box-final-<?= $sets_p2 ?> <?= $p2_win ? 'win' : '' ?>"><?= $sets_p2 ?></span>
                                            </div>
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
                $rondas    = array_values($playoff);
                $numRounds = count($rondas);

                // Numeración correlativa: partido_id => "Partido N"
                $_n = 1; $partido_num = [];
                foreach ($rondas as $_r) {
                    foreach ($_r['partidos'] as $_p) { $partido_num[$_p->id] = $_n++; }
                }

                // Mapa inverso: [partido_siguiente_id][slot] => partido_id fuente
                $feeder_map = [];
                foreach ($rondas as $_r) {
                    foreach ($_r['partidos'] as $_p) {
                        if ($_p->partido_siguiente_id) {
                            $feeder_map[$_p->partido_siguiente_id][(int)$_p->slot_siguiente] = $_p->id;
                        }
                    }
                }

                // ---- Cálculo de slots para alineación correcta del bracket ----
                // Cada ronda tiene un número fijo de slots (potencia de 2).
                // Los slots vacíos se muestran como "bye" (gris) para mantener alineación.
                $slotMap    = [];
                $roundSizes = [];

                // Última ronda: slots 1..N
                foreach ($rondas[$numRounds - 1]['partidos'] as $i => $m) {
                    $slotMap[$m->id] = $i + 1;
                }
                $roundSizes[$numRounds - 1] = count($rondas[$numRounds - 1]['partidos']);

                // Recorrer hacia atrás asignando slots
                for ($ri = $numRounds - 2; $ri >= 0; $ri--) {
                    $nextSz   = $roundSizes[$ri + 1];
                    $thisCnt  = count($rondas[$ri]['partidos']);
                    // Si esta ronda tiene el doble de partidos que la siguiente → duplica slots
                    $isDouble = ($thisCnt === $nextSz * 2);
                    $roundSizes[$ri] = $isDouble ? $nextSz * 2 : $nextSz;
                    $total_en_ronda = count($rondas[$ri]['partidos']);
                    foreach ($rondas[$ri]['partidos'] as $i => $m) {
                        if ($m->partido_siguiente_id && isset($slotMap[$m->partido_siguiente_id])) {
                            $ps = $slotMap[$m->partido_siguiente_id];
                            $slotMap[$m->id] = $isDouble
                                ? ($ps - 1) * 2 + (int)$m->slot_siguiente
                                : $ps;
                        } else {
                            // Fallback: en rondas no-doble el último partido va al último slot
                            // (formato APA: Reclasif match 1→slot 1, match 2→slot N)
                            if (!$isDouble && $i === $total_en_ronda - 1) {
                                $slotMap[$m->id] = $roundSizes[$ri];
                            } else {
                                $slotMap[$m->id] = $i + 1;
                            }
                        }
                    }
                }

                // Construir grilla de slots y flags de transición
                $slotGrid = []; $isDoubleTrans = [];
                for ($ri = 0; $ri < $numRounds; $ri++) {
                    $sz = $roundSizes[$ri];
                    $slotGrid[$ri] = array_fill(1, $sz, null);
                    foreach ($rondas[$ri]['partidos'] as $m) {
                        if (isset($slotMap[$m->id])) $slotGrid[$ri][$slotMap[$m->id]] = $m;
                    }
                    $isDoubleTrans[$ri] = ($ri < $numRounds - 1) && ($roundSizes[$ri] === $roundSizes[$ri + 1] * 2);
                }
                ?>

                <div class="bracket-controls">
                    <button class="bracket-ctrl-btn" onclick="bZoomOut('admin-playoff-bracket')">− Zoom</button>
                    <button class="bracket-ctrl-btn" onclick="bZoomReset('admin-playoff-bracket')">↺</button>
                    <button class="bracket-ctrl-btn" onclick="bZoomIn('admin-playoff-bracket')">+ Zoom</button>
                    <button class="bracket-ctrl-btn bracket-ctrl-landscape" onclick="bFsOpen('admin-playoff-bracket','admin-bracket-fs')">⤢ Horizontal</button>
                </div>

                <!-- Overlay modo horizontal (rotado 90°) -->
                <div id="admin-bracket-fs" class="bracket-fs-overlay">
                    <div class="bracket-fs-bar">
                        <span class="fs-title">Cruces</span>
                        <button class="fs-close" onclick="bFsClose('admin-playoff-bracket','admin-bracket-fs')">✕ Cerrar</button>
                    </div>
                    <div class="bracket-fs-content"></div>
                </div>

                <div class="playoff-grid" id="admin-playoff-bracket">
                <?php foreach ($rondas as $ri => $rondaData):
                    $es_ultima = ($ri === $numRounds - 1);
                    $colClass  = $isDoubleTrans[$ri] ? 'col-feeds-double' : 'col-feeds-same';
                ?>
                    <div class="pg-col <?= $colClass ?> <?= $es_ultima ? 'pg-last-col' : '' ?>">
                        <div class="pg-col-header"><?= htmlspecialchars($rondaData['nombre']) ?></div>
                        <div class="pg-slots">
                        <?php for ($s = 1; $s <= $roundSizes[$ri]; $s++):
                            $p = $slotGrid[$ri][$s];
                        ?>
                            <div class="pg-slot-fixed <?= $p ? '' : 'pg-slot-bye' ?>">
                            <?php if ($p):
                                $p1w = $p->ganador_id && $p->ganador_id == $p->pareja1_id;
                                $p2w = $p->ganador_id && $p->ganador_id == $p->pareja2_id;
                                $p1_real = $p->pareja1_nombre && strlen(trim(str_replace('/', '', $p->pareja1_nombre))) > 1;
                                $p2_real = $p->pareja2_nombre && strlen(trim(str_replace('/', '', $p->pareja2_nombre))) > 1;
                                if ($p1_real) {
                                    $p1n = $p->pareja1_nombre;
                                } elseif (isset($feeder_map[$p->id][1])) {
                                    $p1n = 'Ganador Partido ' . $partido_num[$feeder_map[$p->id][1]];
                                } elseif ($p->referencia1) {
                                    $p1n = $p->referencia1;
                                } else {
                                    $p1n = 'Por definir';
                                }
                                if ($p2_real) {
                                    $p2n = $p->pareja2_nombre;
                                } elseif (isset($feeder_map[$p->id][2])) {
                                    $p2n = 'Ganador Partido ' . $partido_num[$feeder_map[$p->id][2]];
                                } elseif ($p->referencia2) {
                                    $p2n = $p->referencia2;
                                } else {
                                    $p2n = 'Por definir';
                                }
                                $jugado = $p->set1_p1 !== null;
                                $pg_s1 = 0; $pg_s2 = 0;
                                if ($jugado) {
                                    if ($p->set1_p1 > $p->set1_p2) $pg_s1++; else $pg_s2++;
                                    if ($p->set2_p1 !== null) { if ($p->set2_p1 > $p->set2_p2) $pg_s1++; else $pg_s2++; }
                                    if ($p->set3_p1 !== null) { if ($p->set3_p1 > $p->set3_p2) $pg_s1++; else $pg_s2++; }
                                }
                                $footer = '';
                                if (!empty($p->hora))   $footer .= substr($p->hora, 0, 5).'h';
                                if (!empty($p->cancha)) $footer .= ($footer?' ':'').'C'.$p->cancha;
                                if (!empty($p->fecha))  $footer .= ($footer?' ':'').date('d/m', strtotime($p->fecha));
                            ?>
                                <div class="pg-match admin-clickable"
                                     data-partido-id="<?= $p->id ?>"
                                     onclick="abrirModalPartido(this)"
                                     title="Cargar resultado">
                                    <div class="pg-match-id">Partido <?= $partido_num[$p->id] ?></div>
                                    <div class="pg-team <?= $p1w ? 'pg-winner' : '' ?>">
                                        <?php if ($p1_real && $p->referencia1): ?>
                                            <span class="pg-seed"><?= htmlspecialchars($p->referencia1) ?></span>
                                        <?php endif; ?>
                                        <span class="pg-name"><?= htmlspecialchars($p1n) ?></span>
                                        <div class="pg-scores">
                                            <?php if ($jugado): ?>
                                                <span class="pg-score-cell"><?= $p->set1_p1 ?></span>
                                                <?php if ($p->set2_p1 !== null): ?><span class="pg-score-cell"><?= $p->set2_p1 ?></span><?php endif; ?>
                                                <?php if ($p->set3_p1 !== null): ?><span class="pg-score-cell"><?= $p->set3_p1 ?></span><?php endif; ?>
                                                <span class="pg-score-cell pg-score-final <?= $pg_s1 > $pg_s2 ? 'win' : '' ?>"><?= $pg_s1 ?></span>
                                            <?php else: ?>
                                                <span class="pg-score-cell"></span><span class="pg-score-cell"></span><span class="pg-score-cell"></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="pg-divider"></div>
                                    <div class="pg-team <?= $p2w ? 'pg-winner' : '' ?>">
                                        <?php if ($p2_real && $p->referencia2): ?>
                                            <span class="pg-seed"><?= htmlspecialchars($p->referencia2) ?></span>
                                        <?php endif; ?>
                                        <span class="pg-name"><?= htmlspecialchars($p2n) ?></span>
                                        <div class="pg-scores">
                                            <?php if ($jugado): ?>
                                                <span class="pg-score-cell"><?= $p->set1_p2 ?></span>
                                                <?php if ($p->set2_p1 !== null): ?><span class="pg-score-cell"><?= $p->set2_p2 ?></span><?php endif; ?>
                                                <?php if ($p->set3_p1 !== null): ?><span class="pg-score-cell"><?= $p->set3_p2 ?></span><?php endif; ?>
                                                <span class="pg-score-cell pg-score-final <?= $pg_s2 > $pg_s1 ? 'win' : '' ?>"><?= $pg_s2 ?></span>
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
                            <?php else: ?>
                                <div class="pg-bye"></div>
                            <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                        </div>
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

                    <div class="listado-header-row">
                        <h3>Listado de Partidos</h3>
                        <button class="btn-outline btn-sm" id="btn-toggle-bulk">Programar horarios</button>
                    </div>

                    <!-- ===== BULK SCHEDULE SECTION ===== -->
                    <div id="bulk-horarios-wrap" style="display:none;margin-bottom:20px;">
                        <div class="bulk-horarios-header">
                            <span class="bulk-horarios-hint">Editá fecha, hora y cancha de varios partidos a la vez. Dejá en blanco para no modificar.</span>
                            <div class="bulk-horarios-actions">
                                <button class="btn-outline btn-sm" id="btn-bulk-fill">Aplicar a todos</button>
                                <button class="btn-guardar" id="btn-guardar-bulk">Guardar cambios</button>
                            </div>
                        </div>

                        <div class="bulk-fill-row" id="bulk-fill-row" style="display:none;">
                            <label>Fecha: <input type="date" id="fill-fecha" class="bulk-input"></label>
                            <label>Hora: <input type="time" id="fill-hora" class="bulk-input" step="1800"></label>
                            <label>Cancha: <input type="number" id="fill-cancha" class="bulk-input" min="1" max="30" placeholder="#"></label>
                            <button class="btn-guardar btn-sm" id="btn-apply-fill">Aplicar</button>
                        </div>

                        <div class="bulk-table-wrap">
                            <table class="bulk-table">
                                <colgroup>
                                    <col class="col-zona">
                                    <col class="col-cat">
                                    <col class="col-match">
                                    <col class="col-fecha">
                                    <col class="col-hora">
                                    <col class="col-cancha">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>Zona</th>
                                        <th>Categoría</th>
                                        <th>Partido</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Cancha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($todos_partidos as $p):
                                    $jugado_b = $p->set1_p1 !== null;
                                    if ($p->zona_numero) {
                                        $zona_lbl = 'Zona ' . chr(64 + $p->zona_numero);
                                    } else {
                                        $nombres_ronda_b = [1=>'Reclas.',2=>'Cuartos',3=>'Semifinal',4=>'Final'];
                                        $zona_lbl = $nombres_ronda_b[$p->ronda] ?? 'Playoff';
                                    }
                                    $bf = $p->fecha  ? substr($p->fecha, 0, 10) : '';
                                    $bh = $p->hora   ? substr($p->hora,  0,  5) : '';
                                    $bc = $p->cancha ?? '';
                                ?>
                                <tr data-id="<?= $p->partido_id ?>" class="bulk-row<?= $jugado_b ? ' bulk-jugado' : '' ?>">
                                    <td><?= $zona_lbl ?></td>
                                    <td><?= htmlspecialchars($p->categoria_nombre ?? '') ?></td>
                                    <td class="bulk-td-match">
                                        <span><?= htmlspecialchars($p->pareja1_nombre ?? '(por definir)') ?></span>
                                        <em class="bulk-vs">vs</em>
                                        <span><?= htmlspecialchars($p->pareja2_nombre ?? '(por definir)') ?></span>
                                    </td>
                                    <td><input type="date" class="bulk-input bulk-fecha" value="<?= $bf ?>"></td>
                                    <td><input type="time" class="bulk-input bulk-hora" value="<?= $bh ?>" step="1800"></td>
                                    <td><input type="number" class="bulk-input bulk-cancha" min="1" max="30" placeholder="#" value="<?= $bc ?>"></td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div id="bulk-msg" style="display:none;margin-top:10px;padding:8px 12px;border-radius:6px;font-size:13px;font-weight:600;"></div>
                    </div>

                    <!-- ══ PANEL DE AVISOS ══════════════════════════════════ -->
                    <div class="avisos-panel">
                        <div class="avisos-panel-header">
                            <span class="avisos-panel-title">⚠️ Avisos de atraso</span>
                            <span class="avisos-panel-sub">Se envían como notificación push y se muestran en la plataforma</span>
                        </div>

                        <form id="form-aviso" class="avisos-form">
                            <input type="hidden" name="torneo_id" value="<?= $torneo->id ?>">
                            <div class="avisos-form-row">
                                <div class="avisos-form-group">
                                    <label>Cancha</label>
                                    <input type="number" name="cancha" min="1" max="30" placeholder="Opcional" class="avisos-input-sm">
                                </div>
                                <div class="avisos-form-group avisos-form-group-grow">
                                    <label>Mensaje</label>
                                    <input type="text" name="mensaje" placeholder="Ej: Se atrasa 1 hora por lluvia" required class="avisos-input">
                                </div>
                                <div class="avisos-form-group">
                                    <label>Duración (hs)</label>
                                    <input type="number" name="horas" value="2" min="0.5" max="24" step="0.5" class="avisos-input-sm">
                                </div>
                                <div class="avisos-form-group avisos-form-group-btn">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn-aviso-crear">Publicar aviso</button>
                                </div>
                            </div>
                        </form>

                        <div id="avisos-lista">
                        <?php foreach ($avisos as $av): ?>
                            <div class="aviso-item <?= $av->activo ? 'aviso-activo' : 'aviso-expirado' ?>" data-id="<?= $av->id ?>">
                                <div class="aviso-item-info">
                                    <?php if ($av->cancha): ?>
                                        <span class="aviso-cancha-badge">Cancha <?= $av->cancha ?></span>
                                    <?php endif; ?>
                                    <span class="aviso-mensaje"><?= htmlspecialchars($av->mensaje) ?></span>
                                    <span class="aviso-expira">
                                        <?= $av->activo ? 'Expira: ' . date('d/m H:i', strtotime($av->expira_at)) : 'Expirado' ?>
                                    </span>
                                </div>
                                <div class="aviso-item-actions">
                                    <button class="btn-aviso-wpp" onclick="compartirAviso(<?= $av->id ?>, '<?= addslashes($av->mensaje) ?>', <?= $av->cancha ?: 'null' ?>)" title="Compartir por WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </button>
                                    <button class="btn-aviso-del" onclick="eliminarAviso(<?= $av->id ?>)" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($avisos)): ?>
                            <p class="avisos-empty" id="avisos-empty">No hay avisos publicados aún.</p>
                        <?php endif; ?>
                        </div>
                    </div>
                    <!-- ════════════════════════════════════════════════════ -->

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
                            <button class="btn-compartir-filtro" id="btn-compartir-filtro" title="Compartir partidos con filtro aplicado">
                                <i class="fab fa-whatsapp"></i> Compartir
                            </button>
                        </div>

                        <div class="listado-cards" id="listado-tbody">
                                <?php foreach ($todos_partidos as $p):
                                    $jugado = $p->set1_p1 !== null;
                                    $ls1 = 0; $ls2 = 0;
                                    if ($jugado) {
                                        if ($p->set1_p1 > $p->set1_p2) $ls1++; else $ls2++;
                                        if ($p->set2_p1 !== null) { if ($p->set2_p1 > $p->set2_p2) $ls1++; else $ls2++; }
                                        if ($p->set3_p1 !== null) { if ($p->set3_p1 > $p->set3_p2) $ls1++; else $ls2++; }
                                    }

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
                                                <span class="set-badge set-badge-final"><?= $ls1 ?>-<?= $ls2 ?></span>
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

            <input type="hidden" name="set_1" id="set_1">
            <input type="hidden" name="set_2" id="set_2">
            <input type="hidden" name="set_3" id="set_3">

            <div class="set-input-grid">
                <div class="set-grid-team-col">
                    <div class="set-grid-header"></div>
                    <div class="set-grid-team-label">Pareja 1</div>
                    <div class="set-grid-team-label">Pareja 2</div>
                </div>
                <div class="set-grid-set-col">
                    <div class="set-grid-header">Set 1</div>
                    <input type="number" class="set-num-input" id="s1p1" min="0" max="7" placeholder="–">
                    <input type="number" class="set-num-input" id="s1p2" min="0" max="7" placeholder="–">
                </div>
                <div class="set-grid-set-col">
                    <div class="set-grid-header">Set 2</div>
                    <input type="number" class="set-num-input" id="s2p1" min="0" max="7" placeholder="–">
                    <input type="number" class="set-num-input" id="s2p2" min="0" max="7" placeholder="–">
                </div>
                <div class="set-grid-set-col">
                    <div class="set-grid-header">Set 3</div>
                    <input type="number" class="set-num-input" id="s3p1" min="0" max="7" placeholder="–">
                    <input type="number" class="set-num-input" id="s3p2" min="0" max="7" placeholder="–">
                </div>
            </div>

            <button type="submit">Guardar</button>
            <button type="button" onclick="cerrarModal()">Cancelar</button>

        </form>

    </div>
</div>


<script>
/* ---- Bracket: zoom & modo horizontal ---- */
(function(){
    var _zm = 1, _origP, _origN;
    var STEP = 0.2, MIN = 0.3, MAX = 3;
    function _el(id){ return document.getElementById(id); }
    window.bZoomIn    = function(id){ _zm = Math.min(MAX, +(_zm+STEP).toFixed(1)); _el(id).style.zoom = _zm; };
    window.bZoomOut   = function(id){ _zm = Math.max(MIN, +(_zm-STEP).toFixed(1)); _el(id).style.zoom = _zm; };
    window.bZoomReset = function(id){ _zm = 1; _el(id).style.zoom = ''; };
    window.bFsOpen = function(bracketId, overlayId){
        var el = _el(bracketId), ov = _el(overlayId);
        _origP = el.parentNode; _origN = el.nextSibling;
        ov.querySelector('.bracket-fs-content').appendChild(el);
        ov.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };
    window.bFsClose = function(bracketId, overlayId){
        var el = _el(bracketId), ov = _el(overlayId);
        if(_origN) _origP.insertBefore(el,_origN); else _origP.appendChild(el);
        ov.style.display = 'none';
        document.body.style.overflow = '';
    };
})();
</script>

<script>
function _parseSet(str) {
    if (!str) return [null, null];
    const p = str.split('-');
    return p.length === 2 ? [p[0], p[1]] : [null, null];
}

function _cargarSetsEnInputs(set1, set2, set3) {
    const [s1p1, s1p2] = _parseSet(set1);
    const [s2p1, s2p2] = _parseSet(set2);
    const [s3p1, s3p2] = _parseSet(set3);
    document.getElementById('s1p1').value = s1p1 ?? '';
    document.getElementById('s1p2').value = s1p2 ?? '';
    document.getElementById('s2p1').value = s2p1 ?? '';
    document.getElementById('s2p2').value = s2p2 ?? '';
    document.getElementById('s3p1').value = s3p1 ?? '';
    document.getElementById('s3p2').value = s3p2 ?? '';
}

function abrirModalPartido(el)
{
    const partidoId = el.dataset.partidoId;

    fetch("<?= base_url('admin/torneos/obtener_partido') ?>/" + partidoId)
        .then(r => r.json())
        .then(data => {
            document.getElementById('partido_id').value = data.id;
            document.getElementById('dia').value    = data.fecha  ? data.fecha.slice(0, 10) : '';
            document.getElementById('hora').value   = data.hora   ?? '';
            document.getElementById('cancha').value = data.cancha ?? '';

            _cargarSetsEnInputs(data.set_1, data.set_2, data.set_3);

            // editable
            ['dia','hora','cancha'].forEach(id => {
                document.getElementById(id).removeAttribute('readonly');
                document.getElementById(id).style.background = '';
                document.getElementById(id).style.color = '';
                document.getElementById(id).style.pointerEvents = '';
            });
            document.querySelectorAll('.set-num-input').forEach(i => {
                i.removeAttribute('readonly');
                i.style.background = '';
                i.style.color = '';
                i.style.pointerEvents = '';
            });

            document.getElementById('modalPartido').style.display = 'flex';
        });
}

function cerrarModal()
{
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

            _cargarSetsEnInputs(data.set_1, data.set_2, data.set_3);

            // fecha/hora/cancha solo lectura
            ['dia','hora','cancha'].forEach(id => {
                const f = document.getElementById(id);
                f.setAttribute('readonly', 'readonly');
                f.style.background    = '#f4f6f8';
                f.style.color         = '#888';
                f.style.pointerEvents = 'none';
            });
            // sets editables (la pestaña resultados SÍ permite editar sets)
            document.querySelectorAll('.set-num-input').forEach(i => {
                i.removeAttribute('readonly');
                i.style.background = '';
                i.style.color = '';
                i.style.pointerEvents = '';
            });

            document.getElementById('modalPartido').style.display = 'flex';
        });
}
</script>

<script>
document.getElementById('formPartido')
.addEventListener('submit', function(e){

    e.preventDefault();

    // Validar resultado de set de pádel
    function isValidPadelSet(a, b) {
        a = parseInt(a, 10); b = parseInt(b, 10);
        if (isNaN(a) || isNaN(b)) return true; // vacío → se ignora
        // 6-x o x-6 con diferencia mínima de 2
        if (a === 6 && b <= 4) return true;
        if (b === 6 && a <= 4) return true;
        // 7-5 o 5-7
        if ((a === 7 && b === 5) || (a === 5 && b === 7)) return true;
        // 7-6 o 6-7
        if ((a === 7 && b === 6) || (a === 6 && b === 7)) return true;
        return false;
    }

    // Combinar inputs numéricos en formato "N-N" para el backend
    function buildSet(p1id, p2id) {
        const v1 = document.getElementById(p1id).value;
        const v2 = document.getElementById(p2id).value;
        return (v1 !== '' && v2 !== '') ? v1 + '-' + v2 : '';
    }
    const s1 = buildSet('s1p1','s1p2');
    const s2 = buildSet('s2p1','s2p2');
    const s3 = buildSet('s3p1','s3p2');

    const setsToCheck = [
        {set: s1, label: 'Set 1'},
        {set: s2, label: 'Set 2'},
        {set: s3, label: 'Set 3'},
    ];
    for (const {set, label} of setsToCheck) {
        if (!set) continue;
        const [a, b] = set.split('-');
        if (!isValidPadelSet(a, b)) {
            alert(`Resultado inválido en ${label}: ${set}.\nResultados válidos: 6-0 a 6-4, 7-5, 7-6 (y sus inversos).`);
            return;
        }
    }

    document.getElementById('set_1').value = s1;
    document.getElementById('set_2').value = s2;
    document.getElementById('set_3').value = s3;

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

    var url = new URL(window.location.href);
    url.searchParams.set('tab', tab);
    if (tab !== 'listado') url.searchParams.delete('fecha');
    history.replaceState(null, '', url.toString());
}

// Restaurar tab activo + filtro de fecha al cargar
(function() {
    var params     = new URLSearchParams(window.location.search);
    var fechaParam = params.get('fecha');
    var tabParam   = params.get('tab') || location.hash.replace('#', '');

    // Si viene fecha pero no tab, ir a listado
    if (fechaParam && !tabParam) tabParam = 'listado';

    const validTabs = ['config','inscriptos','zonas','resultados','playoff','listado'];
    if (tabParam && validTabs.includes(tabParam)) {
        const el  = document.getElementById('tab-' + tabParam);
        const btn = document.querySelector('.tab[onclick*="\''+tabParam+'\'"]');
        if (el && btn) {
            document.querySelectorAll('.fixture-tab-content').forEach(e => e.style.display = 'none');
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            el.style.display = 'block';
            btn.classList.add('active');
        }
    }

    if (fechaParam) aplicarFiltroDia(fechaParam);
})();

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

// ── Disponibilidad: guardar con debounce al escribir ──────────────────────
document.querySelectorAll('.input-disponibilidad').forEach(function(input) {
    var timer;
    input.addEventListener('input', function() {
        clearTimeout(timer);
        var id = this.dataset.id;
        var val = this.value;
        var el = this;
        timer = setTimeout(function() {
            var fd = new FormData();
            fd.append('inscripcion_id', id);
            fd.append('disponibilidad', val);
            fetch("<?= base_url('admin/Torneos/actualizar_disponibilidad') ?>", {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(function(resp) {
                el.style.borderColor = resp.ok ? '#2ecc71' : '#e74c3c';
                setTimeout(function() { el.style.borderColor = ''; }, 1500);
            });
        }, 600);
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

// ---- Bulk horarios ----
(function() {
    var toggleBtn = document.getElementById('btn-toggle-bulk');
    var wrap      = document.getElementById('bulk-horarios-wrap');
    var fillBtn   = document.getElementById('btn-bulk-fill');
    var fillRow   = document.getElementById('bulk-fill-row');
    var applyBtn  = document.getElementById('btn-apply-fill');
    var saveBtn   = document.getElementById('btn-guardar-bulk');
    var msg       = document.getElementById('bulk-msg');

    if (!toggleBtn) return;

    toggleBtn.addEventListener('click', function() {
        var open = wrap.style.display !== 'none';
        wrap.style.display = open ? 'none' : '';
        toggleBtn.textContent = open ? 'Programar horarios' : 'Cerrar horarios';
        toggleBtn.classList.toggle('open', !open);
    });

    fillBtn.addEventListener('click', function() {
        fillRow.style.display = fillRow.style.display === 'none' ? '' : 'none';
    });

    applyBtn.addEventListener('click', function() {
        var fecha  = document.getElementById('fill-fecha').value;
        var hora   = document.getElementById('fill-hora').value;
        var cancha = document.getElementById('fill-cancha').value;
        document.querySelectorAll('#bulk-horarios-wrap .bulk-row').forEach(function(row) {
            if (fecha)  row.querySelector('.bulk-fecha').value  = fecha;
            if (hora)   row.querySelector('.bulk-hora').value   = hora;
            if (cancha) row.querySelector('.bulk-cancha').value = cancha;
        });
        fillRow.style.display = 'none';
    });

    saveBtn.addEventListener('click', function() {
        var rows = document.querySelectorAll('#bulk-horarios-wrap .bulk-row');
        var partidos = [];
        rows.forEach(function(row) {
            partidos.push({
                id:     row.dataset.id,
                fecha:  row.querySelector('.bulk-fecha').value,
                hora:   row.querySelector('.bulk-hora').value,
                cancha: row.querySelector('.bulk-cancha').value
            });
        });

        saveBtn.disabled    = true;
        saveBtn.textContent = 'Guardando...';

        fetch('<?= site_url('admin/torneos/guardar_horarios_bulk') ?>', {
            method:  'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body:    'partidos=' + encodeURIComponent(JSON.stringify(partidos))
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            saveBtn.disabled    = false;
            saveBtn.textContent = 'Guardar cambios';
            msg.style.display   = 'block';
            if (data.ok) {
                msg.textContent   = data.actualizados + ' partido(s) actualizados correctamente.';
                msg.style.background = '#d4edda';
                msg.style.color      = '#155724';
                setTimeout(function() { location.reload(); }, 1500);
            } else {
                msg.textContent      = data.msg || 'Error al guardar.';
                msg.style.background = '#f8d7da';
                msg.style.color      = '#721c24';
            }
        });
    });
})();

// ---- Listado de partidos: filtro por día ----
function aplicarFiltroDia(fechaFiltro) {
    document.querySelectorAll('.btn-dia').forEach(function(b) {
        b.classList.toggle('active', b.dataset.fecha === fechaFiltro);
    });

    var rows     = document.querySelectorAll('#listado-tbody .listado-row');
    var visibles = 0;

    rows.forEach(function(row) {
        var mostrar = !fechaFiltro || row.dataset.fecha === fechaFiltro;
        row.style.display = mostrar ? '' : 'none';
        if (mostrar) visibles++;
    });

    var vacio = document.getElementById('listado-vacio');
    if (vacio) vacio.style.display = visibles === 0 ? '' : 'none';

    // Actualizar URL sin recargar
    var url = new URL(window.location.href);
    if (fechaFiltro) {
        url.searchParams.set('fecha', fechaFiltro);
    } else {
        url.searchParams.delete('fecha');
    }
    history.replaceState(null, '', url.toString());
}

document.querySelectorAll('.btn-dia').forEach(function(btn) {
    btn.addEventListener('click', function() {
        aplicarFiltroDia(this.dataset.fecha);
    });
});

// Aplicar filtro si viene en la URL al cargar
(function() {
    var params = new URLSearchParams(window.location.search);
    var fecha  = params.get('fecha');
    if (fecha) aplicarFiltroDia(fecha);
})();

// Botón compartir filtro
document.getElementById('btn-compartir-filtro').addEventListener('click', function() {
    var url = window.location.href;
    if (navigator.share) {
        navigator.share({ title: document.title, url: url }).catch(function(){});
    } else {
        window.open('https://wa.me/?text=' + encodeURIComponent(url), '_blank');
    }
});

// ── AVISOS ────────────────────────────────────────────────────────────────
var _torneoId = <?= (int)$torneo->id ?>;
var _torneoUrl = '<?= site_url('home/torneo/' . $torneo->id . '?tab=listado') ?>';

document.getElementById('form-aviso').addEventListener('submit', function(e) {
    e.preventDefault();
    var fd = new FormData(this);
    var btn = this.querySelector('.btn-aviso-crear');
    btn.disabled = true;
    btn.textContent = 'Publicando...';

    fetch('<?= base_url('admin/Avisos/crear') ?>', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(function(resp) {
            if (resp.ok) {
                renderAvisos(resp.avisos);
                document.getElementById('form-aviso').reset();
                document.querySelector('[name=horas]').value = '2';
            } else {
                alert('Error: ' + (resp.error ?? 'desconocido'));
            }
        })
        .finally(function() {
            btn.disabled = false;
            btn.textContent = 'Publicar aviso';
        });
});

function eliminarAviso(id) {
    if (!confirm('¿Eliminar este aviso?')) return;
    var fd = new FormData();
    fd.append('id', id);
    fd.append('torneo_id', _torneoId);
    fetch('<?= base_url('admin/Avisos/eliminar') ?>', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(function(resp) { if (resp.ok) renderAvisos(resp.avisos); });
}

function compartirAviso(id, mensaje, cancha) {
    var texto = cancha ? '⚠️ Cancha ' + cancha + ': ' + mensaje : '⚠️ ' + mensaje;
    texto += '\n\n' + _torneoUrl;
    if (navigator.share) {
        navigator.share({ title: 'Aviso Telares Padel', text: texto }).catch(function(){});
    } else {
        window.open('https://wa.me/?text=' + encodeURIComponent(texto), '_blank');
    }
}

function renderAvisos(avisos) {
    var lista = document.getElementById('avisos-lista');
    if (!avisos || avisos.length === 0) {
        lista.innerHTML = '<p class="avisos-empty" id="avisos-empty">No hay avisos publicados aún.</p>';
        return;
    }
    lista.innerHTML = avisos.map(function(av) {
        var activo = av.activo === true || av.activo === 't' || av.activo === '1';
        var expiraLabel = activo
            ? 'Expira: ' + new Date(av.expira_at).toLocaleString('es-AR', {day:'2-digit',month:'2-digit',hour:'2-digit',minute:'2-digit'})
            : 'Expirado';
        return '<div class="aviso-item ' + (activo ? 'aviso-activo' : 'aviso-expirado') + '" data-id="' + av.id + '">' +
            '<div class="aviso-item-info">' +
            (av.cancha ? '<span class="aviso-cancha-badge">Cancha ' + av.cancha + '</span>' : '') +
            '<span class="aviso-mensaje">' + av.mensaje + '</span>' +
            '<span class="aviso-expira">' + expiraLabel + '</span>' +
            '</div>' +
            '<div class="aviso-item-actions">' +
            '<button class="btn-aviso-wpp" onclick="compartirAviso(' + av.id + ',\'' + av.mensaje.replace(/'/g,"\\'") + '\',' + (av.cancha || 'null') + ')" title="Compartir"><i class="fab fa-whatsapp"></i></button>' +
            '<button class="btn-aviso-del" onclick="eliminarAviso(' + av.id + ')" title="Eliminar"><i class="fas fa-trash"></i></button>' +
            '</div></div>';
    }).join('');
}
</script>
