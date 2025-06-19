<?= $this->extend('templates/dashboard') ?>
<?= $this->section('content') ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="pc-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Registrar Venda</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('sales') ?>">Vendas</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Nova Venda</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- [ breadcrumb ] end -->

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Nova Venda</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('sales/store') ?>" class="row" id="saleForm">
                        <?= csrf_field() ?>

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

                        <!-- Cliente -->
                        <div class="form-group col-md-6">
                            <label for="customer_id" class="form-label">Cliente</label>
                            <select name="customer_id" id="customer_id" class="form-control" required>
                                <option value="">Selecione o cliente</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= esc($customer['id']) ?>" <?= old('customer_id') == $customer['id'] ? 'selected' : '' ?>>
                                        <?= esc($customer['first_name'] . ' ' . $customer['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Armazém -->
                        <div class="form-group col-md-6 mb-3">
                            <label for="barcode_input" class="form-label">Código de Barras</label>
                            <input type="text" id="barcode_input" class="form-control" placeholder="Digite ou escaneie o código de barras">
                        </div>


                        <!-- Itens da venda -->
                        <div class="col-12">
                            <h6>Itens da Venda</h6>
                            <table class="table table-bordered" id="saleItemsTable">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Quantidade</th>
                                        <th>Preço Unitário</th>
                                        <th>IVA (14%)</th>
                                        <th>Total</th>
                                        <th><button type="button" class="btn btn-sm btn-success" id="addItemBtn">+</button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="items[0][product_id]" class="form-control product-select" required>
                                                <option value="">Selecione o produto</option>
                                                <?php foreach ($products as $product): ?>
                                                    <option
                                                        value="<?= esc($product['id']) ?>"
                                                        data-price="<?= esc($product['selling_price']) ?>"
                                                        data-stock="<?= esc($product['quantity_available'] ?? 0) ?>">
                                                        <?= esc($product['product_name']) ?>

                                                        <?= isset($product['dosage']) && !empty($product['dosage']) ? ' - ' . esc($product['dosage']) : '' ?>

                                                        <?= isset($product['form']) && !empty($product['form']) ? ' - ' . esc($product['form']) : '' ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][quantity]" class="form-control quantity-input" min="1" value="1" required>
                                            <small class="text-muted stock-info"></small>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][unit_price]" class="form-control unit-price-input" step="0.01" min="0" readonly>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][iva]" class="form-control iva-input" step="0.01" min="0" readonly>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][total_price]" class="form-control total-price-input" step="0.01" min="0" readonly>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger removeItemBtn">-</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Total geral -->
                        <div class="form-group col-md-6 offset-md-6">
                            <label for="total_amount" class="form-label">Total da Venda</label>
                            <input type="number" id="total_amount" name="total_amount" class="form-control" step="0.01" readonly value="0.00">
                        </div>

                        <div class="form-group col-12">
                            <button type="submit" class="btn btn-primary">Registrar Venda</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('push-javascript') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        let itemIndex = 1;

        // Inicializa Select2 para pesquisa no select de produtos
        $('.product-select').select2({
            placeholder: 'Selecione o produto',
            allowClear: true,
            width: '100%'
        });

        // Atualiza totais e campos relacionados
        function updateTotals() {
            let totalSale = 0;
            $('#saleItemsTable tbody tr').each(function() {
                const qty = parseFloat($(this).find('.quantity-input').val()) || 0;
                const unitPrice = parseFloat($(this).find('.unit-price-input').val()) || 0;
                const iva = unitPrice * 0.14;
                const total = (unitPrice + iva) * qty;

                $(this).find('.iva-input').val(iva.toFixed(2));
                $(this).find('.total-price-input').val(total.toFixed(2));
                totalSale += total;
            });
            $('#total_amount').val(totalSale.toFixed(2));
        }

        // Atualiza preço unitário, estoque e limites ao mudar produto
        $('#saleItemsTable').on('change', '.product-select', function() {
            const $row = $(this).closest('tr');
            const selectedOption = $(this).find('option:selected');
            const price = parseFloat(selectedOption.data('price')) || 0;
            const stock = parseInt(selectedOption.data('stock')) || 0;

            $row.find('.unit-price-input').val(price.toFixed(2));
            $row.find('.quantity-input').attr('max', stock);
            $row.find('.quantity-input').val(stock > 0 ? 1 : 0);
            $row.find('.stock-info').text('Disponível: ' + stock);

            updateTotals();
        });

        // Limita quantidade ao estoque disponível
        $('#saleItemsTable').on('input', '.quantity-input', function() {
            const max = parseInt($(this).attr('max')) || 0;
            let val = parseInt($(this).val()) || 0;
            if (val > max) {
                $(this).val(max);
                val = max;
            } else if (val < 1) {
                $(this).val(1);
                val = 1;
            }
            updateTotals();
        });

        // Adicionar nova linha de item
        $('#addItemBtn').click(function() {
            const newRow = `<tr>
            <td>
                <select name="items[${itemIndex}][product_id]" class="form-control product-select" required>
                    <option value="">Selecione o produto</option>
                    <?php foreach ($products as $product): ?>
                        <option 
                            value="<?= esc($product['id']) ?>" 
                            data-price="<?= esc($product['selling_price']) ?>" 
                            data-stock="<?= esc($product['quantity_available'] ?? 0) ?>"
                        >
                            <?= esc($product['product_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity-input" min="1" value="1" required>
                <small class="text-muted stock-info"></small>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][unit_price]" class="form-control unit-price-input" step="0.01" min="0" readonly>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][iva]" class="form-control iva-input" step="0.01" min="0" readonly>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][total_price]" class="form-control total-price-input" step="0.01" min="0" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger removeItemBtn">-</button>
            </td>
        </tr>`;
            $('#saleItemsTable tbody').append(newRow);
            // Inicializa Select2 no novo select
            $(`select[name="items[${itemIndex}][product_id]"]`).select2({
                placeholder: 'Selecione o produto',
                allowClear: true,
                width: '100%'
            });
            itemIndex++;
        });

        // Remover linha de item
        $('#saleItemsTable').on('click', '.removeItemBtn', function() {
            if ($('#saleItemsTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
                updateTotals();
            }
        });

        $('#barcode_input').on('keypress', function(e) {
            if (e.which === 13) { // Enter pressionado
                e.preventDefault();
                const barcode = $(this).val().trim();
                if (!barcode) return;

                // Busca produto via API
                $.ajax({
                    url: '<?= base_url('api/product-by-barcode') ?>/' + encodeURIComponent(barcode),
                    method: 'GET',
                    success: function(product) {
                        console.log(product);
                        let exists = false;
                        $('#saleItemsTable tbody tr').each(function() {
                            const prodId = $(this).find('.product-select').val();
                            if (prodId == product.id) {
                                // Incrementa quantidade, respeitando estoque
                                const qtyInput = $(this).find('.quantity-input');
                                let currentQty = parseInt(qtyInput.val()) || 0;
                                const maxQty = parseInt(qtyInput.attr('max')) || 0;
                                if (currentQty < maxQty) {
                                    qtyInput.val(currentQty + 1).trigger('input');
                                }
                                exists = true;
                                return false; // sai do each
                            }
                        });

                        if (!exists) {
                            // Adiciona nova linha com o produto
                            const newIndex = $('#saleItemsTable tbody tr').length;
                            const newRow = `<tr>
                        <td>
                            <select name="items[${newIndex}][product_id]" class="form-control product-select" required>
                                <option value="">Selecione o produto</option>
                                <option value="${product.id}" data-price="${product.selling_price}" data-stock="${product.stock_available}" selected>${product.name}</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[${newIndex}][quantity]" class="form-control quantity-input" min="1" max="${product.stock_available}" value="1" required>
                            <small class="text-muted stock-info">Disponível: ${product.stock_available}</small>
                        </td>
                        <td>
                            <input type="number" name="items[${newIndex}][unit_price]" class="form-control unit-price-input" step="0.01" min="0" value="${product.selling_price}" readonly>
                        </td>
                        <td>
                            <input type="number" name="items[${newIndex}][iva]" class="form-control iva-input" step="0.01" min="0" readonly>
                        </td>
                        <td>
                            <input type="number" name="items[${newIndex}][total_price]" class="form-control total-price-input" step="0.01" min="0" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger removeItemBtn">-</button>
                        </td>
                    </tr>`;
                            $('#saleItemsTable tbody').append(newRow);

                            // Inicializa Select2 no novo select
                            $(`select[name="items[${newIndex}][product_id]"]`).select2({
                                placeholder: 'Selecione o produto',
                                allowClear: true,
                                width: '100%'
                            });
                        }

                        // Atualiza totais
                        updateTotals();

                        // Limpa input código de barras
                        $('#barcode_input').val('');
                    },
                    error: function() {
                        alert('Produto não encontrado para o código de barras informado.');
                    }
                });
            }
        });

        // Inicializa totais na carga da página
        updateTotals();
    });
</script>

<?= $this->endSection() ?>