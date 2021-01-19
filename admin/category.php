<?php
    // $user have all the properties and method of the current user

    require_once '../includes/admin/page_head2.php';
    if(!$user->hasPermission('category')){
        // redirect to denied page
        Redirect::to(1);
    }

    $category = new Category();
    $categories = $category->get_active('categories',array('company_id' ,'=',$user->data()->company_id));


?>
    <link rel="stylesheet" href="../css/jquery.treegrid.css">


    <!-- Page content -->
    <div id="page-content-wrapper">




    <!-- Keep all page content within the page-content inset div! -->
    <div class="page-content inset">
        <div class="content-header">
            <h1>
                <span id="menu-toggle" class='glyphicon glyphicon-list'></span>
                Manage Categories
            </h1>

        </div>
        <?php
            // get flash message if add or edited successfully
            if(Session::exists('categoryflash')){
                echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('categoryflash')."</div>";
            }
        ?>

        <div class="row">
            <div class="col-md-12">
                <?php
                    if($user->hasPermission('category_m')) {
                        ?>

                        <div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
                            <a href='addcategory.php' class='btn btn-default' title='Add Category'>
                                <span class='glyphicon glyphicon-plus'></span>
                                <span class='hidden-xs'>Add Category</span>
                            </a></div>
                    <?php } ?>

                <?php
                    if ($categories){
                ?>
                <div class="panel panel-primary">
                    <!-- Default panel contents -->
                    <div class="panel-heading">Categories</div>
                    <div class="panel-body">


                        <table class="table tree">
                            <?php
                                function array_sort($array, $on, $order=SORT_ASC){

                                    $new_array = array();
                                    $sortable_array = array();

                                    if (count($array) > 0) {
                                        foreach ($array as $k => $v) {
                                            if (is_array($v)) {
                                                foreach ($v as $k2 => $v2) {
                                                    if ($k2 == $on) {
                                                        $sortable_array[$k] = $v2;
                                                    }
                                                }
                                            } else {
                                                $sortable_array[$k] = $v;
                                            }
                                        }

                                        switch ($order) {
                                            case SORT_ASC:
                                                asort($sortable_array);
                                                break;
                                            case SORT_DESC:
                                                arsort($sortable_array);
                                                break;
                                        }

                                        foreach ($sortable_array as $k => $v) {
                                            $new_array[$k] = $array[$k];
                                        }
                                    }

                                    return $new_array;
                                }
                            function objectToArray($object)
                            {
                                if (!is_object($object) && !is_array($object))
                                    return $object;
                                return array_map('objectToArray', (array)$object);
                            }

                                $ccc = new Category();
                                 $cc = objectToArray($ccc->getCategory($user->data()->company_id));
                                $cc = array_sort($cc, 'name', SORT_ASC);

                            $array = array();

                            function get_nested($array)
                            {
                                $user = new User();
                                $hasActions = $user->hasPermission('category_m');
                                $str = '';

                                if (count($array)) {
                                    foreach ($array as $item) {
                                        $str .= recurseTree($item, $hasActions);
                                    }
                                }
                                return $str;
                            }

                            function recurseTree($var, $hasActions = FALSE){
                                ($hasActions) ? $action = "<a class='actions btn btn-primary' href='addcategory.php?edit=" . Encryption::encrypt_decrypt('encrypt', $var['id']) . "' title='Edit Category'><span class='glyphicon glyphicon-pencil'></span></a>
                                                <a href='#' class='actions btn btn-primary deleteCategory' id='" . Encryption::encrypt_decrypt('encrypt', $var['id']) . "' title='Delete Category'><span class='glyphicon glyphicon-remove'></span></a>" : $action = '';
                                $out = "<tr class='treegrid-$var[id] ";
                                if($var[parent]>0) $out .= "child treegrid-parent-$var[parent]";
                                $out .= "'><td>$var[name]</td><td>$action</td></tr>";

                                foreach($var[children] as $child){
                                    if(is_array($child)){
                                        $out .= recurseTree($child, $hasActions);
                                    }
                                }
                                return $out;
                            }
                                function makeRecursive($d, $r = 0, $pk = 'parent', $k = 'id', $c = 'children') {
                                    $m = array();
                                    foreach ($d as $e) {
                                        isset($m[$e[$pk]]) ?: $m[$e[$pk]] = array();
                                        isset($m[$e[$k]]) ?: $m[$e[$k]] = array();
                                        $m[$e[$pk]][] = array_merge($e, array($c => &$m[$e[$k]]));
                                    }

                                    return $m[$r]; // remove [0] if there could be more than one root nodes
                                }
                            ?>
                            <?php echo get_nested(makeRecursive($cc)); ?>
                        </table>
                    </div>
                    <?php
                        } else {
                        ?>
                        <div class='alert alert-info'>There is no current item at the moment.</div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div> <!-- end page content wrapper-->
    <script type="text/javascript" src="../js/jquery.treegrid.js"></script>
    <script type="text/javascript" src="../js/jquery.treegrid.bootstrap3.js"></script>
    <script>


        $(document).ready(function () {
            $('.tree').treegrid({
                'initialState': 'collapsed',
                'saveState': true
            });
            $(".deleteCategory").click(function () {
                if (confirm("Are you sure you want to delete this record?")) {
                    id = $(this).prop('id');
                    $.post('../ajax/ajax_delete.php',{id:id,table:'categories'},function(data){
                        if(data == "true"){
                            location.reload();
                        }
                    });
                }
            });

        });


    </script>
<?php require_once '../includes/admin/page_tail2.php'; ?>