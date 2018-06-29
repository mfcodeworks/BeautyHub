<?php

class PeepSoConfigSectionLocation extends PeepSoConfigSectionAbstract
{
	// Builds the groups array
	public function register_config_groups()
	{
		$this->context='left';
        $this->group_general();
	}

    function group_general()
    {

        ob_start();
        ?>

        <?php echo __('A Google maps API key is required for the Location suggestions to work properly','peepso-core');?>

        <br/>

        <?php echo __('You can get the API key', 'peepso-core'); ?>
        <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">
            <?php _e('here', 'peepso-core');?>
        </a>.

        <?php
        $this->args('descript', ob_get_clean());
        $this->set_field(
            'location_gmap_api_key',
            __('Google Maps API Key (v3)', 'peepso-core'),
            'text'
        );

        $this->set_group(
            'location',
            __('General', 'peepso-core')
        );
    }

}
?>
