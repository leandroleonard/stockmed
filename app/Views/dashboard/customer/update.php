<?= $this->extend('templates/dashboard') ?>
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
                        <li class="breadcrumb-item"><a href="javascript: void(0)">Clientes</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0)">Actualizar</a></li>
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

                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('dashboard/clients/update') ?>" class="row">
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
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label" for="first_name">Nome</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                        placeholder="Nome" value="<?= $customer['first_name'] ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="last_name">Sobrenome</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                        placeholder="Sobrenome" value="<?= $customer['last_name'] ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Email do cliente" value="<?= $customer['email'] ?>">
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label" for="phone">Telefone</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                placeholder="921252910" minlength="9" maxlength="9" value="<?= $customer['phone'] ?>">
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label" for="address">Endereço</label>
                            <input type="text" class="form-control" id="address" name="address"
                                placeholder="Morro Bento, 123 - Centro" value="<?= $customer['address'] ?>">
                        </div>

                        <div class="form-group col-12">
                            <label class="form-label" for="obs">Observação</label>
                            <textarea id="obs" class="form-control" name="notes" rows="4"><?= $customer['notes'] ?></textarea>
                        </div>

                        <input type="hidden" id="id" name="id" value="<?= $customer['customer_code'] ?>">

                        <div class="form-group col-12">
                            <button type="submit" class="btn btn-primary mb-4">Actualizar</button>
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