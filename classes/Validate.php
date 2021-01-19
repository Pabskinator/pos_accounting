<?php

	class Validate{
		private
			$_errors = array(),
			$_db = null;
		public $_passed= false;
		public function __construct(){
			$this->_db = DB::getInstance();
		}
		public function check ($source, $items = array()){

			foreach($items as $item => $rules){

				foreach ($rules as $rule => $rule_value){
						$value = escape(trim($source[$item]));
						$item = escape($item);
					if ($rule === 'required' && empty($value) ){
						$this->addError(str_replace("_"," ",strtoupper($item)) . " is required");
					}  else if (!empty($value)){
						switch($rule){
							case 'min':
								if (strlen($value) < $rule_value){
									$this->addError(str_replace("_"," ",strtoupper($item )) ." must be a minimun of {$rule_value} characters");
								}
								break;
							case 'max':
								if (strlen($value) > $rule_value){
									$this->addError(str_replace("_"," ",strtoupper($item )) ."  must be a maximun of {$rule_value} characters");
								}
								break;
							case 'isnumber':
								if (!is_numeric($value) && $rule_value==true){
									$this->addError(str_replace("_"," ",strtoupper($item )) ."  must be a number");
								}
								break;
							case 'matches':
								if ($value != $source[$rule_value]){
									$this->addError(str_replace("_"," ",strtoupper($rule_value)) ." must match " . str_replace("_"," ",strtoupper($item)));
								}
								break;
							case 'notmatches':
								if ($value == $source[$rule_value]){
									$this->addError(str_replace("_"," ",strtoupper($rule_value)) ." should not match " . strtoupper($item));
								}
								break;
							case 'unique':
								$user = new User();
								$check = $user->isExist($rule_value,$item,$value,$user->data()->company_id);
								if($check){
									$this->addError(str_replace("_"," ",strtoupper($item))." already exists");
								}
								break;
							case 'unique2':
								$user = new User();
								$check = $user->isExists($rule_value,$value,$user->data()->company_id);
								if($check){
									$this->addError(str_replace("_"," ",strtoupper($item))." already exists");
								}
								break;
							default:
								break;
						}

					}
				}

			}

			if (empty($this->_errors)){
				$this->_passed = true;
			}
		}
		public function addError($error){
			$this->_errors[] = $error;

		}
		public function errors(){
			return $this->_errors;

		}
		public function passed(){
			return $this->_passed;

		}
	}
?>