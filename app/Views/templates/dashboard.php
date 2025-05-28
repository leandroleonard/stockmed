<!DOCTYPE html>
<html lang="pt">

<head>
    <title>StockMED</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Grupo Omicron">

    <link rel="icon" href="<?= base_url('assets/images/favicon.svg')?>" type="image/x-icon"> <!-- [Google Font] Family -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="<?= base_url('assets/fonts/tabler-icons.min.css')?>">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="<?= base_url('assets/fonts/feather.css')?>">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="<?= base_url('assets/fonts/fontawesome.css')?>">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="<?= base_url('assets/fonts/material.css')?>">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css')?>" id="main-style-link">
    <link rel="stylesheet" href="<?= base_url('assets/css/style-preset.css')?>">

    <?= $this->renderSection("push-css") ?>

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Sidebar Menu ] start -->
    <nav class="pc-sidebar">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="#" class="b-brand text-primary" style="font-size: 1.75rem;font-weight: 700">
                    STOCKMED
                </a>
            </div>
            <div class="navbar-content">
                <ul class="pc-navbar">
                    <li class="pc-item">
                        <a href="<?= base_url('dashboard') ?>" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                            <span class="pc-mtext">Dashboard</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="<?= base_url('dashboard/storage') ?>" class="pc-link">
                            <span class="pc-micon"><i class="fa fa-archive"></i></span>
                            <span class="pc-mtext">Armazem</span>
                        </a>
                    </li>

                    <!-- Artigos -->
                    <li class="pc-item pc-hasmenu">
                        <a href="#!" class="pc-link"><span class="pc-micon"><i class="fa fa-archive"></i></span><span class="pc-mtext">
                                Artigos</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
                        <ul class="pc-submenu">
                            <li class="pc-item"><a class="pc-link" href="<?= base_url('dashboard/artigos/marcas-modelo') ?>">Marcas & Modelos</a></li>
                            <li class="pc-item"><a class="pc-link" href="<?= base_url('dashboard/tipo-artigos') ?>">Tipos de artigos</a></li>
                            <li class="pc-item"><a class="pc-link" href="<?= base_url('dashboard/stock') ?>">Stock</a></li>
                        </ul>
                    </li>
                    <!-- End artigos -->

                    <!-- Cliente -->
                    <li class="pc-item">
                        <a href="<?= base_url('dashboard/clients') ?>" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-users"></i></span>
                            <span class="pc-mtext">Clientes</span>
                        </a>
                    </li>

                    <li class="pc-item">
                        <a href="<?= base_url('dashboard/buy') ?>" class="pc-link">
                            <span class="pc-micon"><i class="fa fa-archive"></i></span>
                            <span class="pc-mtext">Compras</span>
                        </a>
                    </li>

                    <li class="pc-item pc-hasmenu">
                        <a href="<?= base_url('dashboard/settings') ?>" class="pc-link"><span class="pc-micon"><i class="fa fa-cog"></i></span><span class="pc-mtext">
                                Definições</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
                        <ul class="pc-submenu">
                            <li class="pc-item"><a class="pc-link" href="#!">Formas de pagamentos</a></li>
                            <li class="pc-item"><a class="pc-link" href="#!">Utilizadores & Permissões</a></li>
                        </ul>
                    </li>

                    <!-- Fornecedores -->
                    <li class="pc-item">
                        <a href="<?= base_url('dashboard/suppliers') ?>" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-users"></i></span>
                            <span class="pc-mtext">Fornecedores</span>
                        </a>
                    </li>

                    <li class="pc-item">
                        <a href="<?= base_url('dashboard/sales') ?>" class="pc-link">
                            <span class="pc-micon"><i class="fa fa-archive"></i></span>
                            <span class="pc-mtext">Vendas</span>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    <!-- [ Sidebar Menu ] end --> <!-- [ Header Topbar ] start -->
    <header class="pc-header">
        <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
                    <!-- ======= Menu collapse Icon ===== -->
                    <li class="pc-h-item pc-sidebar-collapse">
                        <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="pc-h-item pc-sidebar-popup">
                        <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="dropdown pc-h-item d-inline-flex d-md-none">
                        <a
                            class="pc-head-link dropdown-toggle arrow-none m-0"
                            data-bs-toggle="dropdown"
                            href="#"
                            role="button"
                            aria-haspopup="false"
                            aria-expanded="false">
                            <i class="ti ti-search"></i>
                        </a>
                        <div class="dropdown-menu pc-h-dropdown drp-search">
                            <form class="px-3">
                                <div class="form-group mb-0 d-flex align-items-center">
                                    <i data-feather="search"></i>
                                    <input type="search" class="form-control border-0 shadow-none" placeholder="Pesquisar Fármaco. . .">
                                </div>
                            </form>
                        </div>
                    </li>
                    <li class="pc-h-item d-none d-md-inline-flex">
                        <form class="header-search">
                            <i data-feather="search" class="icon-search"></i>
                            <input type="search" class="form-control" placeholder="Pesquisar Fármaco. . .">
                        </form>
                    </li>
                </ul>
            </div>
            <!-- [Mobile Media Block end] -->
            <div class="ms-auto">
                <ul class="list-unstyled">
                    <li class="dropdown pc-h-item">
                        <a
                            class="pc-head-link dropdown-toggle arrow-none me-0"
                            data-bs-toggle="dropdown"
                            href="#"
                            role="button"
                            aria-haspopup="false"
                            aria-expanded="false">
                            <i class="ti ti-bell"></i>
                        </a>
                        <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header d-flex align-items-center justify-content-between">
                                <h5 class="m-0">Notification</h5>
                                <a href="#!" class="pc-head-link bg-transparent"><i class="ti ti-x text-danger"></i></a>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div class="dropdown-header px-0 text-wrap header-notification-scroll position-relative" style="max-height: calc(100vh - 215px)">
                                <div class="list-group list-group-flush w-100">
                                    <a class="list-group-item list-group-item-action">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img src="<?= base_url('assets/images/user/avatar-2.jpg')?>" alt="user-image" class="user-avtar">
                                            </div>
                                            <div class="flex-grow-1 ms-1">
                                                <span class="float-end text-muted">3:00 AM</span>
                                                <p class="text-body mb-1">It's <b>Cristina danny's</b> birthday today.</p>
                                                <span class="text-muted">2 min ago</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div class="text-center py-2">
                                <a href="#!" class="link-primary">Ver todas</a>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown pc-h-item header-user-profile">
                        <a
                            class="pc-head-link dropdown-toggle arrow-none me-0"
                            data-bs-toggle="dropdown"
                            href="#"
                            role="button"
                            aria-haspopup="false"
                            data-bs-auto-close="outside"
                            aria-expanded="false">
                            <img src="<?= base_url('assets/images/user/avatar-2.jpg')?>" alt="user-image" class="user-avtar">
                            <span>Etianete Reepson</span>
                        </a>
                        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header">
                                <div class="d-flex mb-1">
                                    <div class="flex-shrink-0">
                                        <img src="<?= base_url('assets/images/user/avatar-2.')?>" alt="user-image" class="user-avtar wid-35">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Etianete Reepson</h6>
                                        <span>UI/UX Designer</span>
                                    </div>
                                    <a href="#!" class="pc-head-link bg-transparent"><i class="ti ti-power text-danger"></i></a>
                                </div>
                            </div>
                            <ul class="nav drp-tabs nav-fill nav-tabs" id="mydrpTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link active"
                                        id="drp-t1"
                                        data-bs-toggle="tab"
                                        data-bs-target="#drp-tab-1"
                                        type="button"
                                        role="tab"
                                        aria-controls="drp-tab-1"
                                        aria-selected="true"><i class="ti ti-user"></i> Profile</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link"
                                        id="drp-t2"
                                        data-bs-toggle="tab"
                                        data-bs-target="#drp-tab-2"
                                        type="button"
                                        role="tab"
                                        aria-controls="drp-tab-2"
                                        aria-selected="false"><i class="ti ti-settings"></i> Setting</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="mysrpTabContent">
                                <div class="tab-pane fade show active" id="drp-tab-1" role="tabpanel" aria-labelledby="drp-t1" tabindex="0">
                                    <a href="#!" class="dropdown-item">
                                        <i class="ti ti-edit-circle"></i>
                                        <span>Edit Profile</span>
                                    </a>
                                    <a href="#!" class="dropdown-item">
                                        <i class="ti ti-user"></i>
                                        <span>View Profile</span>
                                    </a>
                                    <a href="#!" class="dropdown-item">
                                        <i class="ti ti-clipboard-list"></i>
                                        <span>Social Profile</span>
                                    </a>
                                    <a href="#!" class="dropdown-item">
                                        <i class="ti ti-wallet"></i>
                                        <span>Billing</span>
                                    </a>
                                    <a href="#!" class="dropdown-item">
                                        <i class="ti ti-power"></i>
                                        <span>Logout</span>
                                    </a>
                                </div>
                                <div class="tab-pane fade" id="drp-tab-2" role="tabpanel" aria-labelledby="drp-t2" tabindex="0">
                                    <a href="#!" class="dropdown-item">
                                        <i class="ti ti-help"></i>
                                        <span>Support</span>
                                    </a>
                                    <a href="#!" class="dropdown-item">
                                        <i class="ti ti-user"></i>
                                        <span>Account Settings</span>
                                    </a>
                                    <a href="#!" class="dropdown-item">
                                        <i class="ti ti-lock"></i>
                                        <span>Privacy Center</span>
                                    </a>
                                    <a href="#!" class="dropdown-item">
                                        <i class="ti ti-messages"></i>
                                        <span>Feedback</span>
                                    </a>
                                    <a href="#!" class="dropdown-item">
                                        <i class="ti ti-list"></i>
                                        <span>History</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <!-- [ Header ] end -->



    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- [Page Specific JS] start -->
    <script src="<?= base_url('assets/js/plugins/apexcharts.min.js')?>"></script>
    <script src="<?= base_url('assets/js/pages/dashboard-default.js')?>"></script>
    <!-- [Page Specific JS] end -->
    <!-- Required Js -->
    <script src="<?= base_url('assets/js/plugins/popper.min.js')?>"></script>
    <script src="<?= base_url('assets/js/plugins/simplebar.min.js')?>"></script>
    <script src="<?= base_url('assets/js/plugins/bootstrap.min.js')?>"></script>
    <script src="<?= base_url('assets/js/fonts/custom-font.js')?>"></script>
    <script src="<?= base_url('assets/js/pcoded.js')?>"></script>
    <script src="<?= base_url('assets/js/plugins/feather.min.js')?>"></script>

    <?= $this->renderSection("push-javascript") ?>

    <script>
        layout_change('light');
    </script>




    <script>
        change_box_container('false');
    </script>



    <script>
        layout_rtl_change('false');
    </script>


    <script>
        preset_change("preset-1");
    </script>


    <script>
        font_change("Public-Sans");
    </script>



</body>
<!-- [Body] end -->

</html>