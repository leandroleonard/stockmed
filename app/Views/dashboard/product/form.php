<?= $this->extend('templates/dashboard') ?>
<?= $this->section('content') ?>

<div class="pc-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Produtos</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript: void(0)">Produtos</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0)">Criar</a></li>
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
                    <h5>Novo Produto</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('dashboard/stock/add') ?>" class="row">
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


                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Nome do Produto</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Nome do produto" value="<?= $product ? $product['name'] : old('name') ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="manufacturer_id">ID do Fabricante</label>
                                <select class="form-control" id="manufacturer_id" name="manufacturer_id">
                                    <?php if ($product): ?>
                                        <option value="<?= $product['manufacturer_id'] ?>"><?= $product['manufacturer_name'] ?></option>
                                    <?php endif ?>
                                    <?php foreach ($manufacturers as $manufacturer): ?>
                                        <option value="<?= $manufacturer['id'] ?>"><?= $manufacturer['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="batch_number">Lote</label>
                                <input type="text" class="form-control" id="batch_number" name="batch_number"
                                    placeholder="Nº Lote" value="<?= $product ? $product['batch_number'] : old('batch_number') ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="supplier_id">ID do Fornecedor</label>
                                <select class="form-control" class="form-control" id="supplier_id" name="supplier_id">
                                    <?php if ($product): ?>
                                        <option value="<?= $product['supplier_id'] ?>"><?= $product['supplier_name'] ?></option>
                                    <?php endif ?>
                                    <?php foreach ($suppliers as $supplier): ?>
                                        <option value="<?= $supplier['id'] ?>"><?= $supplier['company_name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="manufacture_date">Data de Fabricação</label>
                                <input type="date" class="form-control" id="manufacture_date" name="manufacture_date"
                                    value="<?= $product ? $product['manufacture_date'] : old('manufacture_date') ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="expiry_date">Data de Expiração</label>
                                <input type="date" class="form-control" id="expiry_date" name="expiry_date"
                                    value="<?= $product ? $product['expiry_date'] : old('expiry_date') ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="quantity_received">Quantidade Recebida</label>
                                <input type="number" class="form-control" id="quantity_received" name="quantity_received"
                                    placeholder="Quantidade" value="<?= $product ? $product['quantity_received'] : old('quantity_received') ?>" required min="1">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="cost_price">Preço de Custo</label>
                                <input type="number" step="0.01" class="form-control" id="cost_price" name="cost_price"
                                    placeholder="Preço de Custo" value="<?= $product ? $product['cost_price'] : old('cost_price') ?>" required min="0">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="selling_price">Preço de Venda</label>
                                <input type="number" step="0.01" class="form-control" id="selling_price" name="selling_price"
                                    placeholder="Preço de Venda" value="<?= $product ? $product['selling_price'] : old('selling_price') ?>" required min="0">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="warehouse_id">ID do Armazém</label>
                                <select name="warehouse_id" id="warehouse_id" class="form-control">
                                    <?php foreach ($warehouses as $warehouse): ?>
                                        <option value="<?= $warehouse['id'] ?>"><?= $warehouse['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-12">
                            <button type="submit" class="btn btn-primary mb-4">Salvar</button>
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

<?= $this->endSection() ?>