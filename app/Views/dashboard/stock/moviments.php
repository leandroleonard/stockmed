<!-- View: stock/movements_list.php -->
<?= $this->extend('templates/dashboard') ?>
<?= $this->section('content') ?>

<div class="pc-content">
    <div class="page-header">
        <h5>Movimentações de Estoque</h5>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Produto</th>
                        <th>Lote</th>
                        <th>Armazém</th>
                        <th>Tipo de Movimento</th>
                        <th>Quantidade</th>
                        <th>Usuário</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($movements)): ?>
                        <?php foreach ($movements as $move): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($move['movement_date'])) ?></td>
                                <td><?= esc($move['product_name']) ?></td>
                                <td><?= esc($move['batch_number'] ?? '-') ?></td>
                                <td><?= esc($move['warehouse_name']) ?></td>
                                <td><?= ucfirst(esc($move['movement_type'])) ?></td>
                                <td><?= esc($move['quantity']) ?></td>
                                <td><?= esc($move['user_name'] ?? '-') ?></td>
                                <td><?= esc($move['notes'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center">Nenhuma movimentação encontrada.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>