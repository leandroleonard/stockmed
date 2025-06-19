<?= $this->extend('templates/dashboard') ?>
<?= $this->section('content') ?>

<div class="pc-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5>Visualizar Venda</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard/sales') ?>">Vendas</a></li>
                        <li class="breadcrumb-item active">#<?= esc($sale['sale_number']) ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- [ breadcrumb ] end -->

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" id="invoice">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Fatura da Venda #<?= esc($sale['sale_number']) ?></h5>
                    <div>
                        <button class="btn btn-primary btn-sm" onclick="window.print()">Imprimir</button>
                    </div>
                </div>
                <div class="card-body">
                    <p><strong>Data da Venda:</strong> <?= date('d/m/Y H:i', strtotime($sale['sale_date'])) ?></p>
                    <p><strong>Cliente:</strong> <?= esc($sale['first_name'] . ' ' . $sale['last_name']) ?></p>
                    <p><strong>Armazém:</strong> <?= esc($sale['warehouse_name']) ?></p>
                    <p><strong>Caixa:</strong> <?= esc($sale['cashier_name']) ?></p>

                    <table class="table table-bordered mt-4">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Preço Unitário</th>
                                <th>IVA (14%)</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <?php
                                $iva = $item['unit_price'] * 0.14;
                                $total = ($item['unit_price'] + $iva) * $item['quantity'];
                            ?>
                            <tr>
                                <td><?= esc($item['product_name']) ?></td>
                                <td><?= esc($item['quantity']) ?></td>
                                <td> <?= number_format($item['unit_price'], 2, ',', '.') ?> kz</td>
                                <td> <?= number_format($iva, 2, ',', '.') ?> kz</td>
                                <td> <?= number_format($total, 2, ',', '.') ?> kz</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total da Venda:</th>
                                <th> <?= number_format($sale['total_amount'], 2, ',', '.') ?> kz</th>
                            </tr>
                        </tfoot>
                    </table>

                    <p class="mt-4"><strong>Observações:</strong> <?= esc($sale['notes'] ?? '-') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('push-css') ?>
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #invoice, #invoice * {
        visibility: visible;
    }
    #invoice {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>
<?= $this->endSection() ?>