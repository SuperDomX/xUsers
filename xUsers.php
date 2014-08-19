<?php
/**
 *
 * @author heylisten@xtiv.net
 * @name Users
 * @desc User Management
 * @version v1.1.4
 * @icon Contacts2.png
 * @mini users
 * @link users
 * @see community
 * @todo
 * @release alpha
 * @alpha true
 */
	class xUsers extends Xengine{
		function dbSync(){
			return array(
				'Users' => array(
					'hash'	 		=> array('Type' => 'varchar(255)'),
					'username' 		=> array('Type' => 'varchar(255)'),
  					'password'		=> array('Type' => 'varchar(255)'),
  					'power_lvl'		=> array('Type' => 'varchar(255)'),
  					'email'			=> array('Type' => 'varchar(255)'),
  					'bday'			=> array('Type' => 'date'),
  					'last_login' 	=> array('Type' => 'varchar(255)'),
  					'last_active' 	=> array('Type' => 'varchar(255)'),
  					'last_location'	=> array('Type' => 'varchar(255)'),
  					'online'		=> array('Type' => 'int(11)'),
  					'newsletter'	=> array('Type' => 'int(11)'),
  					'name'			=> array('Type' => 'varchar(255)'),
  					'picture_src'	=> array('Type' => 'varchar(255)')
				),

				'Users_Addresses' => array(
					'user_id'    => array('Type' => 'int(8)'),
					'address_id' => array('Type' => 'int(8)')
				),

				'Addresses' => array(
					// Primary Street Line [VarChar]
					'primary_street_line'  => array('Type' => 'varchar(100)'),
					'second_street_line'   => array('Type' => 'varchar(100)'),
					
					// Street Number [Int]
					'street_number'        => array('Type' => 'int(8)'),
					// Street Number Suffix [VarChar] - A~Z 1/3 1/2 2/3 3/4 etc
					'street_number_suffix' => array('Type' => 'varchar(25)'),
					// Street Name [VarChar]
					'street_name'          => array('Type' => 'varchar(25)'),
					// Street Type [VarChar] - Street, Road, Place etc.
					'street_type'          => array('Type' => 'varchar(25)'),
					// Street Direction [VarChar] - N, NE, E, SE, S, SW, W, NW
					'street_direction'     => array('Type' => 'varchar(5)'),
					
					// Address Type [VarChar] - For example Apartment, Suite, Office, Floor, Building etc.
					'address_type'         => array('Type' => 'varchar(50)'),
					// Address Type Identifier [VarChar] - For instance the apartment number, suite, office or floor number or building identifier.
					'address_type_id'      => array('Type' => 'varchar(50)'),
					
					// Minor Municipality (Village/Hamlet) [VarChar]
					'municipality_minor'   => array('Type' => 'varchar(25)'),
					
					// Major Municipality (Town/City) [VarChar]
					'municipality_major'   => array('Type' => 'varchar(50)'),
					
					// Governing District (Province, State, County) [VarChar]
					'district'             => array('Type' => 'varchar(25)'),

					// Governing District (County) [VarChar]
					'district_sub'         => array('Type' => 'varchar(50)'),
					
					// Postal Area (Postal Code/Zip/Postcode)[VarChar]
					'postal'               => array('Type' => 'varchar(25)'),

					// Country [VarChar]
					'country'              => array('Type' => 'varchar(50)')
				)
			);
		}

		function autoRun($sdx){
			$q = $sdx->q();

			// Get the content for the page.
			$url   = substr($_SERVER['REQUEST_URI'],1);
			
			$parts = explode('/',$url);

			// Is this a user name?
			$user = $q->Select('*','Users', array('username'=>$parts[0]) );

			if(!empty( $user )){

				// Run User Page.
				$this->set('PAGE_CONTENT',"Welcome !");
				$this->set('PAGE_TITLE',$user[0]['username']);
				$this->set('PAGE',$content);
				$sdx->getXTends();
				// Look for User Extensions in Mods
				// user.html - contains config for users for module.
				foreach($sdx->xphp_files as $k => $v){
					$dir = substr($v['file'],1);
					$dir = lcfirst($dir);
					$dir = str_replace('.php','',$dir);
					$file = HTML_DIR.'/'.PUBLIC_LOC.'/'.$dir.'/user.html';

					if(file_exists($file) ){
						$user_x[$v['see']][$dir] = $v;
					} 
				}

				$sdx->set('user_x',$user_x);
			}


		}

		function idEmail($e){
			$e = array(
				'email' => $e
			);

			$q            = $this->q();
			$id           = $q->Select('id','Users',$e);
			$id           = ( empty($id) ) ? $q->Insert('Users', $e) : $id[0]['id']; 

			return array(
				'success' => $id,
				'error'   => $q->error
			); 
		}

		protected function idAddress($a,$uid){
			$q            = $this->q();
			$id           = $q->Select('id','Addresses',$a);
			$id           = ( empty($id[0]['id']) ) ? $q->Insert('Addresses', $a) : $id[0]['id']; 

			$w['user_id']    = $uid;
			$w['address_id'] = $id;

			// User address not yet linked to user, so link it. 
			$address = $q->Select('id','Users_Addresses', $w);
			if( empty( $address ) )
				$q->Insert('Users_Addresses', $w);

			return array(
				'success' => $id,
				'error'   => $q->error
			); 
		}

		protected function getUsersAddresses($id){
			$q        = $this->q();

			$a        = $q->Select('address_id','Users_Addresses',array(
				'user_id' => $id
			))[0];
			
			$a        = $q->Select('*','Addresses',array(
				'id' => $a['address_id']
			));

			return $a[0];
		}

		function index(){
			$this->set('WWW_PAGE','Users');
			// SQL Connection
			$q = $this->q();
			$this->set('BODY_VALIGN','top');
			// the fields that we want to see are...
			$q->setStartLimit(0,1);
			$fields = $q->Select('*','Users');
			$fields = $fields[0];
			unset($fields['hash']);

			foreach($fields as $k => $v){
				$f[] = $k;
			}
			$start = ($_POST['start'])?$_POST['start']:0;
			$limit = ($_POST['limit'])?$_POST['limit']:0;

			$q->mLimit = null;
			$all_users = $q->Select(implode(',',$f) ,'Users');

			// Lets build the columns.
			foreach($f as $k => $v){
				//
				$columns[] = array(
					'header' => ucwords( str_replace('_',' ',$v) ),
					'id'		=> $v
				);


			}

			// set the datas
			$this->set('columns',	json_encode($columns)	);
			$this->set('fields',	json_encode($f)	);
			$this->set('all_users',	json_encode($all_users)	);

			$this->set('users',	$all_users);
		}

		function avatar($username){

			$picture = "./bin/images/icons/48x48/user.png";

			$user = $this->q()->Select('picture_src','Users',array(
				'username'	=> $username
			));

			if($user[0]['picture_src'] != ''){
				$picture = $user[0]['picture_src'];
			}

			//
			echo file_get_contents($picture);
			exit;
		}

	}

?>