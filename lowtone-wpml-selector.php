<?php
/*
 * Plugin Name: Language Selector
 * Plugin URI: http://wordpress.lowtone.nl/wpml-selector
 * Description: Language selector widget and shortcode for WPML.
 * Version: 1.0
 * Author: Lowtone <info@lowtone.nl>
 * Author URI: http://lowtone.nl
 * License: http://wordpress.lowtone.nl/license
 */
/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\plugins\lowtone\wpml\selector
 */

namespace lowtone\wpml\selector {

	use lowtone\content\packages\Package,
		lowtone\dom\Document,
		lowtone\wp\widgets\simple\Widget,
		lowtone\wp\sidebars\Sidebar,
		lowtone\ui\forms\Form,
		lowtone\ui\forms\Input,
		lowtone\ui\forms\FieldSet;

	// Includes
	
	if (!include_once WP_PLUGIN_DIR . "/lowtone-content/lowtone-content.php") 
		return trigger_error("Lowtone Content plugin is required", E_USER_ERROR) && false;

	$__i = Package::init(array(
			Package::INIT_PACKAGES => array("lowtone", "lowtone\\wp"),
			Package::INIT_MERGED_PATH => __NAMESPACE__,
			Package::INIT_SUCCESS => function() {
				
				add_action("widgets_init", function() {
					wp_unregister_sidebar_widget("icl_lang_sel_widget");

					Widget::register(array(
							Widget::PROPERTY_ID => "lowtone_wpml_selector",
							Widget::PROPERTY_NAME => __("Language Selector", "lowtone_wpml_selector"),
							Widget::PROPERTY_DESCRIPTION => __("Language selection menu for WPML.", "lowtone_wpml_selector"),
							Widget::PROPERTY_FORM => function($instance) {
								$form = new Form();

								$form
									->appendChild(
										$form
											->createInput(Input::TYPE_TEXT, array(
												Input::PROPERTY_NAME => "title",
												Input::PROPERTY_LABEL => __("Title", "lowtone_wpml_selector")
											))
									)
									->appendChild(
										$languagesFieldSet = $form
											->createFieldSet(array(
												FieldSet::PROPERTY_LEGEND => __("Languages", "lowtone_wpml_selector")
											))
									);

								;

								foreach (icl_get_languages('skip_missing=0&orderby=code') as $code => $language) {

									$languagesFieldSet
										->appendChild(
											$form
												->createInput(Input::TYPE_CHECKBOX, array(
													Input::PROPERTY_NAME => array("languages", $code),
													Input::PROPERTY_LABEL => $language["translated_name"],
													Input::PROPERTY_VALUE => 1,
												))
										);

								}

								return $form;
							},
							Widget::PROPERTY_WIDGET => function($args, $instance) {
								echo $args[Sidebar::PROPERTY_BEFORE_WIDGET];

								if (isset($instance["title"]) && ($title = trim($instance["title"])))
									echo $args[Sidebar::PROPERTY_BEFORE_TITLE] . $title . $args[Sidebar::PROPERTY_AFTER_TITLE];

								echo selector($instance);

								echo $args[Sidebar::PROPERTY_AFTER_WIDGET];
							}
						));
				});

				add_shortcode("language_selector", "lowtone\\wpml\\selector\\selector");

			}
		));

	function selector($options) {
		$document = new Document();

		$languagesElement = $document->createAppendElement("languages");

		foreach (icl_get_languages('skip_missing=0&orderby=code') as $code => $language) {
			if (!(isset($options["languages"][$code]) && $options["languages"][$code]))
				continue;

			$languagesElement->createAppendElement("language", $language)->setAttribute("lang", $code);
		}

		$template = apply_filters("lowtone_wpml_selector_template", realpath(__DIR__ . "/assets/templates/selector.xsl"));

		if (isset($options["selector_id"]))
			$template = apply_filters("lowtone_wpml_selector_{$options['selector_id']}_template", $template);

		return $document
			->setTemplate($template)
			->transform()
			->saveHtml();
	}

}