<?php
    $modules = 'products';
    $title_global = 'Cập Nhật Sản Phẩm ';
    require_once __DIR__ .'/../../autoload.php';
    $id = Input::get('id');
    $product = DB::fetchOne('products', intval($id));
    $sizes = DB::query('sizes');
    $colors = DB::query('colors');
    if( !$product ) {
        $_SESSION['error'] = "  Không tồn tại dữ liệu ";
        header("Location: ".baseServerName().'/admin/modules/products');exit();
    }
    //load danh muc san pham
    $catePro = DB::query('category_products', '*', ' AND cpr_parent_id = 0');
    foreach ($catePro as $key => $category) {
        $catePro[$key]['sub'] = DB::query('category_products', '*', ' AND cpr_parent_id = '. $category['id']);
    }

    // kiem tra neu submit 
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        /**
         *  lay giá trị từ input
         */
        $name     = Input::get("prd_name");
        $cate       = Input::get("prd_category_product_id");
        $keywords   = Input::get("prd_keywords");
        $description     = Input::get("prd_description");
        $hot = Input::get('prd_hot');
        $active  = Input::get('prd_active');
        $number  = Input::get('prd_number');
        $sale  = Input::get('prd_sale');    
        $content  = Input::get('prd_content');
        $price  = Input::get('prd_price');
        $size  = !empty(Input::get('size')) ? implode(',', Input::get('size')) : '';
        $color  = !empty(Input::get('color')) ? implode(',', Input::get('color')) : '';
        // kiểm tra thông tin lỗi trên form
        $errors['name'] = $name == '' ? 'Mời bạn điền đầy đủ thông tin' : null;  
        $errors['cate'] = $cate == '' ? 'Mời bạn điền đầy đủ thông tin' : null;
        $errors['keywords'] = $keywords == '' ? 'Mời bạn điền đầy đủ thông tin' : null;
        $errors['description'] = $description == '' ? 'Mời bạn điền đầy đủ thông tin' : null;
        $errors['number'] = $number == '' ? 'Mời bạn điền đầy đủ thông tin' : null;
        $errors['content'] = $content == '' ? 'Mời bạn điền đầy đủ thông tin' : null;
        $errors['price'] = $price == '' ? 'Mời bạn điền đầy đủ thông tin' : null;
        $errors['size'] = $size == '' ? 'Mời bạn điền đầy đủ thông tin' : null;
        $errors['color'] = $color == '' ? 'Mời bạn điền đầy đủ thông tin' : null;

        if ( isset ($_FILES['prd_thunbar']) && $_FILES['prd_thunbar']['name'] != NULL ) {
            $file_name = $_FILES['prd_thunbar']['name'];
            $file_tmp  = $_FILES['prd_thunbar']['tmp_name'];
            $file_type = $_FILES['prd_thunbar']['type'];
            $file_erro = $_FILES['prd_thunbar']['error'];
            if ($file_erro == 0)
            {
                $hinhanh = $file_name;
                $_SESSION['hinhanh'] = $hinhanh;
            }
        }

        //  chuyen doi mang chi muc - loai bo key trung nhau 
        if( isset ($errors )) {
            $error = (array_unique(array_values($errors)));
        }
    
        // neu bien errors  thi ko co loi tien hanh insert
        if ( count($error) == 1)
        {
            // gán vào 1 mảng giá trị để insertt 
            $data = 
            [
                'prd_name'                   => $name ,
                'prd_slug'                   => str_slug($name),
                'prd_category_product_id'    => $cate,
                'prd_keywords'               => $keywords,
                'prd_description'            => $description,
                'prd_active'                 => $active ,
                'prd_hot'                    => $hot,
                'prd_number'                 => $number,
                'prd_content'                => $content,
                'prd_price'                  => $price,
                'prd_sale'                   => $sale,
                'pro_size'                   => $size,
                'pro_color'                   => $color,
                'created_at'                  => date('Y-m-d H:i'),
                'updated_at'                  => date('Y-m-d H:i'),
            ];

            // kiểm tra nếu có tồn tại hình ảnh thì gán dữ liệu update
            if (isset($hinhanh)) {
                $data['prd_thunbar']  = $hinhanh;
            }
    
            //tiến hành update 
            $id_update = DB::update('products',$data , ' id = '.$id);

            if($id_update > 0)
            {
                // insert thanh cong
                // gán session thông báo thành công
                //chuyển về trang index trong thư mục category_products
                move_uploaded_file($file_tmp,UPLOADS.'/products/'.$hinhanh);
                $_SESSION['success'] = "Cập nhật thành công ";
                header("Location: ".baseServerName().'/admin/modules/products');exit();
            } else {
                // gán session thông báo thất bại
                // giữ nguyên trang để nhập lai
                $_SESSION['error'] = "Cập nhật thất bại  ";
                header("Location: ".baseServerName().'/admin/modules/products');exit();
            }
        }
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title> <?= isset($title_global) ? $title_global : 'Trang admin ' ?>  </title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <?php require_once __DIR__ .'/../../layouts/inc_css.php'; ?>
        <script type="text/javascript" src="/public/admin/ckeditor/ckeditor.js"></script>
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
                        <li><a href="/admin"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                        <li><a href="#">Sản phẩm </a></li>
                        <li class="active">Thêm mới</li>
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">
                    <!-- Default box -->
                    <div class="box">
                        <div class="box-body">
                            <div class="box box-primary">
                                <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                                    <div class="box-body">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="inputEmail3" class="col-sm-2 control-label"> Thunbar   </label>
                                                <div class="col-sm-10">
                                                    <input type="file" class="form-control" name="prd_thunbar" id="imgInp">
                                                    <?php if( isset( $errors['hinhanh']) ): ?>
                                                        <span class="color-red"><i class="fa fa-bug"></i><?= $errors['hinhanh'] ?></span>
                                                    <?php endif ; ?>
                                                </div>
                                                <div class="col-sm-10" style="margin-top: 10px;margin-left: 17%">
                                                    <img src="/public/uploads/products/<?= $product['prd_thunbar'] ?>" alt="" class="img img-responsive" id="blah" title=" Logo " style="width: 100%;height: 258px;border: 1px solid #dedede">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <label class="control-label"> Danh mục <span class="color-red">(*)</span></label>
                                                <select class="form-control" name="prd_category_product_id">
                                                    <option value=""> - Chọn danh mục  - </option>
                                                    <?php if(count($catePro) > 0) :?>
                                                        <?php foreach($catePro as $catep) :?>
                                                            <?php if(!empty($catep['sub'])) : ?>
                                                                <option value="<?= $catep['id'] ?>" <?= $product['prd_category_product_id'] && $product['prd_category_product_id'] == $catep['id'] ? 'selected="selected"' : '' ?> ><?php echo $catep['cpr_name'] ?></option>
                                                                <?php foreach ($catep['sub'] as $sub ): ?>
                                                                    <option value="<?php echo $sub['id'] ?>"  <?= $product['prd_category_product_id'] && $product['prd_category_product_id'] == $sub['id'] ? 'selected="selected"' : '' ?>>
                                                                        <?php echo '- '.$sub['cpr_name'] ?>
                                                                    </option>
                                                                <?php endforeach;?>

                                                            <?php else : ?>
                                                                <option value="<?= $catep['id'] ?>" <?= $product['prd_category_product_id'] && $product['prd_category_product_id'] == $catep['id'] ? 'selected="selected"' : '' ?> ><?php echo $catep['cpr_name'] ?></option>
                                                            <?php endif; ?>
                                                        <?php endforeach ;?>
                                                    <?php endif; ?>
                                                </select>
                                                <?php if( isset( $errors['cate']) ): ?>
                                                    <span class="color-red"><i class="fa fa-bug"></i><?= $errors['cate'] ?></span>
                                                <?php endif ; ?>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputEmail3" class="control-label"> Tiêu đề <span class="color-red">(*)</span></label>
                                                <input type="text" class="form-control" name="prd_name" value="<?= $product['prd_name'] ?>"  placeholder=" Tên sản phẩm không quá 200 từ" autocomplete="off">
                                                <?php if( isset( $errors['name']) ): ?>
                                                    <span class="color-red"><i class="fa fa-bug"></i><?= $errors['name'] ?></span>
                                                <?php endif ; ?>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputEmail3" class="control-label" style="margin-bottom: 10px;"> Keywords <span class="color-red">(*)</span></label>
                                                <textarea name="prd_keywords"  cols="10" rows="3" class="form-control" placeholder="Dùng để seo nội dung , không quá 100 ký tự"><?= $product['prd_keywords'] ?></textarea>
                                                <?php if( isset( $errors['keywords']) ): ?>
                                                    <span class="color-red"><i class="fa fa-bug"></i><?= $errors['keywords'] ?></span>
                                                <?php endif ; ?>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputEmail3" class="control-label" style="margin-bottom: 10px;"> Mô tả <span class="color-red">(*)</span></label>
                                                <textarea name="prd_description"  cols="10" rows="3" class="form-control" placeholder=" Mô tả ngắn về nội dung bài viết , không quá 250 ký tự"><?= $product['prd_description'] ?></textarea>
                                                <?php if( isset( $errors['description']) ): ?>
                                                    <span class="color-red"><i class="fa fa-bug"></i><?= $errors['description'] ?></span>
                                                <?php endif ; ?>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="form-group col-md-6 padding-default">
                                                <label for="" class="control-label"> Số Lượng  <span class="color-red">(*)</span></label>
                                                <input type="number" min="0" placeholder="số lượng sp " name="prd_number" class="form-control" value="<?= $product['prd_number'] ?>">
                                                <?php if( isset( $errors['number']) ): ?>
                                                    <span class="color-red"><i class="fa fa-bug"></i><?= $errors['number'] ?></span>
                                                <?php endif ; ?>
                                            </div>

                                            <div class="form-group col-md-6 padding-default" style="float: right;">
                                                <label for="" class="control-label"> Giá SP  <span class="color-red">(*)</span></label>
                                                <input type="number" min="0" placeholder=" Giá Sp" name="prd_price" class="form-control" value="<?= $product['prd_price'] ?>">
                                                <?php if( isset( $errors['price']) ): ?>
                                                    <span class="color-red"><i class="fa fa-bug"></i><?= $errors['price'] ?></span>
                                                <?php endif ; ?>
                                            </div>

                                            <div class="form-group col-md-6 padding-default">
                                                <label for="" class="control-label"> Size  <span class="color-red">(*)</span></label>
                                                <div class="col-sm-12 padding-default" style="margin-right: 0;margin-left: 0">
                                                    <?php
                                                        $productSize = explode(',', $product['pro_size']);
                                                    ?>
                                                    <select class="form-control select2" name="size[]" multiple="multiple">
                                                        <?php foreach($sizes as $size): ?>
                                                            <option <?php echo in_array($size['name'], $productSize) ? 'selected="selected"' : '' ?> value="<?= $size['name'] ?>"> <?= $size['name'] ?> </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?php if( isset( $errors['size']) ): ?>
                                                        <span class="color-red"><i class="fa fa-bug"></i><?= $errors['size'] ?></span>
                                                    <?php endif ; ?>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 padding-default" style="float: right">
                                                <label for="" class="control-label"> Màu  <span class="color-red">(*)</span></label>
                                                <div class="col-sm-12 padding-default" style="margin-right: 0;margin-left: 0">
                                                    <?php
                                                        $productColor = explode(',', $product['pro_color']);
                                                    ?>
                                                    <select class="form-control select2" name="color[]" multiple="multiple">
                                                        <?php foreach($colors as $color): ?>
                                                            <option <?php echo in_array($color['name'], $productColor) ? 'selected="selected"' : '' ?> value="<?= $color['name'] ?>"> <?= $color['name'] ?> </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?php if( isset( $errors['color']) ): ?>
                                                        <span class="color-red"><i class="fa fa-bug"></i><?= $errors['color'] ?></span>
                                                    <?php endif ; ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-12 padding-default"">
                                                <label for="" class="control-label"> Sale ( % )</label>
                                                <div class="col-sm-12 padding-default" style="margin-right: 0;margin-left: 0">
                                                    <input type="number" min="0" max="100" placeholder=" 1 - 100 (%)" name="prd_sale" class="form-control" value="<?= $product['prd_sale'] ?>" style="width: 100%;">
                                                </div>
                                            </div>

                                            <div class="form-group col-md-6 padding-default">
                                                <label for="" class="control-label"> Hot </label>
                                                <div class="col-sm-12 padding-default" style="margin-right: 0;margin-left: 0">
                                                    <select class="form-control" name="prd_hot">
                                                        <option <?= $product['prd_hot'] == 1 ? "selected='selected'" : '' ?> value="1"> Có </option>
                                                        <option <?= $product['prd_hot'] == 0 ? "selected='selected'" : '' ?> value="0"> Không </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-6 padding-default" style="float: right">
                                                <label for="" class="control-label"> Hiển thị </label>
                                                <div class="col-sm-12 padding-default" style="margin-right: 0;margin-left: 0">
                                                    <select class="form-control" name="prd_active">
                                                        <option <?= $product['prd_active'] == 1 ? "selected='selected'" : '' ?> value="1"> Có </option>
                                                        <option <?= $product['prd_active'] == 0 ? "selected='selected'" : '' ?>  value="0"> Không </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin:5px 0">
                                        <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left;margin-bottom: 10px;padding-right: 30px;padding-left: 30px;"> Nội dung </label>
                                        <div class="col-sm-12 padding-default" style="padding-left: 30px ;padding-right: 30px">
                                            <textarea name="prd_content" id="my-editor" cols="10" rows="10" class="form-control" placeholder=" Mời bạn nhập nội dung bài viết "><?= $product['prd_content'] ?></textarea>
                                            <?php if( isset( $errors['content']) ): ?>
                                                <span class="color-red"><i class="fa fa-bug"></i><?= $errors['content'] ?></span>
                                            <?php endif ; ?>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>

                                    <!-- /.box-body -->
                                    <div class="" style="position: fixed;right: 15px;top: 50%;transform: translateY(-50%);">
                                        <button type="submit" class="btn btn-primary btn-xs" style="width: 75px"> Cập nhật </button><br>
                                        <a href="index.php" class="btn btn-danger btn-xs" style="width: 75px"> Huỷ bỏ </a>
                                    </div>
                                    <!-- /.box-footer -->
                                </form>
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
        <script type="text/javascript">
            CKEDITOR.replace( 'my-editor', {
                height:'400px'
            });
            $(document).ready(function() {
                $('.select2').select2();
            });
        </script>
    </body>