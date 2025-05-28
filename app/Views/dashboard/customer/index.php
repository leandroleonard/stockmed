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
                        <li class="breadcrumb-item"><a href="javascript: void(0)">Stock</a></li>
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
                <h5>Clientes</h5>
                <a href="<?= base_url('dashboard/clients/create') ?>" class="btn btn-sm btn-primary"> <span class="fa fa-plus me-2"></span>Criar</a>
            </div>
            <div class="card-body">
                <div class="dt-responsive table-responsive">
                    <table id="tabela-clientes" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Morada</th>
                                <th>Email</th>
                                <th>NIF</th>
                                <th><span class="fa fa-cog text-primary"></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>João Manuel</td>
                                <td>Luanda, Maianga</td>
                                <td>joao@email.com</td>
                                <td>123456789</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary">Editar</a>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Ana Silva</td>
                                <td>Benguela, Lobito</td>
                                <td>ana.silva@email.com</td>
                                <td>987654321</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary">Editar</a>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Carlos Pedro</td>
                                <td>Huambo</td>
                                <td>carlosp@email.com</td>
                                <td>192837465</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary">Editar</a>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Morada</th>
                                <th>Email</th>
                                <th>NIF</th>
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