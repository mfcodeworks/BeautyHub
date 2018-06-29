<?php

class PeepSoWidgetAjax extends PeepSoAjaxCallback
{
    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * Called from PeepSoAjaxHandler
     * Declare methods that don't need auth to run
     * @return array
     */
    public function ajax_auth_exceptions()
    {
        return array(
            'latest_members',
            'online_members',
        );
    }

    public function latest_members(PeepSoAjaxResponse $resp)
    {
        ob_start();
        $empty = TRUE;

        $limit = $this->_input->int('limit');
        $totalmember = $this->_input->int('totalmember', 0);
        $hideempty = $this->_input->int('hideempty', 0);

        $trans_latest_members = 'peepso_cache_widget_latestmembers';

        // check cache
        $list_latest_members = get_transient($trans_latest_members);
        if (false === $list_latest_members) {
            // List of links to be displayed
            $args['orderby'] = 'registered';
            $args['order'] = 'desc';
            $args['exclude'] = get_current_user_id();
            $args_pagination['offset'] = 0;
            $args_pagination['number'] = $limit;
            $args_hide['meta_query'] = array( 
                'relation' => 'OR',
                array(
                    'key' => 'peepso_is_hide_profile_from_user_listing', 
                    'value' => '1', 
                    'compare' => '!='
                    ),
                array(
                    'compare' => 'NOT EXISTS',
                    'key' => 'peepso_is_hide_profile_from_user_listing',
                )
            );

            // Merge pagination args and run the query to grab paged results
            $args = array_merge($args, $args_pagination, $args_hide);

            $list_latest_members = new PeepSoUserSearch($args, get_current_user_id(), '');
            set_transient($trans_latest_members, $list_latest_members, 1 * HOUR_IN_SECONDS);
        }

        $PeepSoMemberSearch = PeepSoMemberSearch::get_instance();

        if (count($list_latest_members->results)) {
            $empty = FALSE;
            ?>

            <div class="ps-widget__members">

                <?php foreach ($list_latest_members->results as $user) { ?>
                    <div class="ps-widget__members-item">
                        <?php $PeepSoMemberSearch->show_latest_member(PeepSoUser::get_instance($user)); ?>
                    </div>
                <?php } ?>
            </div>

            <?php if ($totalmember == 1) {
                $trans_member_count = 'peepso_cache_widget_total_member';
                $total_member_value = get_transient($trans_member_count);

                if ($total_member_value == false) {
                    $user_args = array(
                        'peepso_roles' => array('admin', 'moderator', 'member'),
                    );

                    $user_query = new WP_User_Query($user_args);
                    add_action('pre_user_query', array(PeepSo::get_instance(), 'filter_user_roles'));
                    $user_results = $user_query->get_results();
                    remove_action('pre_user_query', array(PeepSo::get_instance(), 'filter_user_roles'));

                    $total_member_value = count($user_results);
                    set_transient($trans_member_count, $total_member_value, 300);
                }

                echo sprintf("<span class=\"ps-widget--members__count\">" . __('Members count', 'peepso-core') . ": %s</span>", $total_member_value);
            }

        } else { ?>
            <span class="ps-text--muted"><?php echo __('No latest members', 'peepso-core'); ?></span>
        <?php }

        $resp->success(TRUE);
        $resp->set('empty', $empty);
        $resp->set('html', str_replace(array("  ",PHP_EOL),'',ob_get_clean()));
    }


    public function online_members(PeepSoAjaxResponse $resp) {
        ob_start();
        $empty = TRUE;

        $limit = $this->_input->int('limit');
        $totalmember = $this->_input->int('totalmember', 0);
        $hideempty = $this->_input->int('hideempty', 0);

        $trans_online_members = 'peepso_cache_widget_onlinemembers';

        // check cache
        $list_online_members = get_transient($trans_online_members);
        if(false === $list_online_members) {
            // List of links to be displayed
            $args['orderby']= 'peepso_last_activity';
            $args['order']  = 'desc';
            $args_pagination['offset'] = 0;
            $args_pagination['number'] = $limit;

            $args_hide_online = array();
            // Check config option for Allow users to hide themselves from all user listings
            if (!PeepSo::is_admin()) {
                $args_hide_online['meta_query'] = array(
                    'relation' => 'OR',
                    array(
                        'key' => 'peepso_hide_online_status',
                        'value' => '1',
                        'compare' => '!='
                    ),
                    array(
                        'compare' => 'NOT EXISTS',
                        'key' => 'peepso_hide_online_status',
                    )
                );
            }

            // Merge pagination args and run the query to grab paged results
            $args = array_merge($args, $args_pagination, $args_hide_online);

            $list_online_members = new PeepSoUserSearch($args, get_current_user_id(), '');
            set_transient( $trans_online_members, $list_online_members, 60 );
        }

        $list = array();
        foreach($list_online_members->results as $user_id)
        {
            $user = PeepSoUser::get_instance($user_id);
            if(TRUE === $user->is_online())
            {
                $list[] = $user;
            }
        }

        $PeepSoMemberSearch = PeepSoMemberSearch::get_instance();

        if (count($list)) {
            $empty = FALSE;
            ?>

            <div class="ps-widget__members">
                <?php
                foreach ($list as $user) {
                    echo '<div class="ps-widget__members-item">';
                    $PeepSoMemberSearch->show_online_member($user);
                    echo '</div>';
                }
                ?>
            </div>

            <?php if ($totalmember == 1) {
                $trans_member_count = 'peepso_cache_widget_total_member';
                $total_member_value = get_transient($trans_member_count);

                if ($total_member_value == false) {
                    $user_args = array(
                        'peepso_roles' => array('admin', 'moderator', 'member'),
                    );

                    $user_query = new WP_User_Query($user_args);
                    add_action('pre_user_query', array(PeepSo::get_instance(), 'filter_user_roles'));
                    $user_results = $user_query->get_results();
                    remove_action('pre_user_query', array(PeepSo::get_instance(), 'filter_user_roles'));

                    $total_member_value = count($user_results);
                    set_transient($trans_member_count, $total_member_value, 300);
                }


                echo sprintf("<span class=\"ps-widget--members__count\">" . __('Members count', 'peepso-core') . ": %s</span>", $total_member_value);
            }

        } else { ?>
            <span class='ps-text--muted'><?php echo __('No online members', 'peepso-core');?></span>
        <?php }

        $resp->success(TRUE);
        $resp->set('empty', $empty);
        $resp->set('html', str_replace(array("  ",PHP_EOL),'',ob_get_clean()));
    }
}




