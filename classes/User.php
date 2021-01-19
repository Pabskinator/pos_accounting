<?php
	class User extends Crud implements PagingInterface{
		protected
			$_sessionName,
			$_cookieName,
			$_isLoggedIn,
			$_table='users';
		// extend the crud , set the table name
		public function __construct($user=null){
			// invoke the parent construct
			parent::__construct($user);
			// get the session of user
			$this->_sessionName = Config::get('session/session_name');
			// if user is not passed, id or username
			if(!$user){
				// check if there is session
				if(Session::exists($this->_sessionName)){
					// store the session of user
					$user = Session::get($this->_sessionName);
					// find the user
					if($this->find($user)){
						// if find, set logged in to true
						$this->_isLoggedIn = true;
					} else {
						// process log out here
						Redirect::to('logout.php');
					}
				}

				// to nothing if it was just instantiate by an object

			} else {
				// if user variable is passed, find it
				$this->find($user);
			}

		}


		public function find($user=null){
			// receive id or username
			if($user){
				// if number , set field as id, else username.
				$field = (is_numeric($user)) ? 'id' : 'username';
				// get the items in database
				$data = $this->_db->get('users',array($field ,'=' , $user),0);
				if($data->count()){
					// set the data if it returns any row
					$this->_data= $data->first();
					return true;
				}
			}
			return false;
		}
		// get all users base on company, pass optional userid to get specific user
		public function getAllUsers($company_id = 0, $userid=0){
			$parameters = array();
			if($company_id){
				// set the company
				$parameters[] = $company_id;
 				$q= 'Select b.name as branch_name, u.id,u.lastname,u.middlename,u.firstname, u.username,u.created, p.position,p.id as position_id, p.permisions,u.branch_id,u.department_id , u.wallet, m.role_id as accounting_role_id from users u left join acc_model_has_roles m on m.model_id = u.id left join positions p on p.id = u.position_id left join branches b on b.id=u.branch_id where u.company_id=? and u.is_active=1 ';
				// if company , set method as results cause it has many rows
				$method = "results";
				// if user id is set, get specific user only
				if($userid){
					$parameters[] = $userid;
					$q.= ' and u.id=?';
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
		public function getUsers($cid=0,$uid = 0){

			$where ='';
			$parameters = array();
			$method='results';
			if($cid){
				$where = ' and u.company_id=? ';
				$parameters[] = $cid;
			}
			if($uid){
				$whereuid = ' and u.id=? ';
				$parameters[] = $uid;
				$method = 'first';
			}
			$q= 'Select u.id,u.lastname,u.middlename,u.firstname, u.username,u.password,u.company_id,u.created, p.position,p.id as position_id, p.permisions, co.name as company_name, u.branch_id from users u left join positions p on p.id = u.position_id left join companies co on co.id=u.company_id where u.is_active=1 ' . $where . ' ' . $whereuid;
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->$method();
			}
		}
		public function userJSON($cid = 0 , $search = ''){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$whereSearch = '';
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$whereSearch = " and (lastname like ? or firstname like ? or middlename like ? )";
				}

				$q = "Select * from users where is_active = 1 and company_id = ? $whereSearch";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getCompany($cid){

			$parameters = array();
			if($cid){
				$parameters[] = $cid;
			}

			$q= 'Select c.*, IFNULL(p.amount,0) as amount,IFNULL(p.points,0)as points from companies c left join points p on p.company_id=c.id where c.id=?';
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
		}
		public function logout(){
			// delete the session name
			Session::delete($this->_sessionName);
			// set logged in to false
			$this->_isLoggedIn  = false;

		}
		public function checkCredential($username=null,$password=null){
			$parameters = array();
			if($username && $password){
				$parameters[] = $username;
				$parameters[] = md5($password);
				$q= "Select * from users where username = ? and password = ? limit 1";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
				return false;
			}
			return false;
		}
		public function checkCredentialByUsername($username = null){
			$parameters = array();
			if($username ){
				$parameters[] = $username;

				$q= "Select * from users where username = ? limit 1";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
				return false;
			}
			return false;
		}
		public function checkMemberPassword($member_id,$password=null){
			$parameters = array();
			if($member_id && $password){
				$parameters[] = $member_id;
				$parameters[] = md5($password);
				$q= "Select * from users where member_id = ? and password = ? limit 1";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
				return false;
			}
			return false;
		}
		public function getUserIdOfMember($member_id=0){
			$parameters = array();
			if($member_id){
				$parameters[] = $member_id;

				$q= "Select * from users where member_id = ? limit 1";
				$data = $this->_db->query($q,$parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
				return false;
			}
			return false;
		}


		public function login($username=null,$password=null,$remember=false){

			if(!$username && !$password && $this->exists()) {
				Session::put($this->_sessionName,$this->data()->id);

			} else {
				$user = $this->find($username);

				if($user){
					if($this->data()->is_active == 1){
					if(trim($this->data()->password) == trim(Hash::make($password))){
						if($remember){
							$hash = Hash::unique();
							$hashCheck = $this->_db->get('users_session',array('user_id','=',$this->data()->id),$this->data()->company_id);
							if(!$hashCheck->count()){
								$this->_db->insert('users_session',array(
									'user_id' => $this->data()->id,
									'hash' => $hash
								));
							}
							else {
								$hash = $hashCheck->first()->hash;
							}
							Cookie::put($this->_cookieName,$hash,Config::get('remember/cookie_expiry'));
						}

						Session::put($this->_sessionName,$this->data()->id);
						return true;
					}
				}
			}
			}
			return false;
		}
		public function data(){
			return $this->_data;
		}
		public function exists(){
			return (!empty($this->_data)) ? true: false;
		}
		public function isLoggedIn(){
			return $this->_isLoggedIn;
		}
		public function update($fields=array(),$id=null){
			if(!$id && $this->isLoggedIn()){
				$id = $this->data()->id;
			}
			if(!$this->_db->update('users',$id,$fields)){
				throw new Exception("There's a problem in updating your account");
			}
		}
		public function hasPermission($key){
			$group = $this->_db->get('positions',array('id','=',$this->data()->position_id),0);

			if($group->count()){

				$permissions = json_decode($group->first()->permisions,true);

				if(isset($permissions[$key])){
					return true;
				}
			}
			return false;
		}

		public function countRecord($cid,$like=''){
			$parameters = array();
			$parameters[] = $cid;
			if($like){
				$parameters[] = "%$like%";
				$parameters[] = "%$like%";
				$likewhere = " and (u.lastname like ? or u.firstname like ? )";
			} else {
				$likewhere='';
			}


			$q= "Select count(u.id) as cnt from users u where u.company_id = ?  $likewhere ";
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
				$parameters[] = "%$like%";
				$likewhere = " and (u.lastname like ? or u.firstname like ? )";
			} else {
				$likewhere='';
			}


			// prepare the query
			$q= "Select u.* , b.name as branch_name ,p.position from users u left join branches b on b.id = u.branch_id left join positions p on p.id = u.position_id where u.is_active = 1 and u.company_id = ?  $likewhere $l  ";
			//submit the query
			$data = $this->_db->query($q, $parameters);
			// return results if there is any
			if($data->count()){
				return $data->results();
			}
		}

		public function insertLoginAttempts($ip,$is_okay,$remarks='',$user_agent='',$user_os='',$user_browser=''){
			$now = time();
			$parameters = array();
			$parameters[] = $ip;
			$parameters[] = $now;
			$parameters[] = $is_okay;
			$parameters[] = $remarks;
			$parameters[] = $user_agent;
			$parameters[] = $user_os;
			$parameters[] = $user_browser;
			$q= "INSERT INTO `login_attempts`(`ip_addr`, `created_at`, `is_okay`,`remarks`,`user_agent`,`os`,`browser`) VALUES (?,?,?,?,?,?,?);";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return true;
			}
		}

		public function tooManyLoginAttempts($ip){
			$now = time() - 120;
			$parameters = array();
			$parameters[] = $ip;
			$parameters[] = $now;

			$q= "Select count(*) as failed_attempts from login_attempts where ip_addr = ? and created_at >= ?";
			$data = $this->_db->query($q,$parameters);
			if($data->count()){
				// return the data if exists
				return $data->first();
			}
			return false;
		}

		public function getPageNavigation($page, $total_pages, $limit, $stages) {
			getpagenavigation($page, $total_pages, $limit, $stages);
		}

		public function paginate($cid, $args) {

			$user = new User();
			$search = Input::get('search');


			?>
			<div id="no-more-tables">
				<div class="table-responsive">

					<table class='table' id='tblSales'>
						<thead>
						<tr>
							<TH>Name</TH>
							<TH>Branch</TH>
							<TH>Username</TH>
							<TH>Data Created</TH>
							<TH>Position</TH>

								<TH>
									<?php 	if($user->hasPermission('user_m')){ ?>
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
									$fullname = ucwords($userdata->lastname . ", " .$userdata->firstname . " $userdata->middlename");
									?>
									<tr>
										<td data-title="Name" style='border-top: 1px solid #ccc;'>
											<?php echo escape($fullname); ?><br><?php echo escape($userdata->id); ?>
											<?php
												if($userdata->is_verified){
													echo "<br><span class='label label-primary'>Verified</span>";
												}
											?>
										</td>
										<td data-title="Branch" style='border-top: 1px solid #ccc;'><?php echo capitalize($userdata->branch_name); ?></td>
										<td data-title="Username" style='border-top: 1px solid #ccc;'><?php echo escape($userdata->username); ?></td>
										<td data-title="Created" style='border-top: 1px solid #ccc;'><?php echo escape(date('m/d/Y H:i:s A',$userdata->created)); ?></td>
										<td data-title="Position" style='border-top: 1px solid #ccc;'><?php echo escape($userdata->position); ?></td>

											<td data-title="Action" style='border-top: 1px solid #ccc;'>
												<?php 	if($user->hasPermission('user_m')){ ?>
												<a class='btn btn-primary' href='adduser.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$userdata->id);?>' title='Edit User'><span class='glyphicon glyphicon-pencil'></span></a>
												<a href='#' class='btn btn-primary deleteUser' id="<?php echo Encryption::encrypt_decrypt('encrypt',$userdata->id);?>" title='Delete User'><span class='glyphicon glyphicon-remove'></span></a>
												<?php } ?>
										<?php 	if($user->hasPermission('pw_reset')){ ?>
											<a class='btn btn-primary btnReset' href='#' data-id='<?php echo $userdata->id; ?>' >
												<span class='glyphicon glyphicon-refresh'></span>
											</a>

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
?>