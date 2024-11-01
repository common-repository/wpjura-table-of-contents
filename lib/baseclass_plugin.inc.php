<?php
 namespace raonline;

 if (!defined('ABSPATH')) { exit(); }

 /* --------------------------------------------------------------------------------------------------------------------------------
         Aufgabe: Klasse zum Bereitstellen von Funktionen zur Textgestaltung
     Bemerkungen: keine
    -------------------------------------------------------------------------------------------------------------------------------- */
     abstract class plugin {
 
       // Deklaration der Klassenvariablen
       protected $_className;
       protected $_pluginDisplayname;
       protected $_pluginName; 
       protected $_pluginPath;
       protected $_pluginURL;
       protected $_pluginSlug;
       protected $_langPath;
       protected $_langTextDomain;
       protected $_minWPversion;
 
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Initialisiere die Klasse
             Eingabe: der Anzeigename des Plugins, der interne Name des Plugins, der Basispfad des Plugins (auf dem Web-Server), die
                      Basis-URI des Plugins aus dem Internet, der Pfad zu den Sprachdateien (auf dem Web-Server), die Text-Domain für
                      die Multilanguage-Unterstützung, sowie die minimal notwendige Wordpress-Version
             Ausgabe: nichts
         Bemerkungen: (Konstruktor der Klasse)
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function __construct($pluginDisplayname, $pluginName, $pluginPath, $pluginURL, $langPath, $langTextDomain, $minWPversion) {
           // Initialisiere den Klassenvariablen, welche nicht auf Einstellungen des Plugins basieren
           $this->_className = preg_replace('/^([^\/]+\\\\)*(.*)$/u', '$2', get_class($this));
           $this->_pluginDisplayname = $pluginDisplayname;
           $this->_pluginName = $pluginName; 
           $this->_pluginPath = $pluginPath;
           $this->_pluginURL = $pluginURL;
           $this->_pluginSlug = plugin_basename($this->_pluginPath . $this->_pluginName . '.php');       
           $this->_langPath = $langPath;
           $this->_langTextDomain = $langTextDomain;
           $this->_minWPversion = $minWPversion;
			     // Verhindere die Ausführung auf veralteten Wordpress-Versionen
           if (!version_compare(WPversion, $minWPversion, '>=')) {
             add_action('admin_notices', array(&$this, 'outputAdminnote_wpVersionFailed'));
             return false;
            }
          } /* (end constructor) */         


     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Ausgabe einer Fehlermeldung im Backend
             Eingabe: nichts
             Ausgabe: nichts
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
		     public function outputAdminnote_wpVersionFailed() {
           if (preg_match('/^de_[a-z]{2}$/iu', get_locale()) > 0) {
             $temp = 'Ihre Version von WordPress ist zu alt! %s benötigt mindestens WordPress %s!';             
            } else {
             $temp = 'Your version of wordpress is too old! %s needs at least wordpress %s!';
            }
           $temp = sprintf($temp, $this->_pluginName, $this->_minWPversion); 
           echo('<div id="message" class="error fade"><p>' . $temp . '</p></div>');
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Ausgabe einer Fehlermeldung im Backend
             Eingabe: nichts
             Ausgabe: nichts
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
		     public function outputAdminnote_premiumVersionNotStandalone() {
           if (preg_match('/^de_[a-z]{2}$/iu', get_locale()) > 0) {
             $temp = 'Verwenden Sie nur das Plugin %s oder seine Premium-Version %s, da es sonst zu Funktionseinschränkungen kommen kann.';
            } else {
             $temp = 'Use only the plugin %s or its premium version %s, as otherwise there may be functional limitations.';
            }
           $temp = sprintf($temp, '&bdquo;<b>' . preg_replace('/[ ]+Premium$/iu', '', $this->_pluginDisplayname) . '</b>&ldquo;', '&bdquo;<b>' . $this->_pluginDisplayname . '</b>&ldquo;'); 
           echo('<div id="message" class="error fade"><p>' . $temp . '</p></div>');
          } /* (end function) */

     /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Ermittle den Slugs dieses Plugins
               Eingabe: nichts
               Ausgabe: nichts
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           public function getPluginSlug() {
             // Ermittle den passenden Pfad
             $path = $this->_pluginPath . $this->_pluginName . '.php';
             // Gebe die Aufgabe an eine Schwestermethode ab
             $result = self::getSomeOnePluginSlug($path);
             // Rückgabe des ermittelten Slugs
             return $result;
            } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Ermittle den Slugs der Basisversion dieses Plugins
               Eingabe: nichts
               Ausgabe: nichts
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           public function getBasePluginSlug() {
             // Ermittle den passenden Pfad
             $pathA = ( preg_match('/^(.*)_premium(\/?)$/iu', $this->_pluginPath, $match) > 0 ? $match[1] . $match[2] : $this->_pluginPath );              
             $pathB = ( preg_match('/^(.*)_premium$/iu', $this->_pluginName, $match) > 0 ? $match[1] : $this->_pluginName );
             $path = $pathA . $pathB . '.php';
             // Gebe die Aufgabe an eine Schwestermethode ab
             $result = self::getSomeOnePluginSlug($path);
             // Rückgabe des ermittelten Slugs
             return $result;
            } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Ermittle den Slugs der Premiumversion dieses Plugins
               Eingabe: nichts
               Ausgabe: nichts
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           public function getPremiumPluginSlug() {
             // Ermittle den passenden Pfad
             $pathA = ( preg_match('/_premium(\/?)$/iu', $this->_pluginPath) > 0 ? $this->_pluginPath : preg_replace('/^(.*?)(\/?)$/iu', '$1_premium$2', $this->_pluginPath) );              
             $pathB = ( preg_match('/_premium$/iu', $this->_pluginName) > 0 ? $this->_pluginName : $this->_pluginName . '_premium' );
             $path = $pathA . $pathB . '.php';
             // Gebe die Aufgabe an eine Schwestermethode ab
             $result = self::getSomeOnePluginSlug($path);
             // Rückgabe des ermittelten Slugs
             return $result;
            } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Ermittle den Slugs eines Plugins
               Eingabe: nichts
               Ausgabe: nichts
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           public static function getSomeOnePluginSlug($path) {
             // Initialisiere den Rückgabewert
             $result = null;
             // Normiere den übergebenen Pfad
             if (preg_match('/^[ ]*$/u', $path) <= 0) {
               $path = str_replace('\\', '/', $path);
               $temp = str_replace('\\', '/', ABSPATH . 'wp-content/plugins/');
               $temp = str_replace('/', '\/', preg_quote($temp));
               $result = preg_replace('/^' . $temp . '/u', '', $path);
              }
             // Rückgabe des ermittelten Slugs
             return $result;
            } /* (end function) */

     /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Lade die Unterstützung für die Mehrsprachigkeit über einen Hook
               Eingabe: nichts
               Ausgabe: nichts
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           public function activateMultiLanguageSupport() {
             add_action('plugins_loaded', array(&$this, 'initMultiLanguageSupport'));
            } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Lade die Sprachdateien für die Mehrsprachigkeit über einen Hook
               Eingabe: nichts
               Ausgabe: nichts
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           public function initMultiLanguageSupport() {
             load_plugin_textdomain($this->_langTextDomain, false, $this->_langPath);   
            } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Überlade eine Funktion von Wordpress zur Umsetzung der Mehrsprachigkeit, um die Eingabe der Text-Domain
                        zu vermeiden
               Eingabe: der auszugebemde Text (in Englisch)
               Ausgabe: der ggf. übersetzte Text (in der aktuell eingestellten Sprache)
           Bemerkungen: (Vereinfachung einer Wordpress-Funktion)
          -------------------------------------------------------------------------------------------------------------------------------- */
           public function __($text) {
             return __($text, $this->_langTextDomain);
            } /* (end function) */         
     /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Überlade eine Funktion von Wordpress zur Umsetzung der Mehrsprachigkeit, um die Eingabe der Text-Domain
                        zu vermeiden
               Eingabe: der auszugebemde Text (in Englisch)
               Ausgabe: nichts
           Bemerkungen: (Vereinfachung einer Wordpress-Funktion)
          -------------------------------------------------------------------------------------------------------------------------------- */
           public function _e($text) {
             _e($text, $this->_langTextDomain);
            } /* (end function) */         


     /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Löse das Laden der relevanten Stylesheets und Skripte für die eigentliche Webseite in Wordpress über
                        einen Hook aus
               Eingabe: nichts
               Ausgabe: nichts
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           public function loadStylesAndScriptsInFrontend() {
             add_action('wp_enqueue_scripts', array(&$this, 'doStylesAndScriptsLoadingInFrontend'), 100);
            } /* (end function) */         
     /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Löse das Laden der relevanten Stylesheets und Skripte für die Admin-Oberfläche in Wordpress
                        über einen Hook aus
               Eingabe: nichts
               Ausgabe: nichts
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           public function loadStylesAndScriptsInBackend() {
             add_action('admin_enqueue_scripts', array(&$this, 'doStylesAndScriptsLoadingInBackend'), 100);
            } /* (end function) */         
     /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Führe das Laden der relevanten Stylesheets und Skripte in Wordpress durch
               Eingabe: nichts
               Ausgabe: nichts
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           public function doStylesAndScriptsLoading($possibleFilenames) {
             // Initialisiere die Ersetzungswerte
             $replacementValues = array('className' => null, 'themeURL' => null, 'themePath' => null, 'themeName' => null,  'pluginURL' => null, 'pluginPath' => null, 'pluginName' => null);
             // Versuche die passenden Dateien einzubinden
             if (is_array($possibleFilenames)) {
               foreach ($possibleFilenames as $key1 => $value1) {
                 foreach ($value1['filenames'] as $key2 => $value2) {
                   $path = $this->replaceThemeAndPluginMarks($value2, $replacementValues);
                   $url = $this->replaceThemeAndPluginMarks(preg_replace('/\{(theme|plugin)Path\}/iu', '{$1URL}', $value2), $replacementValues);                
                   $key1 = $this->replaceThemeAndPluginMarks($key1, $replacementValues);
                   // Prüfe die Existenz der Datei ...
                   if (file_exists($path)) {
                     if (preg_match('/^(css|style)$/iu', $value1['type']) > 0) {
                       wp_enqueue_style($key1, $url);
                      } elseif (preg_match('/^(js|scripts?)$/iu', $value1['type']) > 0) {
                       if (isset($value1['addOnScript'])) {
                         wp_enqueue_script($key1, $url, array($value1['addOnScript']), false, true);
                        } else {
                         wp_enqueue_script($key1, $url);
                        }
                      }
                     break;
                    }
                  }
                }
              }
            } /* (end function) */
                     
     /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Ersetze Markierungen bez. Theme oder Plugin in einem Text
               Eingabe: der zu bearbeitende Text, sowie eine Referenz auf die Werte hinter den Markierungen
               Ausgabe: der angepasste Text
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           protected function replaceThemeAndPluginMarks($text, &$replacementValues) {
             // Initialisiere den Rückgabewert
             $result = $text;
             // Bestimme, welche Markierungen ersetzt werden müssen
             if (is_array($replacementValues)) {
               foreach ($replacementValues as $key1 => $value1) {
                 if (preg_match('/\{' . preg_quote($key1) . '\}/iu', $text) > 0) {
                   // Ergänze ggf. die fehlende Information
                   if ($replacementValues[$key1] === null) {
                     switch ($key1) {
                       case 'className':
                         $replacementValues[$key1] = $this->_className;
                         break;
                       case 'themeURL':
                       case 'themePath':
                       case 'themeName':
                         $replacementValues['themeURL'] = preg_replace('/\/$/', '', get_template_directory_uri());
                         $replacementValues['themePath'] = get_stylesheet_directory();
                         $replacementValues['themeName'] = preg_replace('/^' . str_replace('/', '\/', preg_quote(get_theme_root_uri())) . '\/?/', '', $replacementValues['themeURL']);
                         break;
                       case 'pluginURL':
                         $replacementValues[$key1] = $this->_pluginURL;
                         break;
                       case 'pluginPath':
                         $replacementValues[$key1] = $this->_pluginPath;
                         break;
                       case 'pluginName':
                         $replacementValues[$key1] = $this->_pluginName;
                         break;
                      }
                    }
                   // Ersetze die Markierung
                   $result = preg_replace('/\{' . preg_quote($key1) . '\}/iu', $replacementValues[$key1], $result); 
                  }
                }
              }
             // Rückgabe des angepassten Textes
             return $result;
            } /* (end function) */         


     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Aktiviere die unterstützten Shortcodes
             Eingabe: nichts
             Ausgabe: die zusammengestellte Liste
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function activateShortcodes() {
           $availableShortCodes = $this->getShortcodeList(false);
           foreach ($availableShortCodes as $key1 => $value1) {           
             $keyList = ( isset($value1['keyList']) ? preg_split('/[ ]*\|[ ]*/u', $value1['keyList']) : array($key1) );
             $function = ( isset($value1['function']) ? $value1['function'] : $value1 );
             foreach ($keyList as $key2 => $value2) { add_shortcode($value2, $function); } 
            }
          } /* (end function) */

     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Normalisiere den übergebenen Inhalt eines unterstützten Shortcodes
             Eingabe: eine Referenz auf den übergebenen Inhalt 
             Ausgabe: nichts
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         protected static function normalizeShortcodeContent(&$content) {
           $content = ( preg_match('/^[ ]*$/u', $content, $match) > 0 ? null : $content );
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Normalisiere den übergebenen Attribute eines unterstützten Shortcodes
             Eingabe: eine Referenz auf die übergebenen Attribute, eine Liste der gültigen Attributschlüssel, ein regulärer Ausdruck
                      zur Prüfung des Attributwertes, sowie optional ein Standardwert für dieses Attribut
             Ausgabe: nichts
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         protected static function normalizeShortcodeAttributes(&$attributes, $keyList, $pattern, $defaultValue = null) {
           // Blockiere, wenn keine Attribute vorliegen
           if (!is_array($attributes)) {
             $attributes = array();
            }
           // Bereite die übergebenen Daten auf ...
           $keyList = preg_split('/[ ]*[|][ ]*/u', $keyList);
           $selectedKey = reset($keyList);
           if (is_array($pattern)) {
             foreach ($pattern as $key1 => $value1) {
               $negotation = ( preg_match('/^\!/u', $key1) > 0 );
               if ($negotation === true) {
                 unset($pattern[$key1]);
                 $key1 = preg_replace('/^\!/u', '', $key1);
                }
               $pattern[$key1] = array('originalValue' => false, 'value' => $value1, 'negotation' => $negotation);
              }
            } else {
             $negotation = ( preg_match('/^\!/u', $pattern) > 0 );
             if ($negotation === true) { $pattern = preg_replace('/^\!/u', '', $pattern); }
             $pattern = array($pattern => array('originalValue' => true, 'value' => null, 'negotation' => $negotation));
            }
           // Führe die Prüfung durch ...
           $temp = $defaultValue;
           if ($temp === $defaultValue) {
             foreach ($keyList as $key1 => $value1) {
               if (isset($attributes[$value1])) {
                 foreach ($pattern as $key2 => $value2) {
                   if ($value2['negotation'] === true) {
                     $temp = ( preg_match($key2, $attributes[$value1]) <= 0 ? ( $value2['originalValue'] ? $attributes[$value1] : $value2['value'] ) : $temp );
                    } else {
                     $temp = ( preg_match($key2, $attributes[$value1]) > 0 ? ( $value2['originalValue'] ? $attributes[$value1] : $value2['value'] ) : $temp );
                    }
                  }
                }
              }
            }
           if ($temp === $defaultValue) {
             foreach ($keyList as $key1 => $value1) {
               foreach ($attributes as $key2 => $value2) {
                 if (preg_match('/^' . $key2 . '$/iu', $value1) > 0) {
                   foreach ($pattern as $key3 => $value3) {
                     if ($value3['negotation'] === true) {
                       $temp = ( preg_match($key3, $value2) <= 0 ? ( $value3['originalValue'] ? $value2 : $value3['value'] ) : $temp );
                      } else {
                       $temp = ( preg_match($key3, $value2) > 0 ? ( $value3['originalValue'] ? $value2 : $value3['value'] ) : $temp );
                      }
                    }
                   unset($attributes[$key2]);
                  }
                }
              }
            }
           $attributes[$selectedKey] = $temp;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Normalisiere den übergebenen Attribute eines unterstützten Shortcodes
             Eingabe: eine Referenz auf die übergebenen Attribute, eine Liste der gültigen Attributschlüssel, ein regulärer Ausdruck
                      zur Prüfung des Attributwertes, sowie optional ein Standardwert für dieses Attribut
             Ausgabe: nichts
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         protected static function normalizeShortcodeAttributesPlus(&$attributes, $keyList, $pattern, $defaultValueA = null, $defaultValueB = null) {
           // Blockiere, wenn keine Attribute vorliegen
           if (!is_array($attributes)) {
             $attributes = array();
            }
           // Bereite die übergebenen Daten auf ...
           $keyList = preg_split('/[ ]*[|][ ]*/u', $keyList);
           $selectedKey = reset($keyList);
           if (is_array($pattern)) {
             foreach ($pattern as $key1 => $value1) {
               $negotation = ( preg_match('/^\!/u', $key1) > 0 );
               if ($negotation === true) {
                 unset($pattern[$key1]);
                 $key1 = preg_replace('/^\!/u', '', $key1);
                }
               $pattern[$key1] = array('originalValue' => false, 'value' => $value1, 'negotation' => $negotation);
              }
            } else {
             $negotation = ( preg_match('/^\!/u', $pattern) > 0 );
             if ($negotation === true) { $pattern = preg_replace('/^\!/u', '', $pattern); }
             $pattern = array($pattern => array('originalValue' => true, 'value' => null, 'negotation' => $negotation));
            }
           // Führe die Prüfung durch ...
           $temp = $defaultValueA;
           if ($temp === $defaultValueA) {
             foreach ($keyList as $key1 => $value1) {
               if (isset($attributes[$value1])) {
                 foreach ($pattern as $key2 => $value2) {
                   if ($value2['negotation'] === true) {
                     $temp = ( preg_match($key2, $attributes[$value1]) <= 0 ? ( $value2['originalValue'] ? $attributes[$value1] : $value2['value'] ) : $temp );
                    } else {
                     $temp = ( preg_match($key2, $attributes[$value1]) > 0 ? ( $value2['originalValue'] ? $attributes[$value1] : $value2['value'] ) : $temp );
                    }
                  }
                }
              }
            }
           if ($temp === $defaultValueA) {
             foreach ($keyList as $key1 => $value1) {
               foreach ($attributes as $key2 => $value2) {
                 if (preg_match('/^' . $key2 . '$/iu', $value1) > 0) {
                   foreach ($pattern as $key3 => $value3) {
                     if ($value3['negotation'] === true) {
                       $temp = ( preg_match($key3, $value2) <= 0 ? ( $value3['originalValue'] ? $value2 : $value3['value'] ) : $temp );
                      } else {
                       $temp = ( preg_match($key3, $value2) > 0 ? ( $value3['originalValue'] ? $value2 : $value3['value'] ) : $temp );
                      }
                    }
                   unset($attributes[$key2]);
                  }
                }
              }
            }
           if ($temp === $defaultValueA) {
             foreach ($keyList as $key1 => $value1) {
               foreach ($attributes as $key2 => $value2) {
                 if ((preg_match('/^[0-9]+$/iu', $key2) > 0) and (preg_match('/^' . preg_quote($value1) . '$/iu', $value2) > 0)) {
                   $temp = ( null !== $defaultValueB ? $defaultValueB : $defaultValueA );
                  }
                }
              }
            }
           $attributes[$selectedKey] = $temp;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Normalisiere einen übergebenen Werte in Bezug auf einen unterstützten Shortcode
             Eingabe: der eigentliche Wert, eine Liste von gültigen Werten, sowie eine Option zur Unterscheidung der Groß- und
                      Kleinschreibung 
             Ausgabe: nichts
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         protected static function normalizeShortcodeValue(&$value, $valueList, $caseSensitive = false) {
           // Bereite die übergebenen Daten auf ...
           $valueList = preg_split('/[|]/u', $valueList);
           // Führe die Prüfung durch ...
           $temp = null;
           foreach ($valueList as $key1 => $value1) {
             $temp = ( preg_match('/^' . $value1 . '$/u' . ( $caseSensitive === true ? '' : 'i' ), $value) > 0 ? $value1 : $temp );
            }
           $value = $temp; 
          } /* (end function) */         
          
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Normalisiere einen Text für die Nutzung als HTML-kompatibler Bezeichner für eine ID
             Eingabe: der ursprüngliche Text, welcher vereinfacht werden soll, sowie optional die Möglichkeit ein alternatives
                      Platzhalterzeichen für inkompatible Zeichen zu definieren 
             Ausgabe: nichts
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public static function normHMTLidentifier($text, $placeholderChar = null) {
           // Normiere die übergebenen Parameter
           $placeholderChar = ( preg_match('/^[_-]$/u', $placeholderChar) > 0 ? $placeholderChar : '_' );
           // Stelle den Rückgabewert zusammen ...
           $result = $text;
           $result = str_replace('ß', 'ss', $result);
           $result = str_replace('Ä', 'Ae', $result);
           $result = str_replace('Ö', 'Oe', $result);
           $result = str_replace('Ü', 'Ue', $result);
           $result = str_replace('ä', 'ae', $result);
           $result = str_replace('ö', 'ae', $result);
           $result = str_replace('ü', 'ue', $result);
           $result = preg_replace('/[^a-z0-9]+/iu', $placeholderChar, $result);
           $result = preg_replace('/' . preg_quote($placeholderChar) . '+$/u', '', $result);
           // Rückgabe des normierten Textes
           return $result;
          } /* (end function) */

     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Verschiebe automatisch alle Untermenüpunkte der ra-online einen seperates Untermenü, wenn gewisse Bedingungen
                      erfüllt sind 
             Eingabe: nichts 
             Ausgabe: der Slug des Hauptmenüs, in dass die weiteren Untermenüpunkte eingefügt werden sollen
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public static function automoveMySubmenus() {
           // Initialisiere den Rückgabewert
           $result = null;
           // Prüfe, ob bereits ein seperates Hauptmenü für die ra-online existiert ...
           if (!$result) {
             global $admin_page_hooks;
             foreach ($admin_page_hooks as $key1 => $value1) {
               if (preg_match('/^raonline[-_][a-z0-9]+(\.php)?$/iu', $key1) > 0) {
                 $result = $key1;
                 break;
                }
              }
            }  
           // Prüfe, ob bereits mehr als ein Untermenüpunkt in den Einstellungen verankert wurde ...
           if (!$result) {
             $menuKeys = array();
             global $submenu; global $wp_filter;
             if (isset($submenu['options-general.php'])) {
               foreach ($submenu['options-general.php'] as $key1 => $value1) {
                 if (preg_match('/^raonline[-_][a-z0-9]+(\.php)?$/iu', $value1[2]) > 0) {
                   // Prüfe, ob eine Funktion für den Menüpunkt hinterlegt wurde ...
                   $func = null;
                   $hookname = get_plugin_page_hookname($value1[2], 'options-general.php');
                   $temp = reset($wp_filter[$hookname]->callbacks);
                   $func = reset($temp); $func = $func['function'];
                   // Speichere die Eigenschaften der gefundenen Menüpunkte
                   if (!$menuKeys) { $menuKeys = array(); }
                   $menuKeys[$key1] = array('page_title' => $value1[3], 'menu_title' => $value1[0], 'capability' => $value1[1], 'menu_slug' => $value1[2], 'function' => $func);
                  } 
                }
               // Prüfe, ob die entsprechende Anzahl erreicht wurde, um die Untermenüpunkte umzuziehen ...
               if (($menuKeys) and (count($menuKeys) > 1)) {
                 $k = 0;
                 foreach ($menuKeys as $key1 => $value1) {
                   $k++;
                   if ($k == 1) {
                     $result = $value1['menu_slug'];
                     add_menu_page('ra-online', 'ra-online', 'manage_options', $result, $value1['function']);
                    }
                   add_submenu_page($result, $value1['page_title'], $value1['menu_title'], $value1['capability'], $value1['menu_slug'], $value1['function']);
                   remove_submenu_page('options-general.php', $value1['menu_slug']);
                  } 
                }
              }              
            }  
           // Wenn nichts anderes zieht, dann ist es das Einstellungsmenü
           if (!$result) { $result = 'options-general.php'; }
           // Rückgabe des ermittelten Slugs
           return $result;
          } /* (end function) */
          
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere eine Zeile für die Optionsseite 
             Eingabe: der Type der Zeile, die Beschriftung der Zeile, der Tiptext passend zur Beschriftung, optional der einzufügende
                      Inhalt, sowie optional die Attribute für die Zeile    
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateLineOfOptionPage($lineType, $label, $title, $subcode, $attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           $lineType = ( preg_match('/^[a-c]$/iu', $lineType) > 0 ? $lineType : 'A' );
           $label = ( preg_match('/^[ ]*$/u', $label) <= 0 ? $label : null );
           $title = ( preg_match('/^[ ]*$/u', $title) <= 0 ? $title : null );
           $subcode = ( preg_match('/^[ ]*$/u', $subcode) <= 0 ? $subcode : null );
           if (!is_array($attributes)) {
             $attributes = null;
            } else {
             foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
            }
           // Stelle den Quellcode zusammen             
           $code = array();
           $code[] = '<tr lineType="' . $lineType . '"' . ( $attributes ? implode('', $attributes) : '' ) . '>';
           if (null !== $label) {
             $code[] = '<th' . ( $title ? ' title="' . $title . '"' : '' ) . '>' . $label . '</th>';
            } else {
             $code[] = '<td>&nbsp;</td>';
            } 
           $code[] = '<td>' . ( $subcode ? $subcode : '&nbsp;' ) . '</td>';
           $code[] = '</tr>';
           $result = implode('', $code);
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere eine Untertabelle für die Optionsseite 
             Eingabe: der einzufügende Inhalt, sowie optional die Attribute für die Zeile    
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateSubtableOfOptionPage($subcode, $attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           $subcode = ( preg_match('/^[ ]*$/u', $subcode) <= 0 ? $subcode : null );
           if (!is_array($attributes)) { $attributes = array(); }
           if (!isset($attributes['class'])) { $attributes['class'] = ''; }
           $attributes['class'] = 'form' . ( (preg_match('/^[ ]*$/', $attributes['class']) <= 0) ? ', ' : '' ) . $attributes['class'];
           foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
           // Stelle den Quellcode zusammen
           if ($subcode) {
             $code = array();
             $code[] = '<table' . ( $attributes ? implode('', $attributes) : '' ) . '>';
             $code[] = $subcode;
             $code[] = '</table>';
             $result = implode('', $code);
            }             
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere ein Auswahlfeld für die Optionsseite 
             Eingabe: der Name des Feldes, der Wert (mit dem vorbelegt werden soll), die Liste der verfügbaren Optionen, sowie
                      optional die Attribute für dieses Feld 
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateSelectFieldOfOptionPage($name, $value, $options, $attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           $name = ( preg_match('/^[ ]*$/u', $name) <= 0 ? $name : null );
           $value = ( preg_match('/^[a-z0-9% _\.,-]+$/iu', $value) > 0 ? $value : null );
           if (!is_array($options)) {
             $options = null;
            } else { 
             foreach ($options as $key1 => $value1) {
               if ((!isset($value1['label'])) or (!isset($value1['options']))) {
                 $temp = array('label' => null, 'options' => array()); 
                 foreach ($options as $key2 => $value2) { $temp['options'][$key2] = $value2; }
                 $options = array($temp); 
                }
               break;
              }
            }
           if (!is_array($attributes)) {
             $attributes = null;
            } else {
             foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
            }
           // Stelle den Quellcode zusammen             
           if ($name and $options) {  
             $code = array();
             $code[] = '<select name="' . $name . '"' . ( $attributes ? implode('', $attributes) : '' ) . '>';
             foreach ($options as $key1 => $value1) {
               if ($value1['label']) { $code[] = '<optgroup label="&nbsp;' . $value1['label'] . '">'; }
               foreach ($value1['options'] as $key2 => $value2) {
                 $code[] = ' <option value="' . $key2 . '"' . ( $value == $key2 ? ' selected="selected"' : '' ) . '>' . $value2 . '</option>';
                }
               if ($value1['label']) { $code[] = '</optgroup>'; } 
              }
             $code[] = '</select>';
             $result = implode('', $code);
            }  
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere ein einzeiliges Textfeld für die Optionsseite 
             Eingabe: der Name des Feldes, der Wert (mit dem vorbelegt werden soll), sowie optional die Attribute für dieses Feld 
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateTextFieldOfOptionPage($name, $value, $attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           $name = ( preg_match('/^[ ]*$/u', $name) <= 0 ? $name : null );
           $value = ( preg_match('/^[[:print:]]+$/iu', $value) > 0 ? $value : null );
           if (!is_array($attributes)) {
             $attributes = null;
            } else {
             foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
            }
           // Stelle den Quellcode zusammen             
           if ($name) {  
             $code = array();
             $code[] = '<input type="text" name="' . $name . '"' . ( null !== $value ? ' value="' . $value . '"' : '' ) . ( $attributes ? implode('', $attributes) : '' ) . '>';
             $result = implode('', $code);
            }  
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere ein mehrzeiliges Textfeld für die Optionsseite 
             Eingabe: der Name des Feldes, der Wert (mit dem vorbelegt werden soll), sowie optional die Attribute für dieses Feld 
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateTextareaFieldOfOptionPage($name, $value, $attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           $name = ( preg_match('/^[ ]*$/u', $name) <= 0 ? $name : null );
           $value = str_replace('\"', '"', $value);
           $value = ( preg_match('/^[[:print:]\r\n]+$/isu', $value) > 0 ? $value : null );
           if (!is_array($attributes)) {
             $attributes = null;
            } else {
             foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
            }
           // Stelle den Quellcode zusammen             
           if ($name) {  
             $code = array();
             $code[] = '<textarea type="text" name="' . $name . '"' . ( $attributes ? implode('', $attributes) : '' ) . '>' . ( null !== $value ? $value : '' )  . '</textarea>';
             $result = implode('', $code);
            }  
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere eine Checkbox für die Optionsseite 
             Eingabe: der Name des Feldes, der erläuternde Text zu dieser Option, der Tiptext passend zum erläuternden Text, der Wert
                      (mit dem vorbelegt werden soll), sowie optional die Attribute für dieses Feld 
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateCheckBoxOfOptionPage($name, $value, $attributes = null) {         
           // Übergebe diese Aufgabe an eine Schwestermethode
           return $this->generateCheckBoxWithLabelOfOptionPage($name, null, null, $value, $attributes);
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere eine Checkbox für die Optionsseite 
             Eingabe: der Name des Feldes, der erläuternde Text zu dieser Option, der Tiptext passend zum erläuternden Text, der Wert
                      (mit dem vorbelegt werden soll), sowie optional die Attribute für dieses Feld 
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateCheckBoxWithLabelOfOptionPage($name, $label, $title, $value, $attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           $name = ( preg_match('/^[ ]*$/u', $name) <= 0 ? $name : null );
           $label = ( preg_match('/^[ ]*$/u', $label) <= 0 ? $label : null );
           $title = ( preg_match('/^[ ]*$/u', $title) <= 0 ? $title : null );
           $value = ( preg_match('/^[a-z0-9% _\.,-]+$/iu', $value) > 0 ? $value : null );
           if (!is_array($attributes)) {
             $htmlID = null; $attributes = null;
            } else {
             $htmlID = ( isset($attributes['id']) ? $attributes['id'] : null );
             foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
            }
           // Stelle den Quellcode zusammen             
           $code = array();
           $code[] = '<input type="checkbox"' . ( $name ? ' name="' . $name . '"' : '' ) . ( $attributes ? implode('', $attributes) : '' ) . ( $value === true ? ' checked="checked"' : '' ) . '>';
           if ($label) {
             $code[] = '<label' . ( $htmlID !== null ? ' for="' . $htmlID . '"' : '' ) . ( $title ? ' title="' . $title . '"' : '' ) . '>' . $label . '</label>';
            }
           $result = implode('', $code);
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere eine Radio-Button für die Optionsseite 
             Eingabe: der Name des Feldes, der erläuternde Text zu dieser Option, der Tiptext passend zum erläuternden Text, der Wert
                      (mit dem vorbelegt werden soll), sowie optional die Attribute für dieses Feld 
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateRadioButtonOfOptionPage($name, $selection, $value, $attributes = null) {         
           // Übergebe diese Aufgabe an eine Schwestermethode
           return $this->generateRadioButtonWithLabelOfOptionPage($name, null, null, $selection, $value, $attributes);
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere eine Radio-Button für die Optionsseite 
             Eingabe: der Name des Feldes, der erläuternde Text zu dieser Option, der Tiptext passend zum erläuternden Text, der Wert
                      (mit dem vorbelegt werden soll), sowie optional die Attribute für dieses Feld 
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateRadioButtonWithLabelOfOptionPage($name, $label, $title, $selection, $value, $attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           $name = ( preg_match('/^[ ]*$/u', $name) <= 0 ? $name : null );
           $selection = ( preg_match('/^[ ]*$/u', $selection) <= 0 ? $selection : null );
           $label = ( preg_match('/^[ ]*$/u', $label) <= 0 ? $label : null );
           $title = ( preg_match('/^[ ]*$/u', $title) <= 0 ? $title : null );
           $value = ( preg_match('/^[a-z0-9% _\.,-]+$/iu', $value) > 0 ? $value : null );
           if (!is_array($attributes)) {
             $htmlID = null; $attributes = null;
            } else {
             $htmlID = ( isset($attributes['id']) ? $attributes['id'] : null );
             foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
            }
           // Stelle den Quellcode zusammen             
           if ($name and $selection) {  
             $code = array();
             $code[] = '<input type="radio" name="' . $name . '" value="' . $selection . '"' . ( $attributes ? implode('', $attributes) : '' ) . ( $value == $selection ? ' checked="checked"' : '' ) . '>';
             if ($label) {
               $code[] = '<label' . ( $htmlID !== null ? ' for="' . $htmlID . '"' : '' ) . ( $title ? ' title="' . $title . '"' : '' ) . '>' . $label . '</label>';
              }
             $result = implode('', $code);
            }  
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */

     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere einen (Haupt)Block für die Dokumentation in der Optionsseite 
             Eingabe: der einzufügende Inhalt, sowie optional die Attribute für die Zeile    
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateBlockOfDokuOnOptionPage($subcode, $attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           $subcode = ( preg_match('/^[ ]*$/u', $subcode) <= 0 ? $subcode : null );
           if (!is_array($attributes)) { $attributes = array(); }
           if (!isset($attributes['class'])) { $attributes['class'] = ''; }
           $attributes['class'] = 'helpText' . ( (preg_match('/^[ ]*$/u', $attributes['class']) <= 0) ? ', ' : '' ) . $attributes['class'];
           foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
           // Stelle den Quellcode zusammen
           if ($subcode) {
             $code = array();
             $code[] = '<div' . ( $attributes ? implode('', $attributes) : '' ) . '>';
             $code[] = $subcode;
             $code[] = '</div>';
             $result = implode('', $code);
            }             
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere eine Textzeile für die Dokumentation in der Optionsseite 
             Eingabe: optional der einzufügende Inhalt, sowie optional die Attribute für die Zeile    
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateTextLineOfDokuOnOptionPage($subcode, $attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           $subcode = ( preg_match('/^[ ]*$/u', $subcode) <= 0 ? $subcode : null );
           if (!is_array($attributes)) { $attributes = array(); }
           foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
           // Stelle den Quellcode zusammen
           if ($subcode) {
             $code = array();
             $code[] = '<p' . ( $attributes ? implode('', $attributes) : '' ) . '>';
             $code[] = $subcode;
             $code[] = '</p>';
             $result = implode('', $code);
            }             
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere eine Passage mit Quellcode für die Dokumentation in der Optionsseite 
             Eingabe: der einzufügende Inhalt, sowie optional die Attribute für die Zeile    
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateCodeOfDokuOnOptionPage($subcode, $attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           $subcode = ( preg_match('/^[ ]*$/u', $subcode) <= 0 ? $subcode : null );
           if (!is_array($attributes)) { $attributes = array(); }
           foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
           // Stelle den Quellcode zusammen
           if ($subcode) {
             $code = array();
             $code[] = '<code' . ( $attributes ? implode('', $attributes) : '' ) . '>';
             $code[] = $subcode;
             $code[] = '</code>';
             $result = implode('', $code);
            }             
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere einen Block mit Quellcode für die Dokumentation in der Optionsseite 
             Eingabe: der einzufügende Inhalt, sowie optional die Attribute für die Zeile    
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateCodeBlockOfDokuOnOptionPage($subcode, $attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           $subcode = ( preg_match('/^[ ]*$/u', $subcode) <= 0 ? $subcode : null );
           if (!is_array($attributes)) { $attributes = array(); }
           if (!isset($attributes['class'])) { $attributes['class'] = ''; }
           $attributes['class'] = 'sourcecodeBlock' . ( (preg_match('/^[ ]*$/u', $attributes['class']) <= 0) ? ', ' : '' ) . $attributes['class'];
           foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
           // Stelle den Quellcode zusammen
           if ($subcode) {
             $code = array();
             $code[] = '<code' . ( $attributes ? implode('', $attributes) : '' ) . '>';
             $code[] = $subcode;
             $code[] = '</code>';
             $result = implode('', $code);
            }
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere eine Erläuterungstabelle für die Dokumentation in der Optionsseite 
             Eingabe: der einzufügende Inhalt der Tabelle, sowie optional die Attribute für die Zeile    
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateCodeExplanationTableOfDokuOnOptionPage($subcode, $attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           $subcode = ( preg_match('/^[ ]*$/u', $subcode) <= 0 ? $subcode : null );
           if (!is_array($attributes)) { $attributes = array(); }
           if (!isset($attributes['class'])) { $attributes['class'] = ''; }
           $attributes['class'] = 'codeExplanation' . ( (preg_match('/^[ ]*$/u', $attributes['class']) <= 0) ? ', ' : '' ) . $attributes['class'];
           foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
           // Stelle den Quellcode zusammen
           if ($subcode) {
             $code = array();
             $code[] = '<table' . ( $attributes ? implode('', $attributes) : '' ) . '>';
             $code[] = '<tr>';
             $code[] = $subcode;
             $code[] = '</tr>';
             $code[] = '</table>';
             $result = implode('', $code);
            }
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere eine neue Zeile in einer Erläuterungstabelle für die Dokumentation in der Optionsseite 
             Eingabe: optional die Attribute für die Zeile    
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateNewTRofCodeExplanationTableOfDokuOnOptionPage($attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           if (!is_array($attributes)) { $attributes = array(); }
           foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
           // Stelle den Quellcode zusammen
           $code = array();
           $code[] = '</tr>';
           $code[] = '<tr' . ( $attributes ? implode('', $attributes) : '' ) . '>';
           $result = implode('', $code);
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere eine Überschriftszelle einer Erläuterungstabelle für die Dokumentation in der Optionsseite 
             Eingabe: der einzufügende Inhalt für die Zelle, sowie optional die Attribute für die Zeile    
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateTHofCodeExplanationTableOfDokuOnOptionPage($subcode, $attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           $subcode = ( preg_match('/^[ ]*$/u', $subcode) <= 0 ? $subcode : null );
           if (!is_array($attributes)) { $attributes = array(); }
           foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
           // Stelle den Quellcode zusammen
           if ($subcode) {
             $code = array();
             $code[] = '<th' . ( $attributes ? implode('', $attributes) : '' ) . '>';
             $code[] = $subcode;
             $code[] = '</th>';
             $result = implode('', $code);
            }
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere eine normale Zelle einer Erläuterungstabelle für die Dokumentation in der Optionsseite 
             Eingabe: der einzufügende Inhalt für die Zelle, sowie optional die Attribute für die Zeile    
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateTDofCodeExplanationTableOfDokuOnOptionPage($subcode, $attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           $subcode = ( preg_match('/^[ ]*$/u', $subcode) <= 0 ? $subcode : null );
           if (!is_array($attributes)) { $attributes = array(); }
           foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
           // Stelle den Quellcode zusammen
           if ($subcode) {
             $code = array();
             $code[] = '<td' . ( $attributes ? implode('', $attributes) : '' ) . '>';
             $code[] = $subcode;
             $code[] = '</td>';
             $result = implode('', $code);
            }
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere eine normale Zelle einer Erläuterungstabelle für die Dokumentation in der Optionsseite 
             Eingabe: der einzufügende Inhalt für die Zelle, sowie optional die Attribute für die Zeile    
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateStandardLineInCodeExplanationTableOfDokuOnOptionPage($subcodeA, $subcodeB1, $subcodeB2, $attributesA = null, $attributesB = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           $subcodeA = ( preg_match('/^[ ]*$/u', $subcodeA) <= 0 ? $subcodeA : null );
           if (!is_array($attributesA)) { $attributesA = array(); }
           if (!isset($attributesA['class'])) { $attributesA['class'] = ''; }
           $attributesA['class'] = 'colA attribute' . ( (preg_match('/^[ ]*$/u', $attributesA['class']) <= 0) ? ', ' : '' ) . $attributesA['class'];
           foreach ($attributesA as $key1 => $value1) { $attributesA[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
           $subcodeB1 = ( preg_match('/^[ ]*$/u', $subcodeB1) <= 0 ? $subcodeB1 : null );
           $subcodeB2 = ( preg_match('/^[ ]*$/u', $subcodeB2) <= 0 ? $subcodeB2 : null );
           if (!is_array($attributesB)) { $attributesB = array(); }
           if (!isset($attributesB['class'])) { $attributesB['class'] = ''; }
           $attributesB['class'] = 'colB info' . ( (preg_match('/^[ ]*$/u', $attributesB['class']) <= 0) ? ', ' : '' ) . $attributesB['class'];
           foreach ($attributesB as $key1 => $value1) { $attributesB[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
           // Stelle den Quellcode zusammen
           if (($subcodeA) and ($subcodeB1)) {
             $code = array();
             $code[] = '</tr>';
             $code[] = '<tr>';
             $code[] = '<td' . ( $attributesA ? implode('', $attributesA) : '' ) . '>';
             $code[] = $subcodeA;
             $code[] = '</td>';
             $code[] = '<td' . ( $attributesB ? implode('', $attributesB) : '' ) . '>';
             $code[] = '<p class="explanation">';
             $code[] = $subcodeB1;
             $code[] = '</p>';
             $code[] = $subcodeB2;
             $code[] = '</td>';
             $result = implode('', $code);
            }
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere die Zeile(n) innerhalb einer Erläuterungstabelle für die Dokumentation in der Optionsseite, welche
                      Auskunft über einen Daten- oder Variablentyp geben. 
             Eingabe: der einzufügende Inhalt bzw. die einzufügenden Zeilen (als Array), sowie optional die Attribute für die Zeile    
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateValueLinesInCodeExplanationTableOfDokuOnOptionPage($lines, $attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           if (!is_array($lines)) { $lines = array($lines); }
           foreach ($lines as $key1 => $value1) {
             $lines[$key1] = ( preg_match('/^[ ]*$/u', $lines[$key1]) <= 0 ? $lines[$key1] : null );
             if ($lines[$key1] === null) { unset($lines[$key1]); }
            }
           if (count($lines) < 1) { $lines = null; } 
           if (!is_array($attributes)) { $attributes = array(); }
           if (!isset($attributes['class'])) { $attributes['class'] = ''; }
           $attributes['class'] = 'values' . ( (preg_match('/^[ ]*$/u', $attributes['class']) <= 0) ? ', ' : '' ) . $attributes['class'];
           foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
           // Stelle den Quellcode zusammen
           if ($lines) {
             $code = array();
             $code[] = '<ul' . ( $attributes ? implode('', $attributes) : '' ) . '>';
             foreach ($lines as $key1 => $value1) {
               $code[] = '<li>' . $value1 . '</li>';
              }
             $code[] = '</ul>';
             $result = implode('', $code);
            }
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere die Zeile(n) innerhalb einer Erläuterungstabelle für die Dokumentation in der Optionsseite, welche
                      Beispielquelltexte darstellen. 
             Eingabe: der einzufügende Inhalt bzw. die einzufügenden Zeilen (als Array), sowie optional die Attribute für die Zeile    
             Ausgabe: der generierte Quellcode (als String)
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function generateExampleLinesInCodeExplanationTableOfDokuOnOptionPage($lines, $attributes = null) {
           // Initialisiere den Rückgabewert
           $result = null;
           // Normiere die übergebenen Parameter
           if (!is_array($lines)) { $lines = array($lines); }
           foreach ($lines as $key1 => $value1) {
             $lines[$key1] = ( preg_match('/^[ ]*$/u', $lines[$key1]) <= 0 ? $lines[$key1] : null );
             if ($lines[$key1] === null) { unset($lines[$key1]); }
            }
           if (count($lines) < 1) { $lines = null; } 
           if (!is_array($attributes)) { $attributes = array(); }
           if (!isset($attributes['class'])) { $attributes['class'] = ''; }
           $attributes['class'] = 'example' . ( (preg_match('/^[ ]*$/u', $attributes['class']) <= 0) ? ', ' : '' ) . $attributes['class'];
           foreach ($attributes as $key1 => $value1) { $attributes[$key1] = ' ' . $key1 . '="' . $value1 . '"'; }
           // Stelle den Quellcode zusammen
           if ($lines) {
             $code = array();
             $code[] = '<p' . ( $attributes ? implode('', $attributes) : '' ) . '>';
             foreach ($lines as $key1 => $value1) {
               $code[] = '<code>' . $value1 . '</code>';
              }
             $code[] = '</p>';
             $result = implode('', $code);
            }
           // Rückgabe des erzeugten Quellcodes
           return $result;
          } /* (end function) */
  
       /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Gebe den HTML-Quellcode für die Basis einer Standard-Optionsseite an den Browser aus
               Eingabe: die darzustellenden Tabs 
               Ausgabe: nichts
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           public function outputDefaultCodeOfOptionPage($tabs) {
             // Nutze für diese Aufgabe eine Schwestermethode
             $code = $this->generateDefaultCodeOfOptionPage($tabs);
             // Führe ggf. die Ausgabe durch
             if (null !== $code) { echo($code); }
            }  /* (end class) */
       /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Stelle den HTML-Quellcode für die Basis einer Standard-Optionsseite bereit
               Eingabe: die darzustellenden Tabs 
               Ausgabe: der erzeugte Quellcode bzw. null als Fehlerwert
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           public function generateDefaultCodeOfOptionPage($tabs) {
             // Initialisiere den Rückgabewert
             $result = null;
             // Normiere die übergebenen Parameter
             $tabs = ( is_array($tabs) ? $tabs : null );
             $page = ( (isset($_REQUEST['page']) and (preg_match('/^[[:print:]]+$/', $_REQUEST['page']) > 0)) ? $_REQUEST['page'] : null );
             // Blockiere eine Darstellung ohne Tabs
             if ((null !== $tabs) and (null !== $page)) {
               $code = array();
               $code[] = sprintf(' <form action="options-general.php?page=%s&rK=%s" method="post">', $page, mt_rand(1000, 9999));
               $code[] = sprintf('  <input type="hidden" name="nonce" value="%s">', wp_create_nonce($this->_pluginName . '_saveOptions'));
               $code[] = '  <ul id="tabNav">';
               $selectedTab = ( isset($_REQUEST['activeTab']) ? $_REQUEST['activeTab'] : null );
               foreach ($tabs as $key1 => $value1) {
                 $code[] = '   <li name="' . $key1 . '"' . ( $selectedTab == $key1 ? ' active="yes"' : '' ) . '><a href="#' . $key1 . '">' . $value1['label']['nav'] . '</a></li>';           
                }
               $code[] = '  </ul>';
               $code[] = '  <div id="tabContent">';
               foreach ($tabs as $key1 => $value1) {
                 $subcodeA = $value1['function']($code);
                 if ($subcodeA) {
                   $code[] = '   <div id="' . $key1 . '" class="tabContent">';
                   $code[] = '    <h3>' . $value1['label']['content'] . '</h3>';
                   $code[] = '    <table class="form">';
                   $code[] = '     <tr>';
                   $code[] = '      <td>';
                   foreach ($subcodeA as $key1 => $value1) { $code[] = '       ' . $value1; }
                   $code[] = '      </td>';
                   $code[] = '     </tr>';
                   $code[] = '    </table>';
                   $code[] = '    <p id="versionKey">' . $this->getVersionKey() . '</p>';
                   $code[] = '   </div>';
                  } 
                }
               $code[] = '  </div>';
               $code[] = '  ' . get_submit_button();
               $code[] = ' </form>';
               $temp = $this->getAuthorHint(); if ($temp !== null) { $code[] = sprintf(' <p id="authorHint">%s</p>', $temp); }            
               $code[] = '</div>';
               $result = implode("\n", $code);
              }
             // Rückgabe des zusammengestellten Quellcodes
             return $result;
            } /* (end function) */
              
       /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Ermittle den standardtisierten Hinweis des Autoren bez. dieses Plugins
               Eingabe: nichts 
               Ausgabe: die ermittelte Hinweistext bzw. null als Fehlerwert
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           protected function getAuthorHint() {
             // Initialisiere den Rückgabewert
             $result = null;
             // Stelle den gesuchten Text zusammen
             $temp = array();
             $temp[] = sprintf($this->__('More information about this and other plugins can be found on the internet at %s%s'), '<a href="https://www.wpjura.de/">www.wpjura.de</a>', '&nbsp;...');              
             $result = implode('', $temp);
             // Rückgabe der ermittelten Textes
             return $result;
            } /* (end function) */  

       /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Bestimme die aktuelle Versionsmarkierung dieses Plugins
               Eingabe: nichts 
               Ausgabe: die ermittelte Markierung bzw. null als Fehlerwert
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           protected function getVersionKey() {
             // Initialisiere den Rückgabewert
             $result = null;
             // Bestimme die aktuelle Versionsnummer
             if (!function_exists('get_plugin_data')) { require_once(ABSPATH . 'wp-admin/includes/plugin.php'); }
             $pluginData = get_plugin_data($this->_pluginPath . $this->_pluginName . '.php', false);
             $result = $this->__('version') . ' ' . $pluginData['Version'];
             // Ergänze die Kennzeichnung für nicht über wordpress.org vertriebene Versionen 
             $result .= ( file_exists($this->_pluginPath . 'lib/class_WPlasuClient.inc.php') ? '(wj)' : '' );
             // Rückgabe der ermittelten Versionsmarkierung
             return $result;
            } /* (end function) */  

        }  /* (end class) */
?>