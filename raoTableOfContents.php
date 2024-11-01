<?php
/**
 * Plugin Name: WPjura Table of Contents
 * Description: This plugin places a table of contents in posts to allow the direct jumping of headings.
 *              Dieses Plugin plaziert ein Inhaltsverzeichnis in Posts, um das direkte Anspringen von Überschriften zu ermöglichen.
 * Version: 0.0.17
 * Author: wpjura
 * Developer: ra-online GmbH (Marco Mruk)
 * Text Domain: raoTableOfContents
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

 if (!defined('ABSPATH')) { exit(); }
?><?php
 // Stelle die erwartete Ausführungumgebung bereit
 global $wp_version;
 if (!defined('WPversion')) { define('WPversion', $wp_version); }
 if (!defined('PHPversion')) { define('PHPversion', phpversion()); }
 if (!defined('raonline_raoTableOfContents_pluginDisplayname')) { define('raonline_raoTableOfContents_pluginDisplayname', 'WPjura Table of Contents'); }
 if (!defined('raonline_raoTableOfContents_pluginName')) { define('raonline_raoTableOfContents_pluginName', 'raoTableOfContents'); }
 if (!defined('raonline_raoTableOfContents_pluginPath')) { define('raonline_raoTableOfContents_pluginPath', preg_replace('/[\/\\\\]$/', '', plugin_dir_path(__file__)) . '/'); }
 if (!defined('raonline_raoTableOfContents_pluginURL')) { define('raonline_raoTableOfContents_pluginURL', preg_replace('/[\/\\\\]$/', '', plugin_dir_url(__file__)) . '/'); }
 if (!defined('raonline_raoTableOfContents_langPath')) { define('raonline_raoTableOfContents_langPath', dirname(plugin_basename(__file__)) . '/lang/'); }
 if (!defined('raonline_raoTableOfContents_langTextDomain')) { define('raonline_raoTableOfContents_langTextDomain', raonline_raoTableOfContents_pluginName); }
 if (!defined('raonline_raoTableOfContents_minWPversion')) { define('raonline_raoTableOfContents_minWPversion', '4.8.2'); }
?><?php
 // Führe das Plugin aus ...
 require_once(raonline_raoTableOfContents_pluginPath . '/lib/class_tableOfContents.inc.php'); 
 $tableOfContents = new raonline\raoTableOfContents\tableOfContents();
?>