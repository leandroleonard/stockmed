<?= $this->extend('templates/dashboard') ?>
<?= $this->section('content') ?>

<div class="pc-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Home</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript: void(0)">Dashboard</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- [ breadcrumb ] end -->
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Total em stock</h6>
                    <h4 class="mb-3"><?= esc($totalStock) ?> <span class="badge bg-light-primary border border-primary"><i
                                class="ti ti-trending-up"></i></span></h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Cliente</h6>
                    <h4 class="mb-3"><?= esc($totalClients) ?> <span class="badge bg-light-success border border-success"><i
                                class="ti ti-trending-up"></i></span></h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Vendas no mês</h6>
                    <h4 class="mb-3"><?= esc($salesThisMonth) ?> <span class="badge bg-light-warning border border-warning"><i
                                class="ti ti-trending-down"></i></span></h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Total em vendas</h6>
                    <h4 class="mb-3"><?= number_format($totalSalesValue, 2, ',', '.') ?> KZ <span class="badge bg-light-danger border border-danger"><i
                                class="ti ti-trending-down"></i></span></h4>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <h5 class="mb-3">Últimas vendas</h5>
            <div class="card tbl-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>VENDA NO.</th>
                                    <th>CLIENTE</th>
                                    <th>ARTIGO</th>
                                    <th>QUANTIDADE</th>
                                    <th class="text-end">TOTAL VENDA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($lastSales)): ?>
                                    <?php foreach ($lastSales as $sale): ?>
                                        <tr>
                                            <td><a href="#" class="text-muted"><?= esc($sale['id']) ?></a></td>
                                            <td><?= esc($sale['first_name']) . ' ' . esc($sale['last_name']) ?></td>
                                            <td><?= esc($sale['product']) ?></td>
                                            <td><?= esc($sale['quantity']) ?></td>
                                            <td class="text-end"><?= number_format($sale['total_amount'], 2, ',', '.') ?> KZ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Nenhuma venda encontrada.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>