<div class="container-fluid py-4">
    <h2 class="mb-4">Métricas de uso</h2>

    <!-- CARDS RESUMEN HOY -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="fs-1 fw-bold text-primary"><?= $hoy->total_visitas ?? 0 ?></div>
                    <div class="text-muted small">Eventos hoy</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="fs-1 fw-bold text-success"><?= $hoy->sesiones_unicas ?? 0 ?></div>
                    <div class="text-muted small">Sesiones únicas hoy</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="fs-1 fw-bold text-warning"><?= $hoy->mobile ?? 0 ?></div>
                    <div class="text-muted small">Accesos mobile hoy</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="fs-1 fw-bold text-info"><?= $general->pct_mobile ?? 0 ?>%</div>
                    <div class="text-muted small">% mobile histórico</div>
                </div>
            </div>
        </div>
    </div>

    <!-- TOTALES HISTORICOS -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted small">Total eventos registrados</div>
                            <div class="fs-4 fw-bold"><?= number_format($general->total_eventos ?? 0) ?></div>
                        </div>
                        <div>
                            <div class="text-muted small">Sesiones históricas únicas</div>
                            <div class="fs-4 fw-bold"><?= number_format($general->sesiones_totales ?? 0) ?></div>
                        </div>
                        <div>
                            <div class="text-muted small">Desde</div>
                            <div class="fs-6 fw-bold"><?= $general->primer_evento ? date('d/m/Y', strtotime($general->primer_evento)) : '-' ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GRAFICO VISITAS POR DIA -->
    <div class="card mb-4">
        <div class="card-header fw-bold">Visitas por día (últimos 14 días)</div>
        <div class="card-body">
            <?php if (empty($por_dia)): ?>
                <p class="text-muted">Sin datos aún.</p>
            <?php else: ?>
                <?php
                $maxVal = max(array_map(fn($r) => $r->total, $por_dia));
                $maxVal = max($maxVal, 1);
                ?>
                <div class="d-flex align-items-end gap-1" style="height:120px; overflow-x:auto;">
                    <?php foreach ($por_dia as $fila): ?>
                        <?php $pct = round(($fila->total / $maxVal) * 100); ?>
                        <div class="d-flex flex-column align-items-center" style="min-width:36px;">
                            <div class="text-muted" style="font-size:10px;"><?= $fila->total ?></div>
                            <div class="bg-primary rounded-top" style="width:28px; height:<?= $pct ?>%;"></div>
                            <div class="text-muted mt-1" style="font-size:9px; white-space:nowrap;">
                                <?= date('d/m', strtotime($fila->dia)) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- TOP PAGINAS -->
        <div class="col-12 col-md-6">
            <div class="card h-100">
                <div class="card-header fw-bold">Top páginas (últimos 7 días)</div>
                <div class="card-body p-0">
                    <?php if (empty($top_paginas)): ?>
                        <p class="text-muted p-3">Sin datos aún.</p>
                    <?php else: ?>
                        <table class="table table-sm mb-0">
                            <thead><tr><th>URL</th><th class="text-end">Vistas</th></tr></thead>
                            <tbody>
                                <?php foreach ($top_paginas as $p): ?>
                                    <tr>
                                        <td class="text-truncate" style="max-width:260px;" title="<?= htmlspecialchars($p->url) ?>">
                                            <?= htmlspecialchars($p->url) ?>
                                        </td>
                                        <td class="text-end fw-bold"><?= $p->vistas ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- TOP ACCIONES -->
        <div class="col-12 col-md-6">
            <div class="card h-100">
                <div class="card-header fw-bold">Top acciones (últimos 7 días)</div>
                <div class="card-body p-0">
                    <?php if (empty($top_acciones)): ?>
                        <p class="text-muted p-3">Sin datos aún.</p>
                    <?php else: ?>
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Acción</th><th class="text-end">Cantidad</th></tr></thead>
                            <tbody>
                                <?php foreach ($top_acciones as $a): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($a->accion) ?></td>
                                        <td class="text-end fw-bold"><?= $a->cantidad ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- VISITAS POR TORNEO -->
    <div class="card mb-4">
        <div class="card-header fw-bold">Visitas por torneo (últimos 30 días)</div>
        <div class="card-body p-0">
            <?php if (empty($por_torneo)): ?>
                <p class="text-muted p-3">Sin datos aún.</p>
            <?php else: ?>
                <table class="table table-sm mb-0">
                    <thead><tr><th>Torneo</th><th class="text-end">Visitas</th><th class="text-end">Sesiones únicas</th></tr></thead>
                    <tbody>
                        <?php foreach ($por_torneo as $t): ?>
                            <tr>
                                <td><?= htmlspecialchars($t->torneo_nombre ?? 'ID ' . $t->torneo_id) ?></td>
                                <td class="text-end"><?= $t->visitas ?></td>
                                <td class="text-end"><?= $t->sesiones ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
