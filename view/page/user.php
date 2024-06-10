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
                        <div class="col-md-12 grid-margin">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <!-- <h4 class="font-weight-bold mb-0">RoyalUI Dashboard</h4> -->
                                </div>
                                <div>
                                    <a href="/tambah_user.php" type="button" class="btn btn-primary btn-icon-text btn-rounded text-white">
                                        <i class="ti-clipboard btn-icon-prepend"></i>Tambah
                                    </a>
                                </div>
                            </div>
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
                                                <th>Aksi</th>
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
                                                    <td><label class="badge badge-danger">Pending</label></td>
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
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © <a href="https://www.bootstrapdash.com/" target="_blank">bootstrapdash.com </a>2021</span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Only the best <a href="https://www.bootstrapdash.com/" target="_blank"> Bootstrap dashboard </a> templates</span>
                    </div>
                </footer>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
    </div>

    <?php include("../component/plugin.php"); ?>
</body>

</html>