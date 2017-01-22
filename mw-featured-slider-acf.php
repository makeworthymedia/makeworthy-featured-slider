<?php

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_slider-link',
		'title' => 'Slide Properties',
		'fields' => array (
			array (
				'key' => 'field_579fcb68da20c',
				'label' => 'Slide Link',
				'name' => 'slide_link',
				'type' => 'text',
				'instructions' => 'URL slide links to. Must include http://',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_579fcb68da20d',
				'label' => 'CSS Class',
				'name' => 'slide_class',
				'type' => 'text',
				'instructions' => 'Additional classes for the mw-featured-content div',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'slide',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}
