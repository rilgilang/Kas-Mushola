<?php
include '../../bootstrap/db.php';
include '../../middleware/auth.php';
include '../../controller/user.controller.php';

checkLogin();
?>
<!DOCTYPE html>
<html>

<head>
    <?php include("../component/header.php"); ?>
</head>

<body>
    <div class="container-scroller">
        <?php include("../component/navbar.php"); ?>
        <div class="container-fluid page-body-wrapper">
            <?php include("../component/sidebar.php"); ?>

            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                    </div>
                    <div class="col-lg-12 grid-margin stretch-card">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <?php if (isAdmin()) : ?>
                                <a href="./tambah_user.php" class="btn btn-primary fw-bold text-white">Tambah User</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">User</h4>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Username</th>
                                                <th>Level</th>
                                                <?php if (isAdmin()) : ?>
                                                    <th>Aksi</th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $data = getUserList();
                                            foreach ($data as $key => $val) {
                                            ?>
                                                <tr>
                                                    <td><?= $key + 1 ?></td>
                                                    <td><?= $val['nama'] ?></td>
                                                    <td><?= $val['username'] ?></td>
                                                    <td><?= $val['usertype'] ?></td>
                                                    <?php if (isAdmin()) : ?>
                                                        <td>
                                                            <a type="button" href="./edit_user.php?id=<?= $val['id_user'] ?>" class="btn btn-outline-secondary btn-icon-text">
                                                                Edit
                                                                <i class="ti-file btn-icon-append"></i>
                                                            </a>
                                                            <a type="button" href="./delete_user.php?id=<?= $val['id_user'] ?>" class="btn btn-outline-danger btn-icon-text">
                                                                <i class="ti-upload btn-icon-prepend"></i>
                                                                Hapus
                                                            </a>
                                                        </td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Footer inside content-wrapper -->
                    <footer class="footer mt-auto py-3">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6 text-center text-md-left">
                                </div>
                                <div class="col-md-6 text-center text-md-right text-white row">
                                    <!-- <span>Oleh :</span>
                                <span>Deslia Miratunnisa (213210018)</span>
                                <span>Sistem Informasi Akuntansi</span>
                                <span>Univ Teknologi Digital Indonesia</span>
                                <span>Yogyakarta</span> -->
                                </div>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
    </div>

    <?php include("../component/plugin.php"); ?>
</body>

</html>