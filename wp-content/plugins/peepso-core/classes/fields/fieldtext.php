<?php

class PeepSoFieldText extends PeepSoField {

	public static $admin_label='Text';

	public function __construct($post, $user_id)
	{
		parent::__construct($post, $user_id);

		$render_form_methods = array(
			'_render_form_input' => __('input (single line)', 'peepso-core'),
			'_render_form_textarea' => __('textarea (multiple lines)', 'peepso-core'),
		);

		$this->render_form_methods = array_merge( $this->render_form_methods, $render_form_methods );

		$validation_methods = array(
			'lengthmin',
			'lengthmax',
		);

		$this->validation_methods = array_merge( $this->validation_methods, $validation_methods);

		$this->default_desc = __('Tell us about it.', 'peepso-core');
	}

	protected function _render()
    {
        if(empty($this->value) || ($this->is_registration_page)) {
            return $this->_render_empty_fallback();
        }

        return (strstr($this->value, PHP_EOL)) ? wpautop($this->value) : $this->value;
    }

	protected function _render_form_textarea()
	{
		$ret  = '<div style="position:relative">';
		$ret .= '<textarea'.$this->_render_input_args().'>' . $this->value . '</textarea>';
		$ret .= '<div class="ps-charcount ps-charcount--input ps-js-counter" style="display:none"></div>';
		$ret .= '</div>';

		return $ret;
	}

	protected function _render_form_textarea_register()
	{
		$this->el_class = 'ps-textarea';

		$ret  = '<div style="position:relative">';
		$ret .= '<textarea'.$this->_render_input_register_args().'>' . $this->value . '</textarea>';
		$ret .= '<div class="ps-charcount ps-charcount--input ps-js-counter" style="display:none"></div>';
		$ret .= '</div>';

		return $ret;
	}
}
