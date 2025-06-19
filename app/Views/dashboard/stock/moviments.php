<?= $this->extend('templates/dashboard') ?>
<?= $this->section('push-css') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/plugins/dataTables.bootstrap5.min.css') ?>">
<?= $this->endSection() ?>
<?= $this->section('content') ?>

<div class="pc-content">
    <div class="page-header">
        <h5>Movimentações de Estoque</h5>
    </div>

    <div class="card">
        <div class="card-body table-responsive dt-responsive">
            <table id="movements-table" class="table table-striped table-bordered nowrap">
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
                        <tr>
                            <td colspan="8" class="text-center">Nenhuma movimentação encontrada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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