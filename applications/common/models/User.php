<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends DataMapper 
{
    public $table    = 'user';
    public $has_many = array(
        'sessions'   => 'user_session',
        'roles' => array(
            'class'          => 'role',
            'other_field'    => 'users',
            'join_self_as'   => 'user',
            'join_other_as'  => 'role',
        ),
    );

    public $validation = array(
        'name' => array(
            'type'  => 'text',
            'rules' => array('required'),
        ),
        'email' => array(
            'type'  => 'email',
            'rules' => array('required','trim','valid_email','unique','spaces'),
        ),
        'password' => array(
            'type'  => 'password',
            'rules' => array('required','encrypt','spaces','trim'),
        ),
        'confirm_password' => array(
            'type'  => 'password',
            'rules' => array('encrypt','required','matches' => 'password','spaces','trim')
        ),
        'active_flag' => array(
            'type'   => 'radiogroup',
            'rules'  => array('required'),
            'value'  => '1',
        ),
        'avatar' => array(
            'type' => 'file',
        ),
        'roles' => array(
            'type'  => 'multiselect',
        ),
    );

    /**
     * save: Save user and insert relations with roles.s
     *
     * @access public
     * @param  mixed  $object,  [Optional] object to save or array of objects to save.
     * @param  string $relation,[Optional] string to save the object as a specific relationship.
     * @return bool Success or Failure of the validation and save.
     **/
    public function save( $object = '', $relation = '' ) 
    {
        // Start Transaction
        $this->trans_begin();

        if( is_array($object) && !empty($object) ) 
        {
            // If exists Roles Delete All.
            if ( $this->roles->count() > 0 ) 
            {
                $objects = $this->roles->get();
                $this->delete( $objects->all, 'roles' );
            }

            // Associate Roles to User.
            if ( $ids = $object['roles'] ) 
            {
                if ( !empty( $ids ) ) 
                {
                    $roles = array();
                    foreach ( $ids as $id ) 
                    {
                        $role = new Role();
                        $role->get_by_id( $id );

                        if ( !$role->exists() )
                            continue;

                        array_push( $roles, $role );
                    }

                    $object['roles'] = $roles;
                }
            }
        }

        parent::save( $object, $relation );

        // Check status of transaction.
        if ( $this->trans_status() === FALSE ) 
        {
            // Transaction failed, rollback.
            $this->trans_rollback();

            return FALSE;
        }
        else 
        {
            // Transaction successful, commit.
            $this->trans_commit();

            return TRUE;
        }
    }

    /**
     * login: Check if User have a valid login.
     *
     * @access public
     * @return boolean
    **/
    public function login() 
    {
        // Validate and get this user by their property values and active status.
        $this->validate()->where('active_flag', 1)->get();

        // Login Success.
        return ( $this->exists() ) ? TRUE : FALSE;
    }

    /**
     * encrypt: Encryption of a value in the SHA1 algorithm.
     *
     * @access public
     * @param  string $field, [Required] $field to encrypt.
     * @return void.
    **/
    public function _encrypt( $field ) 
    {
        // Don't encrypt an empty value.
        if ( !empty( $this->{ $field } ) )
            $this->{ $field } = sha1( $this->{ $field } );
    }

    /**
     * can: Check if user can access to path.
     *
     * @access public
     * @param  $path strint, Path to check access.
     * @return boolean
     */
    public function can( $uripath ) 
    {
        foreach( $this->roles->get() as $role ) 
        {
            if ( preg_match( "/$role->key_match/", $uripath ) == 1 )
                return TRUE;
        }

        return FALSE;
    }

}