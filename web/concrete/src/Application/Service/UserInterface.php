<?php
namespace Concrete\Core\Application\Service;
use User as ConcreteUser;
use Loader;
use Page;
use Localization;
use Config;
use Session;
use \Concrete\Core\View\ErrorView;
use stdClass;

/**
 * @package Helpers
 * @subpackage Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Useful functions for generating elements on the Concrete interface
 * @subpackage Concrete
 * @package Helpers
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

class UserInterface {

	static $menuItems = array();

	/**
	 * Generates a submit button in the Concrete style
	 * @param string $text The text of the button
	 * @param string $formID The form this button will submit
	 * @param string $buttonAlign
	 * @param string $innerClass
	 * @param array $args Extra args passed to the link
	 * @return string
	 */
	public function submit($text, $formID = false, $buttonAlign = 'right', $innerClass = null, $args = array()) {
		if ($buttonAlign == 'right') {
			$innerClass .= ' pull-right';
		} else if ($buttonAlign == 'left') {
			$innerClass .= ' pull-left';
		}

		if (!$formID) {
			$formID = 'button';
		}
		$argsstr = '';
		foreach($args as $k => $v) {
			$argsstr .= $k . '="' . $v . '" ';
		}
		return '<input type="submit" class="btn ' . $innerClass . '" value="' . $text . '" id="ccm-submit-' . $formID . '" name="ccm-submit-' . $formID . '" ' . $align . ' ' . $argsstr . ' />';
	}

	/**
	 * Generates a simple link button in the Concrete style
	 * @param string $text The text of the button
	 * @param string $href
	 * @param string $buttonAlign
	 * @param string $innerClass
	 * @param array $args Extra args passed to the link
	 * @return string
	 */
	public function button($text, $href, $buttonAlign = 'right', $innerClass = null, $args = array()) {
		if ($buttonAlign == 'right') {
			$innerClass .= ' pull-right';
		} else if ($buttonAlign == 'left') {
			$innerClass .= ' pull-left';
		}
		$argsstr = '';
		foreach($args as $k => $v) {
			$argsstr .= $k . '="' . $v . '" ';
		}
		return '<a href="'.$href.'" class="btn btn-default '.$innerClass.'" '.$argsstr.'>'.$text.'</a>';
	}

	/**
	 * Generates a JavaScript function button in the Concrete style
	 * @param string $text The text of the button
	 * @param string $onclick
	 * @param string $buttonAlign
	 * @param string $innerClass - no longer used
	 * @param array $args Extra args passed to the link
	 * @return string
	 */
	public function button_js($text, $onclick, $buttonAlign = 'right', $innerClass = null, $args = array()) {
		if ($buttonAlign == 'right') {
			$innerClass .= ' pull-right';
		} else if ($buttonAlign == 'left') {
			$innerClass .= ' pull-left';
		}
		$argsstr = '';
		foreach($args as $k => $v) {
			$argsstr .= $k . '="' . $v . '" ';
		}
		return '<input type="button" class="btn btn-default ' . $innerClass . '" value="' . $text . '" onclick="' . $onclick . '" ' . $align . ' ' . $argsstr . ' />';
	}

	/**
	 * Outputs button text passed as arguments with a special Concrete wrapper for positioning
	 * <code>
	 *    $bh->buttons($myButton1, $myButton2, $myButton3);
	 * </code>
	 * @param string $buttonHTML
	 * @return string
	 */
	public function buttons($buttons = null) {
		if (!is_array($buttons)) {
			$buttons = func_get_args();
		}
		$html = '<div class="ccm-buttons well">';
		foreach($buttons as $_html) {
			$html .= $_html . ' ';
		}
		$html .= '</div>';
		return $html;
	}

	public function getQuickNavigationLinkHTML($c) {
		$cnt = Loader::controller($c);
		if (method_exists($cnt, 'getQuickNavigationLinkHTML')) {
			return $cnt->getQuickNavigationLinkHTML();
		} else {
			return '<a href="' . Loader::helper('navigation')->getLinkToCollection($c) . '">' . $c->getCollectionName() . '</a>';
		}
	}

	public function showWhiteLabelMessage() {
		return (Config::get('concrete.white_label.logo') || file_exists(DIR_APPLICATION . '/' . DIRNAME_IMAGES . '/logo_menu.png'));
	}

	public function getToolbarLogoSRC() {
		if (Config::get('concrete.white_label.name')) {
			$alt = Config::get('concrete.white_label.name');
		}
		if (!$alt) {
			$alt = 'concrete5';
		}
		if (Config::get('concrete.white_label.logo')) {
			$src = Config::get('concrete.white_label.logo');
		}
		if (!$src) {
			$filename = 'logo.png';
			if (file_exists(DIR_APPLICATION . '/' . DIRNAME_IMAGES . '/' . $filename)) {
				$src = REL_DIR_APPLICATION . '/' . DIRNAME_IMAGES . '/' . $filename;
				$d = getimagesize(DIR_APPLICATION . '/' . DIRNAME_IMAGES . '/' . $filename);
				$dimensions = $d[3];
			} else {
				$src = ASSETS_URL_IMAGES . '/' . $filename;
				$dimensions = 'width="23" height="23"';
			}
		}
		return '<img id="ccm-logo" src="' . $src . '" ' . $dimensions . ' alt="' . $alt . '" title="' . $alt . '" />';
	}

	public function showNewsflowOverlay() {
		$tp = new \TaskPermission();
		$c = Page::getCurrentPage();
        if ((!defined(MOBILE_THEME_IS_ACTIVE) || MOBILE_THEME_IS_ACTIVE == false) && Config::get('concrete.core.news_overlay') && $tp->canViewNewsflow() && $c->getCollectionPath() != '/dashboard/news') {
			$u = new ConcreteUser;
			$nf = $u->config('NEWSFLOW_LAST_VIEWED');
			if ($nf == 'FIRSTRUN') {
				return false;
			}

			if (Config::get('concrete.maintenance_mode')) {
				return false;
			}

			if (!$nf) {
				return true;
			}
			if (time() - $nf > NEWSFLOW_VIEWED_THRESHOLD) {
				return true;
			}
		}
		return false;
	}

	public function clearInterfaceItemsCache() {
		$u = new ConcreteUser();
		if ($u->isRegistered()) {
			Session::remove('dashboardMenus');
		}
	}

	public function cacheInterfaceItems() {
		$u = new ConcreteUser();
		if ($u->isRegistered()) {
			Loader::helper('concrete/dashboard')->getIntelligentSearchMenu();
		}
	}

	public function pagetabs($tabs) {
		$tcn = rand(0, getrandmax());

		$html = '<ul class="nav-tabs nav" id="ccm-tabs-' . $tcn . '">';
		$c = Page::getCurrentPage();
		foreach($tabs as $t) {
			if (is_array($t)) {
				$name = $t[1];
				$_c = $t[0];
			} else {
				$_c = $t;
				$name = $t->getCollectionName();
			}

			$href = Loader::helper('navigation')->getLinkToCollection($_c);
			$active = false;
			if (is_object($c) && $c->getCollectionID() == $_c->getCollectionID()) {
				$active = true;
			}
			$html .= '<li class="' . (($active) ? 'active' : ''). '"><a href="' . $href . '">' . $name . '</a></li>';
		}
		$html .= '</ul>';
		return $html;
	}

	public function tabs($tabs, $jstabs = true, $callback = 'ccm_activateTabBar') {
		$tcn = rand(0, getrandmax());

		$html = '<ul class="nav-tabs nav" id="ccm-tabs-' . $tcn . '">';
		foreach($tabs as $t) {
			$dt = $t[0];
			$href = '#';
			if (!$jstabs) {
				$dt = '';
				$href = $t[0];
			}
			$html .= '<li class="' . ((isset($t[2]) && $t[2] == true) ? 'active' : ''). '"><a href="' . $href . '" data-tab="' . $dt . '">' . $t[1] . '</a></li>';
		}
		$html .= '</ul>';
		if ($jstabs) {
			$html .= '<script type="text/javascript">$(function() { ' . $callback . '($(\'#ccm-tabs-' . $tcn . '\'));});</script>';
		}
		return $html;
	}


	public function renderError($title, $error, $exception = false) {
		$o = new stdClass;
		$o->title = $title;
		$o->content = $error;
		if ($exception) {
			$o->content .= $exception->getTraceAsString();
		}
		$ve = new ErrorView($o);
		$response = $ve->render($o);
		print $response;
	}

	public function notify($arguments) {
		$defaults = array(
			'type' => 'success',
			'icon' => 'ok',
			'title' => false,
			'message' => false,
			'buttons' => array()
		);

		// overwrite all the defaults with the arguments
		$arguments = array_merge($defaults, $arguments);

        if ($arguments['title']) {
            $messageText = '<h3>' . $arguments['title'] . '</h3>' . $arguments['message'];
        } else {
            $messageText = '<h3>' . $arguments['message'] . '</h3>';
        }

        if (count($arguments['buttons']) > 0) {
        	$messageText .= '<div class="ccm-notification-inner-buttons">';
        	foreach($arguments['buttons'] as $button) {
        		$messageText .= $button;
        	}
        	$messageText .= '</div>';
        }

        $content = '<div id="ccm-notification-page-alert" class="ccm-ui ccm-notification ccm-notification-' . $arguments['type'] . '">';
        $content .= '<i class="ccm-notification-icon fa fa-' . $arguments['icon'] . '"></i><div class="ccm-notification-inner">' . $messageText . '</div>';
		$content .= '<div class="ccm-notification-actions"><a href="#" data-dismiss-alert="page-alert">' . t('Hide') . '</a></div></div>';
        return $content;

	}

}
