<?php
class Department extends Crud implements PagingInterface{

    protected $_table='acc_departments';

    public function __construct($department=null){
        parent::__construct($department);
    }

    // get all departments based on company, pass optional deptid to get specific department
    public function getAllDepts($company_id = 0, $deptid=0){

        $parameters = array();

        if($company_id){

            // set the company
            $parameters[] = $company_id;

            $q= "Select
										d.*,
										u.lastname,
										u.firstname,
										u.middlename
								from
										acc_departments d 
								left join
										users u
												on u.id = d.head_id
								where
										u.company_id=? 
										and d.is_active=1 ";

            // if company , set method as results cause it has many rows
            $method = "results";

            // if user id is set, get specific user only
            if($deptid){
                $parameters[] = $deptid;
                $q.= ' and d.id=?';
                // set method as first, to get the first row only
                $method = "first";
            }

            // submit the query to DB class
            $data = $this->_db->query($q, $parameters);
            if($data->count()){
                // return the data if exists
                return $data->$method();
            }

        }

    }

    public function countRecord($cid,$like=''){
        $parameters = array();
        $parameters[] = $cid;
        if($like){
            $parameters[] = "%$like%";
            $parameters[] = "%$like%";
            $likewhere = " and d.name like ? ";
        } else {
            $likewhere='';
        }


        $q= "Select count(d.id) as cnt from department d where d.company_id = ?  $likewhere ";
        $data = $this->_db->query($q,$parameters);
        if($data->count()){
            // return the data if exists
            return $data->first();
        }
    }

    public function get_active_record($cid,$start=0,$limit=0,$like=''){

        $parameters = array();

        $parameters[] = $cid;

        if($limit){
            $l = " LIMIT $start,$limit";
        } else {
            $l='';
        }

        if($like){
            $parameters[] = "%$like%";
            $likewhere = " and d.name like ?";
        } else {
            $likewhere='';
        }

        // prepare the query
        $q= "Select 
                d.* ,
                GROUP_CONCAT(CONCAT(u.firstname, ' ', u.lastname) SEPARATOR ', ') as fullname
            from 
                acc_departments d 
            left join users u on 
                FIND_IN_SET(u.id,d.head_id)
           where 
                d.is_active = 1 and d.company_id = ?
           $likewhere group by d.id $l";

        //submit the query
        $data = $this->_db->query($q, $parameters);

        // return results if there is any
        if($data->count()){
            return $data->results();
        }

    }

    public function getPageNavigation($page, $total_pages, $limit, $stages) {
        getpagenavigation($page, $total_pages, $limit, $stages);
    }

    public function paginate($cid, $args) {

        $department = new Department();
        $search = Input::get('search');
        $user = new User();

        ?>
        <div id="no-more-tables">

            <div class="table-responsive">

                <table class='table' id='tblSales'>
                    <thead>
                        <tr>
                            <TH>Name</TH>
                            <TH>Department Head</TH>
                            <TH>Data Created</TH>
                            <TH>
                                <?php 	if($user->hasPermission('department_m')){ ?>
                                    Actions
                                <?php } ?>
                            </TH>

                        </tr>
                    </thead>
                    <tbody>
                    <?php

                    //$targetpage = "paging.php";
                    $limit = 100;

                    $countRecord = $this->countRecord($cid, $search);

                    $total_pages = $countRecord->cnt;

                    $stages = 4;
                    $page = ($args);
                    $page = (int)$page;
                    if($page) {
                        $start = ($page - 1) * $limit;
                    } else {
                        $start = 0;
                    }

                    $company_items = $this->get_active_record($cid, $start, $limit, $search);

                    $this->getPageNavigation($page, $total_pages, $limit, $stages);

                    if($company_items) {
                        $verified =['','Verified'];
                        foreach($company_items as $userdata){
//                            $fullname = ucwords($userdata->lastname . ", " .$userdata->firstname . " $userdata->middlename");
                            $date_time = strtotime($userdata->created_at);
                            ?>
                            <tr>
                                <td data-title="Name" style='border-top: 1px solid #ccc;'>
                                    <?php echo capitalize($userdata->name); ?>
                                    <br><?php echo escape($userdata->id); ?>
                                </td>
                                <td data-title="Head" style='border-top: 1px solid #ccc;'>
                                    <?php echo escape($userdata->fullname); ?>
                                </td>
																<td data-title="Created" style='border-top: 1px solid #ccc;'>
																		<?php echo escape(date('m/d/Y H:i:s A',$date_time)); ?>
																</td>
                                <td data-title="Action" style='border-top: 1px solid #ccc;'>
                                    <?php 	if($user->hasPermission('department_m')){ ?>
                                        <a class='btn btn-primary' href='adddepartment.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$userdata->id);?>' title='Edit Department'><span class='glyphicon glyphicon-pencil'></span></a>
<!--                                        <a href='#' class='btn btn-primary deleteUser' id="--><?php //echo Encryption::encrypt_decrypt('encrypt',$userdata->id);?><!--" title='Delete Department'><span class='glyphicon glyphicon-remove'></span></a>-->
                                    <?php } ?>
                                </td>

                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan='8'><h3><span class='label label-info'>No Record Found...</span></h3></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

}