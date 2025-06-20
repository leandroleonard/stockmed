<?= $this->extend('templates/dashboard') ?>
<?= $this->section('content') ?>

<div class="pc-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Armazens</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard/storage') ?>">Armazens</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0)"><?= $warehouse ? 'Actualizar Armazen' : 'Criar' ?></a></li>
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
                    <h5>Novo Armazem</h5>

                    <?php if ($warehouse): ?>
                        <button class="btn btn-sm btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmDeleteModal"
                            data-delete-url="<?= base_url('dashboard/storage/delete/' . $warehouse['warehouse_code']) ?>">
                            Eliminar
                        </button>
                    <?php endif ?>

                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('dashboard/storage/submit') ?>" class="row">
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
                                <label class="form-label" for="name">Nome do Armazem</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Nome do Armazen" value="<?= $warehouse ? $warehouse['name'] : old('name')   ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="description">Descrição</label>
                                <input type="text" class="form-control" id="description" name="description"
                                    placeholder="Descrição" value="<?= $warehouse ? $warehouse['description'] : old('description')   ?>" required>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="address">Localização</label>
                                <input type="text" class="form-control" id="address" name="address"
                                    placeholder="Localização" value="<?= $warehouse ? $warehouse['address'] : old('address')   ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="manager_id">ID do Usuário Gerente</label>
                                <select class="form-control" class="form-control" id="manager_id" name="manager_id">
                                    <?php if ($warehouse && $warehouse['manager_id']): ?>
                                        <option value="<?= $warehouse['manager_id'] ?>"><?= $warehouse['manager_name'] ?></option>
                                    <?php endif ?>

                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>"><?= $user['first_name'] . ' ' . $user['last_name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>

                        <?php if ($warehouse): ?>
                            <input type="hidden" value="<?= $warehouse['warehouse_code'] ?>" id="warehouse_code" name="warehouse_code">
                        <?php endif ?>


                        <div class="form-group col-12">
                            <button type="submit" class="btn btn-primary mb-4">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeleteLabel">Confirmar Eliminação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja eliminar este armazen?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Sim, eliminar</a>
                </div>
            </div>
        </div>
    </div>


</div>
<?= $this->endSection() ?>

<?= $this->section('push-javascript') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    const confirmDeleteModal = document.getElementById('confirmDeleteModal');
    confirmDeleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const url = button.getAttribute('data-delete-url');
        const confirmBtn = confirmDeleteModal.querySelector('#confirmDeleteBtn');
        confirmBtn.setAttribute('href', url);
    });
</script>


<?= $this->endSection() ?>