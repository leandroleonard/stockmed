<?= $this->extend('templates/dashboard') ?>
<?= $this->section('push-css') ?>
<link rel="stylesheet" href="../assets/css/plugins/dataTables.bootstrap5.min.css">
<?= $this->endSection() ?>
<?= $this->section('content') ?>

<div class="pc-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Dashboard</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript: void(0)">Vendas</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- [ breadcrumb ] end -->
    <!-- [ Main Content ] start -->


    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5>Vendas</h5>

                    <a href="<?= base_url('dashboard/sales/create') ?>" class="btn btn-sm btn-primary"> <span class="fa fa-plus me-2"></span>Nova Venda</a>
                </div>

                <?php if (session()->get('success')): ?>
                    <div class="alert alert-success"><?= session()->get('success') ?></div>
                <?php endif; ?>

                <?php if (session()->get('error')): ?>
                    <div class="alert alert-danger"><?= session()->get('error') ?></div>
                <?php endif; ?>

                <?php if ($errors = session('errors')): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <?= esc($error) ?><br>
                        <?php endforeach ?>
                    </div>
                <?php endif; ?>

                <div class="card-body">
                    <div class="dt-responsive table-responsive">
                        <table id="tabela-vendas" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>Referência</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Data</th>
                                    <th><span class="fa fa-cog text-primary"></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sales as $sale): ?>
                                    <tr>
                                        <td><?= $sale['sale_number'] ?></td>
                                        <td><?= $sale['first_name'] . ' ' . $sale['last_name'] ?></td>
                                        <td><?= $sale['total_amount'] ?></td>
                                        <td><?= $sale['sale_date'] ?></td>
                                        <td>
                                            <a href="<?= base_url('dashboard/sales/details/' . $sale['sale_number']) ?>" class="btn btn-sm btn-primary">Ver</a>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Referência</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Data</th>
                                    <th><span class="fa fa-cog text-primary"></span></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
<?= $this->endSection() ?>

<?= $this->section('push-javascript') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="<?= base_url('assets/js/plugins/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/js/plugins/dataTables.bootstrap5.min.js') ?>"></script>
<script>
    // [ Zero Configuration ] start
    $('#simpletable').DataTable();

    // [ Default Ordering ] start
    $('#order-table').DataTable({
        order: [
            [3, 'desc']
        ]
    });

    // [ Multi-Column Ordering ]
    $('#multi-colum-dt').DataTable({
        columnDefs: [{
                targets: [0],
                orderData: [0, 1]
            },
            {
                targets: [1],
                orderData: [1, 0]
            },
            {
                targets: [4],
                orderData: [4, 0]
            }
        ]
    });

    // [ Complex Headers ]
    $('#complex-dt').DataTable();

    // [ DOM Positioning ]
    $('#DOM-dt').DataTable({
        dom: '<"top"i>rt<"bottom"flp><"clear">'
    });

    // [ Alternative Pagination ]
    $('#alt-pg-dt').DataTable({
        pagingType: 'full_numbers'
    });

    // [ Scroll - Vertical ]
    $('#scr-vrt-dt').DataTable({
        scrollY: '200px',
        scrollCollapse: true,
        paging: false
    });

    // [ Scroll - Vertical, Dynamic Height ]
    $('#scr-vtr-dynamic').DataTable({
        scrollY: '50vh',
        scrollCollapse: true,
        paging: false
    });

    // [ Language - Comma Decimal Place ]
    $('#lang-dt').DataTable({
        language: {
            decimal: ',',
            thousands: '.'
        }
    });
</script>
<!-- [Page Specific JS] end -->
<?= $this->endSection() ?>