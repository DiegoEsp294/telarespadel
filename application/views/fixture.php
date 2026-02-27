<div class="">
    <div class="">

        <div class="container">
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
                                    <div>CANCHA</div>
                                    <div>SETS</div>

                                </div>

                                <?php foreach($zona['partidos'] as $partido): ?>

                                    <div class="duelo-row"
                                        data-partido-id="<?= $partido['partido_id'] ?>">

                                        <div><?= $partido['duelo'] ?></div>
                                        <div><?= strtoupper($partido['dia']) ?></div>
                                        <div class="hora"><?= $partido['hora'] ?></div>
                                        <div class="cancha"><?= $partido['cancha'] ?></div>
                                        <div class="sets">
                                            <?php 
                                            $sets = [];?>
                                            <?= $partido['set1_p1'] ?? '-' ?>-<?= $partido['set1_p2'] ?? '-' ?>
                                            <?= $partido['set2_p1'] ?? '-' ?>-<?= $partido['set2_p2'] ?? '-' ?>
                                            <?= $partido['set3_p1'] ?? '-' ?>-<?= $partido['set3_p2'] ?? '-' ?>
                                            <?php  echo implode(', ', $sets); 
                                            ?>
                                        </div>
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
                <h3>Playoff</h3>

                <?php for ($i = 0; $i < count($playoff); $i += 2): ?>

                    <?php
                        $pareja1 = $playoff[$i];
                        $pareja2 = $playoff[$i + 1] ?? null;
                    ?>

                    <div class="match-card">

                        <div class="match-info">

                            <div class="pareja">
                                <?= $pareja1->pareja_nombre ?>
                                <small>
                                    (<?= $pareja1->posicion ?>° Zona <?= $pareja1->zona_numero ?>)
                                </small>
                            </div>

                            <div class="vs">VS</div>

                            <div class="pareja">
                                <?= $pareja2
                                    ? $pareja2->pareja_nombre .
                                    " <small>({$pareja2->posicion}° Zona {$pareja2->zona_numero})</small>"
                                    : "Libre"
                                ?>
                            </div>

                        </div>

                    </div>

                <?php endfor; ?>
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