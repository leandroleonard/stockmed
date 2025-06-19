<!-- View: stock/stock_levels_list.php -->
<?= $this->extend('templates/dashboard') ?>
<?= $this->section('content') ?>

<style>
    .low-stock {
        background-color: #f8d7da !important; /* vermelho claro */
        color: #842029 !important;
    }
</style>

<div class="pc-content">
    <div class="page-header">
        <h5>Níveis de Stock</h5>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Armazém</th>
                        <th>Quantidade Disponível</th>
                        <th>Quantidade Reservada</th>
                        <th>Quantidade em Pedido</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($stockLevels)): ?>
                        <?php foreach ($stockLevels as $stock): ?>
                            <tr class="<?= ($stock['quantity_available'] < 10) ? 'low-stock' : '' ?>">
                                <td><?= esc($stock['product_name']) ?></td>
                                <td><?= esc($stock['warehouse_name']) ?></td>
                                <td><?= esc($stock['quantity_available']) ?></td>
                                <td><?= esc($stock['quantity_reserved']) ?></td>
                                <td><?= esc($stock['quantity_on_order']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">Nenhum registro de stock encontrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>