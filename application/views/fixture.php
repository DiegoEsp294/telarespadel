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
                <?php if($torneo->partidos_visibles == "t" && !empty($todos_partidos)):?>
                    <button class="tab" onclick="showTab(event,'listado')">Partidos</button>
                <?php endif; ?>
            </div>

            <!-- =============================
                    ZONAS
            ============================= -->

            <div id="tab-zonas" class="fixture-tab-content">

                <div class="">
                    <?php if($torneo->zona_visible == "t"): ?>
                        <?php foreach($zonas as $idx => $zona): ?>

                            <div class="zona-card" id="zona-card-<?= $idx ?>">

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
                            <div class="btn-descargar-wrap">
                                <button class="btn-descargar" onclick="descargarImagen('zona-card-<?= $idx ?>', 'zona-<?= chr(64+$zona['grupo']) ?>.png')">Descargar</button>
                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="bracket-empty">La fase de grupos no es visible para este torneo.</p>
                    <?php endif; ?>
                </div>

            </div>

            <div id="tab-resultados" class="fixture-tab-content" style="display:none;">

                <?php if($torneo->resultados_visibles == "t"): ?>
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

                                    // calcular ganador por sets
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

                                    <div class="res-partido-card">
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
                                                    <span class="set-box set-box-final <?= $p1_win ? 'win' : '' ?>"><?= $sets_p1 ?></span>
                                                </div>
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
                                                    <span class="set-box set-box-final <?= $p2_win ? 'win' : '' ?>"><?= $sets_p2 ?></span>
                                                </div>
                                            <?php endif; ?>
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
            <!-- =============================
                    PLAYOFF
            ============================= -->

            <div id="tab-playoff" class="fixture-tab-content" style="display:none;">

                <?php if (empty($playoff)): ?>
                    <p class="bracket-empty">No hay cruces generados aún.</p>
                <?php else: ?>

                    <div class="btn-descargar-wrap">
                        <button class="btn-descargar" onclick="descargarImagen('playoff-bracket', 'cruces.png')">Descargar</button>
                    </div>
                    <div class="bracket-wrapper" id="playoff-bracket">
                        <?php
                        // Numeración correlativa
                        $_n = 1; $partido_num = []; $feeder_map = [];
                        foreach ($playoff as $_r) {
                            foreach ($_r['partidos'] as $_p) { $partido_num[$_p->id] = $_n++; }
                        }
                        foreach ($playoff as $_r) {
                            foreach ($_r['partidos'] as $_p) {
                                if ($_p->partido_siguiente_id) {
                                    $feeder_map[$_p->partido_siguiente_id][(int)$_p->slot_siguiente] = $_p->id;
                                }
                            }
                        }
                        ?>
                        <?php foreach ($playoff as $ronda => $rondaData): ?>

                            <div class="bracket-round">

                                <div class="round-label"><?= htmlspecialchars($rondaData['nombre']) ?></div>

                                <div class="round-matches">

                                    <?php foreach ($rondaData['partidos'] as $partido): ?>

                                        <?php
                                            $p1_winner = $partido->ganador_id && $partido->ganador_id == $partido->pareja1_id;
                                            $p2_winner = $partido->ganador_id && $partido->ganador_id == $partido->pareja2_id;

                                            $p1_real = $partido->pareja1_nombre && strlen(trim(str_replace('/', '', $partido->pareja1_nombre))) > 1;
                                            $p2_real = $partido->pareja2_nombre && strlen(trim(str_replace('/', '', $partido->pareja2_nombre))) > 1;

                                            if ($p1_real) {
                                                $p1_name = $partido->pareja1_nombre;
                                            } elseif (isset($feeder_map[$partido->id][1])) {
                                                $p1_name = 'Ganador Partido ' . $partido_num[$feeder_map[$partido->id][1]];
                                            } elseif ($partido->referencia1) {
                                                $p1_name = $partido->referencia1;
                                            } else {
                                                $p1_name = 'Por definir';
                                            }

                                            if ($p2_real) {
                                                $p2_name = $partido->pareja2_nombre;
                                            } elseif (isset($feeder_map[$partido->id][2])) {
                                                $p2_name = 'Ganador Partido ' . $partido_num[$feeder_map[$partido->id][2]];
                                            } elseif ($partido->referencia2) {
                                                $p2_name = $partido->referencia2;
                                            } else {
                                                $p2_name = 'Por definir';
                                            }

                                            $p1_tbd = !$partido->pareja1_id;
                                            $p2_tbd = !$partido->pareja2_id;
                                        ?>

                                        <?php
                                            $jugado_playoff = $partido->set1_p1 !== null;
                                            $po_s1 = 0; $po_s2 = 0;
                                            if ($jugado_playoff) {
                                                if ($partido->set1_p1 > $partido->set1_p2) $po_s1++; else $po_s2++;
                                                if ($partido->set2_p1 !== null) { if ($partido->set2_p1 > $partido->set2_p2) $po_s1++; else $po_s2++; }
                                                if ($partido->set3_p1 !== null) { if ($partido->set3_p1 > $partido->set3_p2) $po_s1++; else $po_s2++; }
                                            }
                                            $meta_playoff = '';
                                            if (!empty($partido->hora))   $meta_playoff .= substr($partido->hora, 0, 5).'h';
                                            if (!empty($partido->cancha)) $meta_playoff .= ($meta_playoff ? ' · ' : '').'Cancha '.$partido->cancha;
                                            if (!empty($partido->fecha))  $meta_playoff .= ($meta_playoff ? ' · ' : '').date('d/m', strtotime($partido->fecha));
                                        ?>
                                        <div class="match-wrapper">
                                            <div class="match-card-bracket">

                                                <div class="match-card-num">Partido <?= $partido_num[$partido->id] ?></div>

                                                <?php if ($meta_playoff): ?>
                                                    <div class="match-card-meta"><?= htmlspecialchars($meta_playoff) ?></div>
                                                <?php endif; ?>

                                                <div class="match-team <?= $p1_winner ? 'winner' : ($p1_tbd ? 'tbd' : '') ?>">
                                                    <span class="team-name"><?= htmlspecialchars($p1_name) ?></span>
                                                    <?php if ($jugado_playoff): ?>
                                                        <div class="set-boxes">
                                                            <span class="set-box <?= $partido->set1_p1 > $partido->set1_p2 ? 'win' : '' ?>"><?= $partido->set1_p1 ?></span>
                                                            <?php if ($partido->set2_p1 !== null): ?>
                                                                <span class="set-box <?= $partido->set2_p1 > $partido->set2_p2 ? 'win' : '' ?>"><?= $partido->set2_p1 ?></span>
                                                            <?php endif; ?>
                                                            <?php if ($partido->set3_p1 !== null): ?>
                                                                <span class="set-box <?= $partido->set3_p1 > $partido->set3_p2 ? 'win' : '' ?>"><?= $partido->set3_p1 ?></span>
                                                            <?php endif; ?>
                                                            <span class="set-box set-box-final <?= $po_s1 > $po_s2 ? 'win' : '' ?>"><?= $po_s1 ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="match-divider"></div>

                                                <div class="match-team <?= $p2_winner ? 'winner' : ($p2_tbd ? 'tbd' : '') ?>">
                                                    <span class="team-name"><?= htmlspecialchars($p2_name) ?></span>
                                                    <?php if ($jugado_playoff): ?>
                                                        <div class="set-boxes">
                                                            <span class="set-box <?= $partido->set1_p2 > $partido->set1_p1 ? 'win' : '' ?>"><?= $partido->set1_p2 ?></span>
                                                            <?php if ($partido->set2_p1 !== null): ?>
                                                                <span class="set-box <?= $partido->set2_p2 > $partido->set2_p1 ? 'win' : '' ?>"><?= $partido->set2_p2 ?></span>
                                                            <?php endif; ?>
                                                            <?php if ($partido->set3_p1 !== null): ?>
                                                                <span class="set-box <?= $partido->set3_p2 > $partido->set3_p1 ? 'win' : '' ?>"><?= $partido->set3_p2 ?></span>
                                                            <?php endif; ?>
                                                            <span class="set-box set-box-final <?= $po_s2 > $po_s1 ? 'win' : '' ?>"><?= $po_s2 ?></span>
                                                        </div>
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
            <!-- =============================
                    LISTADO DE PARTIDOS
            ============================= -->

            <?php if(!empty($todos_partidos)): ?>
            <div id="tab-listado" class="fixture-tab-content" style="display:none;">

                <?php
                $dias_es = ['Sunday'=>'Domingo','Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miércoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sábado'];

                $fechas_unicas = [];
                foreach ($todos_partidos as $p) {
                    if ($p->fecha) $fechas_unicas[substr($p->fecha, 0, 10)] = true;
                }
                ksort($fechas_unicas);
                $fechas_unicas = array_keys($fechas_unicas);
                ?>

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

                <div class="listado-cards" id="listado-tbody-pub">
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

                <p class="listado-sin-resultados" id="listado-vacio-pub" style="display:none;">
                    No hay partidos para el día seleccionado.
                </p>

            </div>
            <?php endif; ?>

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
var _fixtureContext = {
    torneo_id:    '<?= $torneo->id ?? '' ?>',
    categoria_id: '<?= $categoria_id ?? '' ?>',
};

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

    if (typeof trackAccion === 'function') {
        trackAccion('tab_' + tab, _fixtureContext);
    }
}

// =====================
// Descarga como imagen
// =====================
const LOGO_URL   = '<?= base_url("logo_inicio.png") ?>';
const SITE_URL   = window.location.hostname;

async function descargarImagen(elementId, filename) {
    const el = document.getElementById(elementId);
    if (!el) return;

    const btn = el.parentElement.querySelector('.btn-descargar');
    if (btn) { btn.disabled = true; btn.textContent = 'Generando...'; }

    try {
        const canvas = await html2canvas(el, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff',
            scrollX: -window.scrollX,
            scrollY: -window.scrollY,
            windowWidth: document.documentElement.scrollWidth,
        });

        const WH = 64 * 2; // watermark height scaled
        const PADDING = 16 * 2;

        const final = document.createElement('canvas');
        final.width  = canvas.width;
        final.height = canvas.height + WH;

        const ctx = final.getContext('2d');

        // Imagen capturada
        ctx.drawImage(canvas, 0, 0);

        // Banda watermark
        ctx.fillStyle = '#1a1a2e';
        ctx.fillRect(0, canvas.height, final.width, WH);

        // Logo
        const logo = new Image();
        logo.crossOrigin = 'anonymous';
        await new Promise(resolve => {
            logo.onload = resolve;
            logo.onerror = resolve;
            logo.src = LOGO_URL;
        });

        const logoH = WH * 0.65;
        const logoW = logoH;
        const logoY = canvas.height + (WH - logoH) / 2;
        ctx.drawImage(logo, PADDING, logoY, logoW, logoH);

        // Texto URL
        ctx.fillStyle = '#ffffff';
        ctx.font = `bold ${22 * 2}px Arial, sans-serif`;
        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';
        ctx.fillText(SITE_URL, final.width - PADDING, canvas.height + WH / 2);

        // Descargar
        const link = document.createElement('a');
        link.download = filename;
        link.href = final.toDataURL('image/png');
        link.click();

    } finally {
        if (btn) { btn.disabled = false; btn.textContent = 'Descargar'; }
    }
}

// Filtro por día - listado de partidos
document.querySelectorAll('.btn-dia').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.btn-dia').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        var fechaFiltro = this.dataset.fecha;
        var rows     = document.querySelectorAll('.listado-row');
        var visibles = 0;

        rows.forEach(function(row) {
            var mostrar = !fechaFiltro || row.dataset.fecha === fechaFiltro;
            row.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        ['listado-vacio', 'listado-vacio-pub'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.style.display = visibles === 0 ? '' : 'none';
        });
    });
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>