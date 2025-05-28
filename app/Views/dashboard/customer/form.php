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
                    <h5>Clientes</h5>

                </div>
                <div class="card-body">
                    <form action="#" class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label" for="nome">Nome</label>
                            <input type="text" class="form-control" id="nome"
                                placeholder="Nome do cliente">
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control" id="email"
                                placeholder="Email do cliente">
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label" for="nif">NIF</label>
                            <input type="text" class="form-control" id="email"
                                placeholder="0000000000XX00">
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label" for="forma_pagamento_id">Forma de pagamento</label>
                            <select name="forma_pagamento_id" id="forma_pagamento_id" class="form-control">
                                <option>Selecione a forma</option>
                            </select>
                        </div>

                        <div class="form-group col-12">
                            <label class="form-label" for="obs">Observação</label>
                            <textarea name="obs" id="obs" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="form-group col-12">
                            <button type="submit" class="btn btn-primary mb-4">Criar</button>

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