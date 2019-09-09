<?php 

namespace GroundhoggExtension\Steps;
use Groundhogg\Contact;
use function Groundhogg\create_contact_from_user;
use Groundhogg\HTML;
use Groundhogg\Plugin;
use Groundhogg\Step;

if ( ! defined( 'ABSPATH' ) ) exit;


class Role_Changed_Example extends Benchmark
{

   /**
     * Get the element name
     *
     * @return string
     */
    public function get_name()
    {
        return _x( 'Role Changed Example', 'step_name', 'groundhogg' );
    }

    /**
     * Get the element type
     *
     * @return string
     */
    public function get_type()
    {
        return 'role_changed_example';
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function get_description()
    {
        return _x( "Runs whenever a user's role is changed.", 'step_description', 'groundhogg' );
    }

    /**
     * Get the icon URL
     *
     * @return string
     */
    public function get_icon()
    {
        return GROUNDHOGG_EXTENSION_ASSETS_URL . '/images/role-changed.png';
    }

  
    /**
     * @param $step Step
     */
    public function settings( $step )
    {
        $this->start_controls_section();

        $this->add_control( 'role', [
            'label'         => __( 'Run when this access is given:', 'groundhogg' ),
            'type'          => HTML::SELECT2,
            'default'       => [ 'subscriber' ],
            'description'   => __( 'Users with these roles will trigger this benchmark.', 'groundhogg' ),
            'multiple' => true,
            'field'         => [
                'multiple' => true,
                'data'  => Plugin::$instance->roles->get_roles_for_select(),
            ],
        ] );

        $this->end_controls_section();
    }

    /**
     * Save the step settings
     *
     * @param $step Step
     */
    public function save( $step )
    {
        $this->save_setting( 'role', array_map( 'sanitize_text_field', $this->get_posted_data( 'role', [ 'subscriber' ] ) ) );
    }

    /**
     * get the hook for which the benchmark will run
     *
     * @return string[]
     */
    protected function get_complete_hooks()
    {
        return [ 'set_user_role' => 3, 'add_user_role' => 2 ];
    }

    /**
     * @param $userId int the ID of a user.
     * @param $cur_role string the new role of the user
     * @param $old_roles array list of previous user roles.
     */
    public function setup( $userId, $cur_role, $old_roles=array() )
    {
        $this->add_data( 'user_id', $userId );
        $this->add_data( 'role', $cur_role );
    }


    /**
     * Get the contact from the data set.
     *
     * @return Contact
     */
    protected function get_the_contact()
    {
        return create_contact_from_user( $this->get_data( 'user_id' ) );
    }

    /**
     * Based on the current step and contact,
     *
     * @return bool
     */
    protected function can_complete_step()
    {
        $role = $this->get_setting( 'role' );
        $step_roles = is_array( $role )? $role : [ $role ];
        $added_role = $this->get_data( 'role' );
        return in_array( $added_role, $step_roles );
    }

}