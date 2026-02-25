<div class="">
<div class="">

<div class="container">

<h2>Fixture — <?= $torneo->nombre ?></h2>

<div class="admin-header-actions">
    <a href="<?= base_url('admin/torneos/torneos') ?>" class="btn-back">
        ← Volver
    </a>
</div>

<!-- =============================
     SELECTOR CATEGORIA
============================= -->

<form method="get">
    <select name="categoria_id"
            onchange="this.form.submit()">

        <?php foreach($categorias as $cat): ?>
            <option value="<?= $cat->id ?>"
                <?= $categoria_id == $cat->id ? 'selected':'' ?>>
                <?= $cat->nombre ?>
            </option>
        <?php endforeach; ?>

    </select>
</form>

<hr>

<!-- =============================
        TABS
============================= -->

<div class="fixture-tabs">
    <button class="tab active" onclick="showTab('zonas')">Zonas</button>
    <button class="tab" onclick="showTab('playoff')">Playoff</button>
</div>

<!-- =============================
        ZONAS
============================= -->

<div id="tab-zonas" class="fixture-tab-content">

    <div class="fixture-container">

        <?php foreach($zonas as $zona): ?>

            <div class="zona-card">

                <!-- HEADER -->
                <div class="zona-header">
                    <h2>FASE DE GRUPOS</h2>
                </div>

                <!-- GRUPO -->
                <div class="grupo-title">
                    GRUPO <?= $zona['grupo'] ?>
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
                    </div>

                    <?php foreach($zona['partidos'] as $partido): ?>

                        <div class="duelo-row">
                            <div><?= $partido['duelo'] ?></div>
                            <div><?= strtoupper($partido['dia']) ?></div>
                            <div class="hora"><?= $partido['hora'] ?></div>
                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

<!-- =============================
        PLAYOFF
============================= -->

<div id="tab-playoff" class="fixture-tab-content" style="display:none;">

<?php foreach($playoff as $ronda => $partidos): ?>

    <h3><?= $ronda ?></h3>

    <?php foreach($partidos as $partido): ?>

        <div class="match-card <?= $partido->estado ?>">

            <div class="match-info">
                <div class="pareja"><?= $partido->pareja1 ?></div>
                <div class="vs">VS</div>
                <div class="pareja"><?= $partido->pareja2 ?></div>
            </div>

        </div>

    <?php endforeach; ?>

<?php endforeach; ?>

</div>

</div>
</div>
</div>



<script>
function showTab(tab)
{
    document.querySelectorAll('.fixture-tab-content')
        .forEach(el => el.style.display='none');

    document.getElementById('tab-'+tab).style.display='block';

    document.querySelectorAll('.tab')
        .forEach(t=>t.classList.remove('active'));

    event.target.classList.add('active');
}
</script>