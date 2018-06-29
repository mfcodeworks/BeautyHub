<?php


class PeepSoWidgetUserBar extends WP_Widget
{

    /**
     * Set up the widget name etc
     */
    public function __construct($id = null, $name = null, $args= null) {
        if(!$id)    $id     = 'PeepSoWidgetUserBar';
        if(!$name)  $name   = __('PeepSo UserBar', 'peepso-core');
        if(!$args)  $args   = array( 'description' => __('PeepSo User Bar Widget', 'peepso-core'), );

        parent::__construct(
            $id, // Base ID
            $name, // Name
            $args // Args
        );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {

        $instance['user_id']    = get_current_user_id();
        $instance['user']       = PeepSoUser::get_instance($instance['user_id']);

        // Disable the widget for guests if
        if(isset($instance['guest_behavior']) && 'hide' === $instance['guest_behavior'] && !$instance['user_id'])
        {
            return FALSE;
        }


        // List of links to be displayed
        $links = apply_filters('peepso_navigation_profile', array('_user_id'=>get_current_user_id()));
        $instance['links'] = $links;


        if(!array_key_exists('template', $instance) || !strlen($instance['template']))
        {
            $instance['template'] = 'userbar.tpl';
        }

        $instance['toolbar'] = '';
        if(isset($instance['show_notifications']) && 1 === intval($instance['show_notifications'])) {
                $instance['toolbar'] = $this->toolbar();
        }


        PeepSoTemplate::exec_template( 'widgets', $instance['template'], array( 'args'=>$args, 'instance' => $instance ) );

        // Included in peepso bundle.
        wp_enqueue_script('peepso-widget-userbar', FALSE, array('peepso-bundle', 'peepso-notification'),
            PeepSo::PLUGIN_VERSION, TRUE);
    }

    // Displays the frontend navbar
    public function toolbar()
    {
        $note = PeepSoNotifications::get_instance();
        $unread_notes = $note->get_unread_count_for_user();

        $toolbar = array(
            'notifications' => array(
                'href' => PeepSo::get_page('notifications'),
                'icon' => 'globe',
                'class' => 'dropdown-notification ps-js-notifications',
                'title' => __('Pending Notifications', 'peepso-core'),
                'count' => $unread_notes,
                'order' => 100
            ),
        );

        $toolbar = PeepSoGeneral::get_instance()->get_navigation('notifications');

        ob_start();
        ?>

        <div class="ps-widget--userbar__notifications">

        <?php foreach ($toolbar as $item => $data) { ?>
            <span class="<?php echo $data['class'];?>">
              <a href="<?php echo $data['href'];?>" title="<?php echo esc_attr($data['label']);?>">
                <div class="ps-bubble__wrapper">
                    <i class="<?php echo $data['icon'];?>"></i>
                        <span class="js-counter ps-bubble ps-bubble--widget ps-js-counter" <?php echo ($data['count'] > 0) ? '' : ' style="display:none"';?>>
                            <?php echo ($data['count'] > 0) ? $data['count'] : '';?>
                        </span>
                </div>
              </a>
            </span>
        <?php } ?>

        </div>

        <?php
        $html = str_replace(PHP_EOL,'',ob_get_clean());

        return $html;
    }

    /**
     * Outputs the admin options form
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {

        $instance['fields'] = array(
            // general
            'section_general' => FALSE,
            'limit'     => FALSE,
            'title'     => FALSE,

            // peepso
            'integrated'   => FALSE,
            'position'  => FALSE,
            'ordering'  => FALSE,
            'hideempty' => FALSE,

        );

        ob_start();

        $settings = apply_filters('peepso_widget_form', array('html'=>'', 'that'=>$this,'instance'=>$instance));

        $guest_behavior         = !empty($instance['guest_behavior']) ? $instance['guest_behavior'] : 'hide';

        $content_position       = !empty($instance['content_position']) ? $instance['content_position'] : 'left';

        $show_notifications     = isset($instance['show_notifications']) ? $instance['show_notifications'] : 1;

        $show_usermenu          = isset($instance['show_usermenu']) ? $instance['show_usermenu'] : 1;

        $show_avatar            = isset($instance['show_avatar']) ? $instance['show_avatar'] : 1;

        $show_name              = isset($instance['show_name']) ? $instance['show_name'] : 1;

        $show_logout            = isset($instance['show_logout']) ? $instance['show_logout'] : 1;

        $show_vip               = isset($instance['show_vip']) ? $instance['show_vip'] : 0;

        $show_badges            = isset($instance['show_badges']) ? $instance['show_badges'] : 0;

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('content_position'); ?>">
                <?php _e('Content Position', 'peepso-core'); ?>
                <select class="widefat" id="<?php echo $this->get_field_id('content_position'); ?>"
                        name="<?php echo $this->get_field_name('content_position'); ?>">
                    <option value="left" <?php if('left' === $content_position) echo ' selected="selected" ';?>><?php _e('Left', 'peepso-core'); ?></option>
                    <option value="right" <?php if('right' === $content_position) echo ' selected="selected" ';?>><?php _e('Right', 'peepso-core'); ?></option>
                    <option value="center" <?php if('center' === $content_position) echo ' selected="selected" ';?>><?php _e('Center', 'peepso-core'); ?></option>
                    <option value="space" <?php if('space' === $content_position) echo ' selected="selected" ';?>><?php _e('Space Between', 'peepso-core'); ?></option>
                </select>
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('guest_behavior'); ?>">
                <?php _e('Guest view', 'peepso-core'); ?>
                <select class="widefat" id="<?php echo $this->get_field_id('guest_behavior'); ?>"
                        name="<?php echo $this->get_field_name('guest_behavior'); ?>">
                    <option value="login"><?php _e('Log-in link', 'peepso-core'); ?></option>
                    <option value="hide" <?php if('hide' === $guest_behavior) echo ' selected="selected" ';?>><?php _e('Hide', 'peepso-core'); ?></option>
                </select>
            </label>
        </p>
        <p>
            <input name="<?php echo $this->get_field_name('show_avatar'); ?>" class="ace ace-switch ace-switch-2"
                   id="<?php echo $this->get_field_id('show_avatar'); ?>" type="checkbox" value="1"
                <?php if(1 === $show_avatar) echo ' checked="" ';?>>
            <label class="lbl" for="<?php echo $this->get_field_id('show_avatar'); ?>">
                <?php _e('Show avatar', 'peepso-core'); ?>
            </label>
        </p>
        <p>
            <input name="<?php echo $this->get_field_name('show_name'); ?>" class="ace ace-switch ace-switch-2"
                   id="<?php echo $this->get_field_id('show_name'); ?>" type="checkbox" value="1"
                <?php if(1 === $show_name) echo ' checked="" ';?>>
            <label class="lbl" for="<?php echo $this->get_field_id('show_name'); ?>">
                <?php _e('Show name', 'peepso-core'); ?>
            </label>
        </p>
        <p>
            <input name="<?php echo $this->get_field_name('show_notifications'); ?>" class="ace ace-switch ace-switch-2"
                   id="<?php echo $this->get_field_id('show_notifications'); ?>" type="checkbox" value="1"
                <?php if(1 === $show_notifications) echo ' checked="" ';?>>
            <label class="lbl" for="<?php echo $this->get_field_id('show_notifications'); ?>">
                <?php _e('Show notifications', 'peepso-core'); ?>
            </label>
        </p>
        <p>
            <input name="<?php echo $this->get_field_name('show_usermenu'); ?>" class="ace ace-switch ace-switch-2"
                   id="<?php echo $this->get_field_id('show_usermenu'); ?>" type="checkbox" value="1"
                <?php if(1 === $show_usermenu) echo ' checked="" ';?>>
            <label class="lbl" for="<?php echo $this->get_field_id('show_usermenu'); ?>">
                <?php _e('Show User dropdown menu', 'peepso-core'); ?>
            </label>
        </p>
        <p>
            <input name="<?php echo $this->get_field_name('show_logout'); ?>" class="ace ace-switch ace-switch-2"
                   id="<?php echo $this->get_field_id('show_logout'); ?>" type="checkbox" value="1"
                <?php if(1 === $show_logout) echo ' checked="" ';?>>
            <label class="lbl" for="<?php echo $this->get_field_id('show_logout'); ?>">
                <?php _e('Show logout icon', 'peepso-core'); ?>
            </label>
        </p>
        <p>
            <input name="<?php echo $this->get_field_name('show_vip'); ?>" class="ace ace-switch ace-switch-2"
                   id="<?php echo $this->get_field_id('show_vip'); ?>" type="checkbox" value="1"
                <?php if(1 === $show_vip) echo ' checked="" ';?>>
            <label class="lbl" for="<?php echo $this->get_field_id('show_vip'); ?>">
                <?php _e('Show VIP icons', 'peepso-core'); ?>
            </label>
        </p>
        <p>
            <input name="<?php echo $this->get_field_name('show_badges'); ?>" class="ace ace-switch ace-switch-2"
                   id="<?php echo $this->get_field_id('show_badges'); ?>" type="checkbox" value="1"
                <?php if(1 === $show_badges) echo ' checked="" ';?>>
            <label class="lbl" for="<?php echo $this->get_field_id('show_badges'); ?>">
                <?php _e('Show Badges', 'peepso-core'); ?>
            </label>
        </p>
        <?php
        $settings['html']  .= ob_get_clean();

        echo $settings['html'];
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['guest_behavior']         = $new_instance['guest_behavior'];
        $instance['content_position']       = $new_instance['content_position'];
        $instance['show_avatar']            = (int) $new_instance['show_avatar'];
        $instance['show_name']              = (int) $new_instance['show_name'];
        $instance['show_notifications']     = (int) $new_instance['show_notifications'];
        $instance['show_usermenu']          = (int) $new_instance['show_usermenu'];
        $instance['show_logout']            = (int) $new_instance['show_logout'];
        $instance['show_vip']               = (int) $new_instance['show_vip'];
        $instance['show_badges']            = (int) $new_instance['show_badges'];

        return $instance;
    }
}

// EOF
