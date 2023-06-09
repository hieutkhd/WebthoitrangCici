<?php
    $modules = 'cate-products';
    $title_global = 'Danh sách danh mục sản phẩm ';
    require_once __DIR__ .'/../../autoload.php';
    $category_products = Pagination::pagination('category_products','','page',10);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title> <?= isset($title_global) ? $title_global : 'Trang admin ' ?>  </title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <?php require_once __DIR__ .'/../../layouts/inc_css.php'; ?>
        <!-- <link rel="stylesheet" href="/public/admin/css/bootstrap-tagsinput.css"> -->
    </head>
    <body class="hold-transition skin-blue fixed sidebar-mini">
        <!-- Site wrapper -->
        <div class="wrapper">
            
            <?php require_once __DIR__ .'/../../layouts/inc_header.php'; ?>
            <!-- ======================HEADER========================= -->
            <?php require_once __DIR__ .'/../../layouts/inc_sidebar.php'; ?>
            <!-- =======================SIDEBAR======================== -->
            <!-- ======================= CONTENT======================== -->
            <div class="content-wrapper">
                <section class="content-header">
                    <h1>
                        <?= isset($title_global) ? $title_global : '' ?>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                        <li><a href="#">Danh mục sản phẩm </a></li>
                        <li class="active"> Danh sách</li>
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">
                    <!-- Default box -->
                    <div class="box">
                        <div class="box-header with-border">
                            <a href="add.php" class="btn btn-xs btn-success"><i class="fa fa-plus"></i> Thêm mới </a>
                        </div>
                        <div class="box-body">
                            <div class="box-body table-responsive no-padding">
                                <table class="table table-hover border">
                                    <tbody>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tên danh mục</th>
                                            <th>Các danh mục</th>
                                            <!-- <th>Hot</th> -->
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                        <?php foreach ($category_products as $key => $cate): ?>
                                            <tr>
                                                <td><?= $cate['id'] ?></td>
                                                <td> <?= $cate['cpr_name'] ?></td>
                                                <td>
                                                    <?php
                                                        if (intval($cate['cpr_parent_id']) == 0) {
                                                            echo "Danh mục cha";
                                                        } else {
                                                            $parent = DB::fetchOne('category_products', intval($cate['cpr_parent_id']));
                                                            echo $parent['cpr_name'];
                                                        }
                                                    ?>
                                                </td>
                                               <!--  <td><a href="hot.php?id=<?= $cate['id'] ?>" class="custome-btn label <?= $cate['cpr_hot'] == 1 ? 'label-info' : 'label-default' ?>"><span><?= $cate['cpr_hot'] == 1 ? 'Hot' : 'None' ?></span></a></td> -->
                                                <td><a href="active.php?id=<?= $cate['id'] ?>" class="custome-btn label <?= $cate['cpr_active'] == 1 ? 'label-info' : 'label-default' ?>"><span><?= $cate['cpr_active'] == 1 ? 'Active' : 'Hide' ?></span></a></td>
                                                <td>
                                                    <a href="update.php?id=<?= $cate['id'] ?>" class="custome-btn btn-info btn-xs"><i class="fa fa-pencil-square"></i> Chỉnh sửa </a>
                                                    <a href="delete.php?id=<?= $cate['id'] ?>" class="custome-btn btn-danger btn-xs delete" ><i class="fa fa-trash"></i> Xóa </a>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <div class="custome-paginate">
                                <div class="pull-right">
                                    <?php echo Pagination::getListpage() ?>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-footer-->
                    </div>
                    <!-- /.box -->
                </section>
            </div>
            <!-- =======================END CONTENT======================== -->
            <?php require_once __DIR__ .'/../../layouts/inc_footer.php'; ?>
        </div>
        <?php require_once __DIR__ .'/../../layouts/inc_js.php'; ?>
