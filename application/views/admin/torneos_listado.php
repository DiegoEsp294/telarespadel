<section class="torneos-section">
<div class="container container-admin">

<h2 class="admin-title">Mis Torneos</h2>
<br>
<div class="admin-toolbar">
    <a href="<?= base_url('admin/torneos/crear') ?>" class="btn-create">
        + Crear Torneo
    </a>
</div>
<br>
<br>
<div class="torneos-list">

<?php foreach($torneos as $torneo): ?>

<div class="torneo-row">

    <div class="torneo-info">
        <h3><?= $torneo->nombre ?></h3>

        <div class="torneo-meta">
            <span><strong>Organiza:</strong> <?= $torneo->organizador_nombre ?? '—' ?></span>
            <span>
                <strong>Fechas:</strong>
                <?= date('d/m/Y', strtotime($torneo->fecha_inicio)) ?>
                - <?= date('d/m/Y', strtotime($torneo->fecha_fin)) ?>
            </span>
        </div>
    </div>

    <div class="torneo-actions">
        <a href="<?= base_url('admin/torneos/ver/'.$torneo->id) ?>" class="btn-admin info btn-action-ver">Ver</a>
        <a href="<?= base_url('admin/torneos/editar/'.$torneo->id) ?>" class="btn-admin edit btn-action-editar">Editar</a>
        <a href="<?= base_url('admin/torneos/fixture/'.$torneo->id) ?>" class="btn-admin info btn-action-ver">Fixture</a>
        <a href="<?= base_url('admin/torneos/eliminar/'.$torneo->id) ?>" class="btn-admin delete btn-action-eliminar">Eliminar</a>
    </div>

</div>

<?php endforeach; ?>

</div>
</div>
</section>