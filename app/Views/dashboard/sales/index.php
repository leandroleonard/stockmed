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
            <table id="movementsTable" class="table table-striped table-bordered nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Produto</th>
                        <th>Lote</th>
                        <th>Armazém</th>
                        <th>Tipo</th>
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
                <tfoot>
                    <tr>
                        <th>Data</th>
                        <th>Produto</th>
                        <th>Lote</th>
                        <th>Armazém</th>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Usuário</th>
                        <th>Notas</th>
                    </tr>
                </tfoot>
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
    $(document).ready(function() {
        $('#movementsTable').DataTable({
            order: [[0, 'desc']],
            language: {
                decimal: ',',
                thousands: '.',
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
            }
        });
    });
</script>
<?= $this->endSection() ?>