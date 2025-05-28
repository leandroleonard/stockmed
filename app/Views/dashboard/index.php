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
                    <h4 class="mb-3">4,236 <span class="badge bg-light-primary border border-primary"><i
                                class="ti ti-trending-up"></i> 59.3%</span></h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Cliente</h6>
                    <h4 class="mb-3">1,250 <span class="badge bg-light-success border border-success"><i
                                class="ti ti-trending-up"></i> 70.5%</span></h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Vendas no mês</h6>
                    <h4 class="mb-3">8,800 <span class="badge bg-light-warning border border-warning"><i
                                class="ti ti-trending-down"></i> 27.4%</span></h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Total em vendas</h6>
                    <h4 class="mb-3">+3M <span class="badge bg-light-danger border border-danger"><i
                                class="ti ti-trending-down"></i> 27.4%</span></h4>
                    </p>
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
                                <tr>
                                    <td><a href="#" class="text-muted">84564564</a></td>
                                    <td>António José</td>
                                    <td>Aspirina</td>
                                    <td>2</td>
                                    </td>
                                    <td class="text-end">5.000 KZ</td>
                                </tr>
                                <tr>
                                    <td><a href="#" class="text-muted">84564564</a></td>
                                    <td>Júlia Baptista</td>
                                    <td>Clavamox 500mg</td>
                                    <td>1</td>
                                    </td>
                                    <td class="text-end">3.500 KZ</td>
                                </tr>
                                <tr>
                                    <td><a href="#" class="text-muted">84564564</a></td>
                                    <td>Miguel</td>
                                    <td>Paracetamol </td>
                                    <td>1</td>
                                    <td class="text-end">1.500 KZ</td>
                                </tr>
                                <tr>
                                    <td><a href="#" class="text-muted">84564564</a></td>
                                    <td>Ana Santos</td>
                                    <td>Paracetamol</td>
                                    <td>10</td>
                                    </td>
                                    <td class="text-end">15.000 KZ</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>