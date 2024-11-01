<?php
 namespace raonline\raoTableOfContents;

 if (!defined('ABSPATH')) { exit(); }

 if (!class_exists('\raonline\plugin')) { require_once(raonline_raoTableOfContents_pluginPath . '/lib/baseclass_plugin.inc.php'); }

 /* --------------------------------------------------------------------------------------------------------------------------------
         Aufgabe: Klasse zum Bereitstellen von Funktionen zur Textgestaltung
     Bemerkungen: keine
    -------------------------------------------------------------------------------------------------------------------------------- */
     class tableOfContents extends \raonline\plugin {
 
       // Deklaration der Klassenvariablen
       // (Einstellungen)
       private $_TOCposition;
       private $_TOCminLinkCountToShow;
       private $_TOCshowHierachy;
       private $_TOCwidth;
       private $_TOCfloating;
       private $_TOCfontsize;
       private $_TOClayout;
       private $_TOCenumerationType;
       private $_TOCtitleVisible;
       private $_TOCanchorMovementToTop;
       private $_TOCshowOnStartpage;
       private $_TOCshowOnArchivePage;       
       private $_TOCsupportedPostTypes;
       private $_TOCsupportLevelMode;
       private $_TOCsupportedLevels;
       // (Arbeitsvariablen)
       private $_TOClinkList;
             
 
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Initialisiere die Klasse
             Eingabe: nichts
             Ausgabe: nichts
         Bemerkungen: (Konstruktor der Klasse)
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function __construct() {
         
           // Nutze den Konstruktur der Basisklasse
           parent::__construct(raonline_raoTableOfContents_pluginDisplayname, raonline_raoTableOfContents_pluginName, raonline_raoTableOfContents_pluginPath, raonline_raoTableOfContents_pluginURL, raonline_raoTableOfContents_langPath, raonline_raoTableOfContents_langTextDomain, raonline_raoTableOfContents_minWPversion);

           // Initialisiere den Klassenvariablen, welche nicht auf Einstellungen des Plugins basieren
           $this->_className = preg_replace('/^([^\/]+\\\\)*(.*)$/', '$2', get_class($this));
           $this->_TOClinkList = null;
            
           // Aktiviere die Unterstützung der Mehrsprachigkeit
           $this->activateMultiLanguageSupport();

           // Lade die Einstellungen des Plugins 
			     $this->getOptions();
           
           // Unterstütze die Hooks zur Steuerung des Plugins
           if (function_exists('register_activation_hook')) {
              register_activation_hook($this->_pluginPath . $this->_pluginName . '.php', array(&$this, 'activate'));
            }
           if (function_exists('register_deactivation_hook')) {
              register_deactivation_hook($this->_pluginPath . $this->_pluginName . '.php', array(&$this, 'deactivate'));
            }
           if (function_exists('register_install_hook')) {
      				register_install_hook($this->_pluginPath . $this->_pluginName . '.php', 'install');
            }
           if (function_exists('register_uninstall_hook')) {
      				register_uninstall_hook($this->_pluginPath . $this->_pluginName . '.php', 'uninstall');
            }

           // Lade die benötigten Stylesheets und Javascripte
           $this->loadStylesAndScriptsInFrontend();
           $this->loadStylesAndScriptsInBackend();
           
           // Aktiviere die Shortcodes
           $this->activateShortcodes();

           // Aktiviere den Hook zum Ersetzen des Inhalts
           add_action('the_content', array(&$this, 'manipulateTheContent'), 100);
           
           // Registriere die Optionsseite im Backend
           add_action('admin_menu', array(&$this, 'addOptionPage'));
           add_filter('plugin_action_links_' . $this->_pluginSlug, array(&$this, 'addPluginActionLinks'));

          } /* (end constructor) */


     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Initialisiere das Plugin beim Installieren
             Eingabe: nichts
             Ausgabe: nichts
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public static function install() {
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Bereinige das Plugin beim Deinstallieren
             Eingabe: nichts
             Ausgabe: nichts
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public static function uninstall() {
           // Lösche alle Einstellungen dieses Plugins
           $this->deleteAllOptions();
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Initialisiere das Plugin beim Aktivieren
             Eingabe: nichts
             Ausgabe: nichts
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
		      public function activate() {
           } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Bereinige das Plugin beim Deaktivieren
             Eingabe: nichts
             Ausgabe: nichts
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public static function deactivate() {
          } /* (end function) */


     /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Lade die Sprachdateien für die Mehrsprachigkeit über einen Hook
               Eingabe: nichts
               Ausgabe: nichts
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           public function initMultiLanguageSupport() {
             parent::initMultiLanguageSupport();
             // Lade die Einstellungen des Plugins 
             $this->getOptions();                           
            } /* (end function) */

     /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Führe das Laden der relevanten Stylesheets und Skripte in Wordpress durch
               Eingabe: nichts
               Ausgabe: nichts
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           public function doStylesAndScriptsLoadingInFrontend() {
             // Definiere die einbindungsfähigen Dateien
             $possibleFilenames = array();
             $possibleFilenames['raonline_{pluginName}_class_{className}_frontend.css'] = array(
               'type' => 'style',
               'filenames' => array(
                 '{themePath}/css/raonline/{pluginName}/class_{className}.css',
                 '{themePath}/style/raonline/{pluginName}/class_{className}.css',
                 '{themePath}/styles/raonline/{pluginName}/class_{className}.css',
                 '{themePath}/css/raonline_class_{className}.css',
                 '{themePath}/style/raonline_class_{className}.css',
                 '{themePath}/styles/raonline_class_{className}.css',
                 '{pluginPath}class_{className}.theme!{themeName}.css',
                 '{pluginPath}style/frontend_class_{className}.theme!{themeName}.css',
                 '{pluginPath}style/frontend_class_{className}.css',
                 '{pluginPath}style/frontend.css'
                ));
             $possibleFilenames['raonline_{pluginName}_class_{className}_frontend.js'] = array(
               'type' => 'script',
               'filenames' => array(
                 '{themePath}/js/raonline/{pluginName}/class_{className}.js',
                 '{themePath}/script/raonline/{pluginName}/class_{className}.js',
                 '{themePath}/scripts/raonline/{pluginName}/class_{className}.js',
                 '{themePath}/js/raonline_class_{className}.js',
                 '{themePath}/script/raonline_class_{className}.js',
                 '{themePath}/scripts/raonline_class_{className}.js',
                 '{pluginPath}class_{className}.theme!{themeName}.js',
                 '{pluginPath}script/frontend_class_{className}.theme!{themeName}.js',
                 '{pluginPath}script/frontend_class_{className}.js',
                 '{pluginPath}script/frontend.js'
                ));
             // Übergebe die Aufgabe an eine Schwestermethode
             $this->doStylesAndScriptsLoading($possibleFilenames);
            } /* (end function) */         
     /* --------------------------------------------------------------------------------------------------------------------------------
               Aufgabe: Führe das Laden der relevanten Stylesheets und Skripte in Wordpress durch
               Eingabe: nichts
               Ausgabe: nichts
           Bemerkungen: keine
          -------------------------------------------------------------------------------------------------------------------------------- */
           public function doStylesAndScriptsLoadingInBackend() {
             // Definiere die einbindungsfähigen Dateien
             $possibleFilenames = array();
             $possibleFilenames['raonline_{pluginName}_class_{className}_backend.css'] = array(
               'type' => 'style',
               'filenames' => array(
                 '{pluginPath}style/backend_class_{className}.css',
                 '{pluginPath}style/backend.css'
                ));
             $possibleFilenames['raonline_{pluginName}_class_{className}_backend.js'] = array(
               'type' => 'script',
               'filenames' => array(
                 '{pluginPath}script/backend_class_{className}.js',
                 '{pluginPath}script/backend.js'
                ));
             // Übergebe die Aufgabe an eine Schwestermethode
             $this->doStylesAndScriptsLoading($possibleFilenames);
            } /* (end function) */         


     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Lade die Einstellungen des Plugins aus der Datenbank in die Klasse
             Eingabe: nichts
             Ausgabe: nichts
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         private function getOptions() {
           // Lade die Einstellungen aus der Datenbank ...
			     $options = get_option($this->_pluginName);
           $optionsCS = sha1(serialize($options)); if ($options === false) { $options = array(); }
           // ... und initialisiere ggf. die fehlenden Werte
           // -> Position
           if ((!isset($options['TOCposition'])) or (preg_match('/^(beforeContent|afterContent|beforeFirstHeading|afterFirstHeading)$/u', $options['TOCposition']) <= 0)) { $options['TOCposition'] = 'afterFirstHeading'; }
           $this->_TOCposition = $options['TOCposition'];
           // -> minimale Anzahl von notwendigen Links für die Einblendung
           $this->_TOCminLinkCountToShow = 4;
           // -> Hierarchie anzeigen?
           if (!isset($options['TOCshowHierachy'])) { $options['TOCshowHierachy'] = true; }
           $this->_TOCshowHierachy = ( $options['TOCshowHierachy'] === true );
           // -> Darstellungsbreite
           if ((!isset($options['TOCwidth'])) or (preg_match('/^(auto|(100|[1-9][0-9]|[1-9])(\.[0-9]+)?%|[0-9]+(\.[0-9]+)?(r?em|px))$/u', $options['TOCwidth']) <= 0)) { $options['TOCwidth'] = '100%'; }
           $this->_TOCwidth = $options['TOCwidth'];
           // -> Textumbruch
           if ((!isset($options['TOCfloating'])) or (preg_match('/^(none|left|center|right)$/u', $options['TOCfloating']) <= 0)) { $options['TOCfloating'] = 'none'; }
           $this->_TOCfloating = $options['TOCfloating'];
           // -> Schriftgröße
           if ((!isset($options['TOCfontsize'])) or (preg_match('/^(([1-9][0-9]{2}|[1-9][0-9]|[1-9])(\.[0-9]+)?%|[0-9]+(\.[0-9]+)?(pt|r?em))$/u', $options['TOCfontsize']) <= 0)) { $options['TOCfontsize'] = '0.9em'; }
           if (preg_match('/^(100|[1-9][0-9]|[1-9])(\.[0-9]+)?%$/u', $options['TOCfontsize'], $match) > 0) { $options['TOCfontsize'] = (floor($match[1]) / 100) . 'em'; }
           $this->_TOCfontsize = $options['TOCfontsize'];
            // -> Layout
           if ((!isset($options['TOClayout'])) or (preg_match('/^[1-2]$/u', $options['TOClayout']) <= 0)) { $options['TOClayout'] = '1'; }
           $this->_TOClayout = (int)$options['TOClayout'];
           // -> Aufzählungsart (inkl. Einrückung der Aufzählung)
           if ((!isset($options['TOCenumerationType'])) or (preg_match('/^[0-2]$/u', $options['TOCenumerationType']) <= 0)) { $options['TOCenumerationType'] = 2; }
           $this->_TOCenumerationType = (int)$options['TOCenumerationType'];
           // -> Überschrift / Unterüberschrift
           if (!isset($options['TOCtitleVisible'])) { $options['TOCtitleVisible'] = true; }
           $this->_TOCtitleVisible = ( $options['TOCtitleVisible'] === true );
           // -> Verschiebung der Anker nach oben
           if ((!isset($options['TOCanchorMovementToTop'])) or (preg_match('/^[0-9]+(\.[0-9]+)?(pt|r?em)$/u', $options['TOCanchorMovementToTop']) <= 0)) { $options['TOCanchorMovementToTop'] = '2em'; }
           $this->_TOCanchorMovementToTop = $options['TOCanchorMovementToTop'];
           // -> Sichtbar auf der Startseite
           if (!isset($options['TOCshowOnStartpage'])) { $options['TOCshowOnStartpage'] = false; }
           $this->_TOCshowOnStartpage = ( $options['TOCshowOnStartpage'] !== false );
           // -> Sichtbar im Archiv
           if (!isset($options['TOCshowOnArchivePage'])) { $options['TOCshowOnArchivePage'] = false; }
           $this->_TOCshowOnArchivePage = ( $options['TOCshowOnArchivePage'] !== false );
           // -> unterstützte Post-Types
           $temp = array(); $k = 0;
           $availablePostTypes = self::getSupportedPostTypes();
           if (isset($options['TOCsupportedPostTypes'])) {
             foreach ($availablePostTypes as $key1 => $value1) {
               if (!isset($options['TOCsupportedPostTypes'][$value1])) { $temp[$value1] = false; } else { $temp[$value1] = ( $options['TOCsupportedPostTypes'][$value1] === true ); $k++;  }
              }
            }
           if ($k < 1) {
             foreach ($availablePostTypes as $key1 => $value1) { $temp[$value1] = true; }
            }
           $this->_TOCsupportedPostTypes = $options['TOCsupportedPostTypes'] = $temp;
           // -> unterstützte Level
           if ((!isset($options['TOCsupportLevelMode'])) or (preg_match('/^(classic|gutenberg)$/u', $options['TOCsupportLevelMode']) <= 0)) { $options['TOCsupportLevelMode'] = 'gutenberg'; }
           $this->_TOCsupportLevelMode = $options['TOCsupportLevelMode'];
           if (preg_match('/^(gutenberg)$/u', $this->_TOCsupportLevelMode)) {
             $this->_TOCsupportedLevels = array(1 => 2, 2 => 3, 3 => 4, 4 => null, 5 => null, 6 => null);
            } else {
             $this->_TOCsupportedLevels = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => null, 6 => null);
            }
           // Speichere ggf. die Änderungen an den Einstellungen
           if ($optionsCS !== sha1(serialize($options))) { $this->setOptions(); }           
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Speichere die Einstellungen des Plugins aus der Klasse in die Datenbank
             Eingabe: nichts
             Ausgabe: nichts
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         private function setOptions() {
           // Stelle die Einstellungen anhand der Klassenvariablen zusammen
echo('<pre style="font-weight: normal; color: #008800;">'); var_dump($this->_TOCsupportLevelMode); echo('</pre>');
           $options = array(
             'TOCposition' => $this->_TOCposition,
             'TOCminLinkCountToShow' => $this->_TOCminLinkCountToShow,
             'TOCshowHierachy' => ( $this->_TOCshowHierachy === true ),
             'TOCwidth' => $this->_TOCwidth,
             'TOCfloating' => $this->_TOCfloating,
             'TOCfontsize' => $this->_TOCfontsize,
             'TOClayout' => $this->_TOClayout,
             'TOCenumerationType' => $this->_TOCenumerationType,
             'TOCtitleVisible' => ( $this->_TOCtitleVisible === true ),
             'TOCanchorMovementToTop' => $this->_TOCanchorMovementToTop,
             'TOCshowOnStartpage' => ( $this->_TOCshowOnStartpage !== false ),
             'TOCshowOnArchivePage' => ( $this->_TOCshowOnArchivePage !== false ),
             'TOCsupportedPostTypes' => $this->_TOCsupportedPostTypes,
             'TOCsupportLevelMode' => $this->_TOCsupportLevelMode,
             'TOCsupportedLevels' => $this->_TOCsupportedLevels,
            );
           // Aktualisiere die Einstellungen in der Datenbank
			     return update_option($this->_pluginName, $options, '', 'yes');
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Lösche die Einstellungen des Plugins aus der Datenbank
             Eingabe: nichts
             Ausgabe: nichts
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         private function deleteAllOptions() {
			     // Lösche die hinterlegten Einstellungen
           delete_option($this->_pluginName);
          } /* (end function) */


   /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Ermittle die unterstützten Post-Typen
             Eingabe: nichts
             Ausgabe: ein Array mit den gefundenen Post-Typen
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         private static function getSupportedPostTypes() {
			     // Stelle die Liste zusammen
           $result = array();
           $temp = get_post_types(array('public' => true));
           foreach ($temp as $key1 => $value1) {
             if (preg_match('/^(attachment|revision|nav_menu_item|safecss)$/iu', $key1) <= 0) { $result[] = $key1; }
            } 
           // Gebe die Liste zurück
           return $result;
          } /* (end function) */
          
          
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Ermittle die Liste der unterstützten Shortcodes
             Eingabe: nichts
             Ausgabe: ein assoziatives Array mit den gewünschten Informationen
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function getShortcodeList($selection = false) {
           // Initialisiere den Rückgabewert
           $result = array();
           // Definiere die unterstützten Shortcodes
           if (($selection === false) or (preg_match('/^TOC$/iu', $selection) > 0)) {
             $keyListA = array('TOC', 'ToC', 'toc', 'tableofcontent', 'TableOfContent', 'Inhaltsverzeichnis');
             $keyListC = array('no', 'No', 'kein', 'Kein');
             $keyList = array(); foreach ($keyListA as $key1 => $value1) { foreach ($keyListC as $key2 => $value2) { $keyList[] = $value2 . $value1; } }
             $result['noTOC'] = array('keyList' => implode('|', $keyList), 'function' => array(&$this, 'generateShortcodeOutput_noTOC'));
            }
           // Rückgabe der erzeugten Liste
           return $result;  
          } /* (end function) */

     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere den Quellcode für einen unterstützten Shortcode
             Eingabe: die angegebenen Attribute als assoziatives Array, sowie der angegebene Inhalt, sowie der Name des verwendeten
                      Shortcodes
             Ausgabe: der generierte Quellcode
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public static function generateShortcodeOutput_noTOC($attributes, $content, $name) {
           // Initialisiere den Rückgabewert
           $result = '<!--noTOC-->';
           // Rückgabe des generierten Quellcodes
           return $result;
          } /* (end function) */


     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Passe den Quellcode des Inhaltes so an, dass ggf. das Inhaltsverzeichnis zu sehen ist
             Eingabe: der übergebene Quellcode des Inhaltes 
             Ausgabe: der manipulierte Quellcode
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function manipulateTheContent($content) {
           // Initialisiere den Rückgabewert
           $result = $content;           
           // Prüfe, ob das Inhaltsverzeichnis zu unterdrücken ist ...
           $showTOC = ( preg_match('/<!--noTOC-->/u', $result) > 0 ? false : true );
           if ($showTOC) {
             global $post;
             if ((isset($this->_TOCsupportedPostTypes[$post->post_type])) and ($this->_TOCsupportedPostTypes[$post->post_type] !== true)) { $showTOC = false; }
            }
           if (($showTOC) and (is_front_page()) and (!$this->_TOCshowOnStartpage)) {
             $showTOC = false;
            }
           if (($showTOC) and (is_archive()) and (!$this->_TOCshowOnArchivePage)) {
             $showTOC = false;
            }
           // Füge das Inhaltsverzeichnis nur ein, wenn es nicht blockiert ist
           if ($showTOC) {
             $result = $this->prepareContent($result);
             if ((!isset($this->_TOClinkList['linkStructure'])) or (count($this->_TOClinkList['linkStructure']) < $this->_TOCminLinkCountToShow)) { $showTOC = false; }
             if ($showTOC) {
               // Erzeuge den Quellcode des Inhaltsverzeichnisses
               $codeTOC = $this->buildTOCcode($result);
               // Ermittle die Position des Inhaltsverzeichnisses
               $posTOC = null;
               if (preg_match('/^(.*?)<!--TOC-->/su', $result, $match) > 0) {
                 $posTOC = strlen($match[1]);
                }
               if ($posTOC === null) {
                 $posTOC = 'onBegin';
                 if (preg_match('/^beforeContent$/iu', $this->_TOCposition) > 0) {
                   $posTOC = 'onBegin';
                  } elseif (preg_match('/^afterContent$/iu', $this->_TOCposition) > 0) {
                   $posTOC = 'onEnd';
                  } elseif (preg_match('/^beforeFirstHeading$/iu', $this->_TOCposition) > 0) {
                   if (preg_match('/^(.*?)<h([1-6])[^>]*>/msu', $result, $match) > 0) {
                     $posTOC = strlen($match[1]);
                    }
                  } elseif (preg_match('/^afterFirstHeading$/iu', $this->_TOCposition) > 0) {
                   if (preg_match('/^(.*?<h([1-6])[^>]*>.*?<\/h\2>)/msu', $result, $match) > 0) {
                     $posTOC = strlen($match[1]);
                    }
                  }            
                } 
               if ($posTOC == 'onEnd') {
                 $result = $result . $codeTOC;
                } elseif ($posTOC == 'onBegin') {            
                 $result = $codeTOC . $result;
                } else { 
                 $result = substr($result, 0, $posTOC) . $codeTOC . substr($result, $posTOC);           
                }
              }  
            }
           // Lösche, die nicht mehr benötigten Markierungen
           $result = preg_replace('/<!--(no)?TOC-->/', '', $result);           
           // Rückgabe des generierten Quellcodes
           return $result;
          } /* (end function) */

     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Bereite den Quellcode für die Verwendung des Inhaltsverzeichnisses vor und generiere nebenbei die Liste der
                      Links für das Inhaltsverzeichnis 
             Eingabe: der auszuwertende Quellcode aus dem (Wordpress-)Post 
             Ausgabe: der angepasste Quellcode
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function prepareContent($content) {
           // Initialisiere den Rückgabewert
           $result = $content;           
           // Stelle die Liste der unterstützten Zwischenüberschriften zusammen
           $this->_TOClinkList = array('htmlIDs' => null, 'linkStructure' => null);
           $result = preg_replace_callback('/(<h([1-6])[^>]*>)(.*?)(<\/h\2>)/msu', function($match) {
             // Initialisiere die angepassten Überschrift
             $replacement = $match[0];
             // Blockiere nicht unterstützte Überschriften
             if (in_array($match[2], $this->_TOCsupportedLevels)) {
               // Bestimme das Level dieser Überschrift im Inhaltsverzeichnis
               $level = null;
               foreach ($this->_TOCsupportedLevels as $key1 => $value1) {
                 if ($value1 == $match[2]) { $level = $key1; break; }
                }
               // Stelle die benötigte HTML-ID bereit
               if (!is_array($this->_TOClinkList['htmlIDs'])) { $this->_TOClinkList['htmlIDs']  = array(); }
               $htmlID = self::normHMTLidentifier($match[3]);
               $k = -1;
               do {
                 $k++; $temp = $htmlID . ( $k > 0 ? $k : '' );
                 if ($k > 10000) { $temp = null; break; }
                } while (in_array($temp, $this->_TOClinkList['htmlIDs']));
               if ($temp === null) {
                 $htmlID = null;
                } else {
                 $htmlID = $temp; $this->_TOClinkList['htmlIDs'][] = $htmlID;
                }
               // Ergänze die Zwischenüberschrift zum Inhaltsverzeichnis
               if (!is_array($this->_TOClinkList['linkStructure'])) { $this->_TOClinkList['linkStructure'] = array(); }
               $this->_TOClinkList['linkStructure'][] = array('htmlID' => $htmlID, 'label' => $match[3], 'level' => $level);
               // Erweitere die Überschrift, um sie mit einem Anker anspringen zu können
               if ($htmlID !== null) {
                 $replacement = '<span class="raoTOCheading"><span id="' . $htmlID . '" class="raoTOCanchor" style="top: -' . $this->_TOCanchorMovementToTop . ';">&nbsp;</span>' . $match[3] . '</span>';
                 $replacement = $match[1] . $replacement . $match[4];
                }
              }
             // Rückgabe der angepassten Überschrift
             return $replacement;  
            }, $result);
           // Rückgabe des Vorbereiteten Quellcodes
           return $result; 
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Generiere den Quellcode für das Inhaltsverzeichnis selbst 
             Eingabe: nichts 
             Ausgabe: der erzeugte Quellcode für das Inhaltsverzeichnis
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function buildTOCcode() {
           // Initialisiere den Rückgabewert
           $result = null;
           // Stelle die Darstellung des Inhaltsverzeichnisses zusammen
           if ((isset($this->_TOClinkList['linkStructure'])) and ($this->_TOClinkList['linkStructure'])) {
             // Ermittle das maximal verwendete Level
             if ($this->_TOCshowHierachy) {
               $maxLevel = 0; foreach ($this->_TOClinkList['linkStructure'] as $key1 => $value1) { $maxLevel = max($maxLevel, $value1['level']); }
              } else {
               $maxLevel = 1;
              }
             // Trage die Links zu einem Code zusammen
             $code = array();
             $level = 0; $counting = array();
             foreach ($this->_TOClinkList['linkStructure'] as $key1 => $value1) {
               if ($this->_TOCshowHierachy) {
                 if ($level < $value1['level']) {
                   for ($i = $level; $i < $value1['level']; $i++) { $level++; $code[] = ( $level > 1 ? '<li class="nextLevel">' : '' ) . '<ul level="' . $level . '" step="' . ($maxLevel - $level) . '">'; }
                  } elseif ($level > $value1['level']) {
                   for ($i = $level; $i > $value1['level']; $i--) { $level--; $code[] = '</ul>' . ( $level > 1 ? '</li>' : '' ); }
                  }
                 if ($level > 1) {
                   for ($i = 1; $i < $level; $i++) {
                     if (!isset($counting[$i])) { $counting[$i] = 1; }
                    }
                  }
                 if (!isset($counting[$level])) { $counting[$level] = 0; }
                 $counting[$level]++;
                 for ($i = $level + 1; $i <= 6; $i++) { $counting[$i] = 0; }
                } else {
                 if ($level < 1) { $level=1; $code[] = '<ul>'; }
                 if (!isset($counting[1])) { $counting[1] = 0; }
                 $counting[1]++;
                }
               if ($this->_TOCenumerationType < 1) {
                 $temp = '';
                } else { 
                 $temp = array();
                 for ($i = 1; $i <= $level; $i++) {
                   if (isset($counting[$i])) {
                     switch ($this->_TOCenumerationType) {
                       case 1:
                         $temp[] = '<span class="level">' . sprintf('%01d' . ( $i == $level ? '' : '.' ), $counting[$i]) . '</span>';
                         break;
                       case 2:
                         $temp[] = '<span class="level">' . sprintf('%01d.', $counting[$i]) . '</span>';
                         break;
                      }
                    } else {
                     break;
                    }
                  }
                 $temp = '<span class="enum">' . trim(implode('', $temp)) . '</span>';
                }                
               $code[] = '<li><a href="#' . $value1['htmlID'] . '">' . $temp . $value1['label'] . '</a></li>'; 
              }
             if ($level > 0) {
               for ($i = $level; $i > 0; $i--) { $level--; $code[] = '</ul>' . ( $level > 1 ? '</li>' : '' ); }
              }   
             // Ergänze die Überschrift (ggf. inkl. Unterüberschrift) ...
             $title = array('main' => null, 'sub' => null);             
             if ($this->_TOCtitleVisible) {
               $title['main'] = $this->__('Table of Contents');
              }
             $subcodeA = ( $title['main'] !== null ? '<p class="title"><span class="maintitle">' . $title['main'] . '</span></p>' : '&nbsp;' );   
             $code = '<div class="header" hasTitle="' . ( $title['main'] !== null ? 'yes' : 'no' ) . '">' . $subcodeA . '</div>' . implode('', $code);
             // ... und den passenden Rahmen
             $CSSclasses = array();
             if (preg_match('/^(left|center|right)$/iu', $this->_TOCfloating) > 0) { $CSSclasses[] = 'float' . ucfirst($this->_TOCfloating); }
             $CSSstyles = array();
             if (preg_match('/^100%$/u', $this->_TOCfloating) <= 0) { $CSSstyles['width'] = $this->_TOCwidth; }
             if (preg_match('/^(100%|1em)$/u', $this->_TOCfontsize) <= 0) { $CSSstyles['font-size'] = $this->_TOCfontsize; }
             foreach ($CSSstyles as $key1 => $value1) { $CSSstyles[$key1] = $key1 . ': ' . $value1 . ';'; }             
             $options = '';
             $options .= ( count($CSSclasses) > 0 ? ' class="' . implode(' ', $CSSclasses) . '"' : '' );
             $options .= ( count($CSSstyles) > 0 ? ' style="' . implode(' ', $CSSstyles) . '"' : '' );             
             $code = '<div id="raoTOC" layout="' . $this->_TOClayout . '"' .  $options  . '>' . $code . '</div>';
             $result = $code;
            }
           // Rückgabe des generierten Quellcodes
           return $result;
          } /* (end function) */

     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Filter zum Ergänzen der Links zum Plugin auf der Plugin-Seite
             Eingabe: die Liste der bisher bestehenden Links
             Ausgabe: die ergänzte Liste
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function addPluginActionLinks($links) {
           // Ergänze die Liste der Links
           array_unshift($links, '<a href="' . admin_url( 'options-general.php?page=raonline_tableOfContents') . '">' . $this->__('Settings') . '</a>');
           // Rückgabe der ggf. manipulierten Liste
           return $links;
          } /* (end function) */

     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Füge die Optionseite der Admin-Oberfläche hinzu 
             Eingabe: nichts 
             Ausgabe: nichts
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function addOptionPage() {
           // Ergänze den Untermenüpunkt für das Plugin
           $menuKey = self::automoveMySubmenus();           
           add_submenu_page($menuKey, '(ra-online) Table Of Contents', $this->__('Table Of Contents'), 'manage_options', 'raonline_tableOfContents', array(&$this, 'handleOptionPage') );  
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Validiere die vom Formular an die Optionseite übergebenen Werte 
             Eingabe: nichts 
             Ausgabe: das Ergebnis der Prüfung als assoziatives Array
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function validateUpdatesOnOptionPage() {
           // Initialisiere den Rückgabewert
           $result = array('changed' => false, 'validateError' => array(), 'validateWarning' => array());
           // Prüfe die einzelnen Felder ...
           $changed =& $result['changed']; $validateError =& $result['validateError']; $validateWarning =& $result['validateWarning'];
           // -> Position
           $tempA = ( ((!isset($_REQUEST['TOCposition'])) or (preg_match('/^(beforeContent|afterContent|beforeFirstHeading|afterFirstHeading)$/u', $_REQUEST['TOCposition']) <= 0)) ? null : $_REQUEST['TOCposition'] );
           if (($tempA !== null) and ($this->_TOCposition !== $tempA)) { $changed = true; $this->_TOCposition = $tempA; }
           // -> Hierarchie anzeigen?
           $tempA = ( (!isset($_REQUEST['TOCshowHierachy'])) ? false : (preg_match('/^on$/u', $_REQUEST['TOCshowHierachy']) > 0) );
           if (($tempA !== null) and ($this->_TOCshowHierachy !== $tempA)) { $changed = true; $this->_TOCshowHierachy = $tempA; }
           // -> Darstellungsbreite
           if ((isset($_REQUEST['TOCpreselectedWidth'])) and (preg_match('/^custom$/u', $_REQUEST['TOCpreselectedWidth']) <= 0)) {
             $tempA = $_REQUEST['TOCpreselectedWidth'];
            } elseif ((isset($_REQUEST['TOCcustomWidthValue'])) and (preg_match('/^[ ]*$/u', $_REQUEST['TOCcustomWidthValue']) <= 0) and (isset($_REQUEST['TOCcustomWidthUnit'])) and (preg_match('/^[ ]*$/u', $_REQUEST['TOCcustomWidthUnit']) <= 0)) {
             $tempA = str_replace(',', '.', $_REQUEST['TOCcustomWidthValue']) . $_REQUEST['TOCcustomWidthUnit'];
            } elseif ((isset($_REQUEST['TOCcustomWidthValue'])) and (preg_match('/^[ ]*$/u', $_REQUEST['TOCcustomWidthValue']) > 0)) {
             $validateError[] = $this->__('No value was specified for the maximum width! The input was thus rejected!'); 
            } else {
             $tempA = null;
            }
           $tempA = ( (($tempA === null) or (preg_match('/^(auto|(100|[1-9][0-9]|[1-9])(\.[0-9]+)?%|[0-9]+([,\.][0-9]+)?(r?em|px))$/u', $tempA) <= 0)) ? null : $tempA ); 
           if (($tempA !== null) and ($this->_TOCwidth !== $tempA)) { $changed = true; $this->_TOCwidth = $tempA; }            
           // -> Textumbruch
           $tempA = ( ((!isset($_REQUEST['TOCfloating'])) or (preg_match('/^(none|left|center|right)$/u', $_REQUEST['TOCfloating']) <= 0)) ? null : $_REQUEST['TOCfloating'] );
           if (($tempA !== null) and ($this->_TOCfloating !== $tempA)) { $changed = true; $this->_TOCfloating = $tempA; }            
           // -> Schriftgröße
           if ((isset($_REQUEST['TOCpreselectedFontSize'])) and (preg_match('/^custom$/u', $_REQUEST['TOCpreselectedFontSize']) <= 0)) {
             $tempA = $_REQUEST['TOCpreselectedFontSize'];
            } elseif ((isset($_REQUEST['TOCcustomFontSizeValue'])) and (preg_match('/^[ ]*$/u', $_REQUEST['TOCcustomFontSizeValue']) <= 0) and (isset($_REQUEST['TOCcustomFontSizeUnit'])) and (preg_match('/^[ ]*$/u', $_REQUEST['TOCcustomFontSizeUnit']) <= 0)) {
             $tempA = str_replace(',', '.', $_REQUEST['TOCcustomFontSizeValue']) . $_REQUEST['TOCcustomFontSizeUnit'];
            } elseif ((isset($_REQUEST['TOCcustomFontSizeValue'])) and (preg_match('/^[ ]*$/u', $_REQUEST['TOCcustomFontSizeValue']) > 0)) {
             $validateError[] = $this->__('No value was specified for the maximum width! The input was thus rejected!'); 
            } else {
             $tempA = null;
            }
           $tempA = ( (($tempA !== null) and (preg_match('/^([1-9][0-9]{2}|[1-9][0-9]|[1-9])(\.[0-9]+)?%$/u', $tempA, $match) > 0)) ? (floor($match[1]) / 100) . 'em' : $tempA );
           $tempA = ( (($tempA === null) or (preg_match('/^(auto|([1-9][0-9]{2}|[1-9][0-9]|[1-9])(\.[0-9]+)?%|[0-9]+([,\.][0-9]+)?(r?em|px))$/u', $tempA) <= 0)) ? null : $tempA ); 
           if (($tempA !== null) and ($this->_TOCfontsize !== $tempA)) { $changed = true; $this->_TOCfontsize = $tempA; }
            // -> Layout
           $tempA = ( ((!isset($_REQUEST['TOClayout'])) or (preg_match('/^[1-2]$/u', $_REQUEST['TOClayout']) <= 0)) ? null : (int)$_REQUEST['TOClayout'] );
           if (($tempA !== null) and ($this->_TOClayout !== $tempA)) { $changed = true; $this->_TOClayout = $tempA; }            
           // -> Aufzählungsart (inkl. Einrückung der Aufzählung)
           $tempA = ( ((!isset($_REQUEST['TOCenumerationType'])) or (preg_match('/^[0-2]$/u', $_REQUEST['TOCenumerationType']) <= 0)) ? null : (int)$_REQUEST['TOCenumerationType'] );
           if (($tempA !== null) and ($this->_TOCenumerationType !== $tempA)) { $changed = true; $this->_TOCenumerationType = $tempA; }            
           // -> Überschrift / Unterüberschrift
           $tempA = ( (!isset($_REQUEST['TOCtitleVisible'])) ? false : (preg_match('/^on$/u', $_REQUEST['TOCtitleVisible']) > 0) );
           if (($tempA !== null) and ($this->_TOCtitleVisible !== $tempA)) { $changed = true; $this->_TOCtitleVisible = $tempA; }
           // -> Verschiebung der Anker nach oben
           if ((isset($_REQUEST['TOCanchorMovementToTopValue'])) and (preg_match('/^[ ]*$/u', $_REQUEST['TOCanchorMovementToTopValue']) <= 0) and (isset($_REQUEST['TOCanchorMovementToTopUnit'])) and (preg_match('/^[ ]*$/u', $_REQUEST['TOCanchorMovementToTopUnit']) <= 0)) {
             $tempA = str_replace(',', '.', $_REQUEST['TOCanchorMovementToTopValue']) . $_REQUEST['TOCanchorMovementToTopUnit'];
            } elseif ((isset($_REQUEST['TOCanchorMovementToTopValue'])) and (preg_match('/^[ ]*$/u', $_REQUEST['TOCanchorMovementToTopValue']) > 0)) {
             $validateError[] = $this->__('No value was specified for the maximum width! The input was thus rejected!'); 
            } else {
             $tempA = null;
            }
           $tempA = ( (($tempA === null) or (preg_match('/^[0-9]+([,\.][0-9]+)?(r?em|px)$/u', $tempA) <= 0)) ? null : $tempA ); 
           if (($tempA !== null) and ($this->_TOCanchorMovementToTop !== $tempA)) { $changed = true; $this->_TOCanchorMovementToTop = $tempA; }
           // -> Sichtbar auf der Startseite
           $tempA = ( (!isset($_REQUEST['TOCshowOnStartpage'])) ? false : (preg_match('/^on$/u', $_REQUEST['TOCshowOnStartpage']) > 0) );
           if (($tempA !== null) and ($this->_TOCshowOnStartpage !== $tempA)) { $changed = true; $this->_TOCshowOnStartpage = $tempA; }
           // -> Sichtbar im Archiv
           $tempA = ( (!isset($_REQUEST['TOCshowOnArchivePage'])) ? false : (preg_match('/^on$/u', $_REQUEST['TOCshowOnArchivePage']) > 0) );
           if (($tempA !== null) and ($this->_TOCshowOnArchivePage !== $tempA)) { $changed = true; $this->_TOCshowOnArchivePage = $tempA; }
           // -> unterstützte Post-Types
           $availablePostTypes = self::getSupportedPostTypes();
           $tempA = array(); $k = 0;
           foreach ($availablePostTypes as $key1 => $value1) {
             if ((isset($_REQUEST['TOCsupportPostType_' . $value1])) and (preg_match('/^on$/u', $_REQUEST['TOCsupportPostType_' . $value1]) > 0)) {
               $k++; $tempA[$value1] = true;
              } else {
               $tempA[$value1] = false;
              }
            }
           if (($tempA !== null) and ($this->_TOCsupportedPostTypes !== $tempA)) { $changed = true; $this->_TOCsupportedPostTypes = $tempA; } 
           // -> unterstützte Level
           if (isset($_REQUEST['TOCsupportLevelMode']) and (preg_match('/^(classic|gutenberg)$/iu', $_REQUEST['TOCsupportLevelMode']) > 0)) {
             $tempA = strtolower($_REQUEST['TOCsupportLevelMode']);
            } else {
             $tempA = null;
            }
           if (($tempA !== null) and ($this->_TOCsupportLevelMode !== $tempA)) { $changed = true; $this->_TOCsupportLevelMode = $tempA; }
           // Rückgabe der ermittelten Meldungen
           return $result;            
          } /* (end function) */
     /* --------------------------------------------------------------------------------------------------------------------------------
             Aufgabe: Stelle den Quellcode der Optionseite zusammen und gebe ihn an den Browser aus 
             Eingabe: nichts 
             Ausgabe: nichts
         Bemerkungen: keine
        -------------------------------------------------------------------------------------------------------------------------------- */
         public function handleOptionPage() {         
           // Gebe den Anfang des Quellcodes aus ...
           $code = array();
           $code[] = '<div id="content_raonline_tableOfContents">';
           $code[] = '<h2>' . $this->__('backend_label') . '</h2>';
           // Versuche die Einstellungen zu speichern
           if ((isset($_REQUEST['submit'])) and (isset($_REQUEST['nonce'])) and (wp_verify_nonce($_REQUEST['nonce'], $this->_pluginName . '_saveOptions'))) {
             // Validiere die vom Formular übergebenen Werte
             $status = $this->validateUpdatesOnOptionPage();
             // Versuche die Daten zu speichern und gebe die entsprechenden Meldungen aus ...
             if (count($status['validateError']) > 0) {
               foreach ($status['validateError'] as $key1 => $value1) {
                 $code[] = '<div class="notice notice-error"><p>' . $value1 . '</p></div>';
                }
              }
             if (count($status['validateWarning']) > 0) {
               foreach ($status['validateWarning'] as $key1 => $value1) {
                 $code[] = '<div class="notice notice-warning"><p>' . $value1 . '</p></div>';
                }
              }
             if ($status['changed'] === true) {
               if ($this->setOptions()) {
                 $code[] = '<div class="notice notice-success"><p>' . $this->__('Save settings successfully!') . '</p></div>';
                } else {
                 $code[] = '<div class="notice notice-error"><p>' . $this->__('Save settings failed!') . '</p></div>';
                }
              } else {
/*               $code[] = '<div class="notice notice-success"><p>' . $this->__('Save settings successfully!') . '</p></div>';*/
               $code[] = '<div class="notice notice-warning"><p>' . $this->__('settings do not need to be saved!') . '</p></div>';
              }
            }         
           echo(implode("\n", $code));
           // Gebe den Rest des Quellcodes aus ...
           $tabs = array();
           $tabs['visibility'] = array('label' => array('nav' => $this->__('visibility'), 'content' => $this->__('visibility of the contents directory')), 'function' => function() {
             $code = array();
             // -> minimale Anzahl von notwendigen Links für die Einblendung
             $subcodeA = sprintf('<span class="fixValue">%s</span>', $this->_TOCminLinkCountToShow);
             $code[] = $this->generateLineOfOptionPage('A', $this->__('necessary number of links'), $this->__('Only if the content directory contains at least as many links, it is displayed.'), $subcodeA);
             // -> Sichtbar auf der Startseite
             $subcodeA = $this->generateCheckBoxWithLabelOfOptionPage('TOCshowOnStartpage', $this->__('try to include a table of contents on the home page.'), $this->__('if you enable this option, try to embed a table of contents on the home page.'), $this->_TOCshowOnStartpage, array('id' => 'TOCshowOnStartpage'));
             $code[] = $this->generateLineOfOptionPage('B', $this->__('also on the homepage'), $this->__('By default, no content directory is embedded on the start page, since the start page is usually a linked overview and not a pure text.'), $subcodeA);
             // -> Sichtbar im Archiv
             $subcodeA = $this->generateCheckBoxWithLabelOfOptionPage('TOCshowOnArchivePage', $this->__('try to include a table of contents on archive pages.'), $this->__('if you enable this option, try to embed a table of contents on archive pages.'), $this->_TOCshowOnArchivePage, array('id' => 'TOCshowOnArchivePage'));
             $code[] = $this->generateLineOfOptionPage('B', $this->__('also on archive pages'), $this->__('By default, no content directory is embedded on the archive pages, since this does not make sense on an overview page.'), $subcodeA);
             // -> unterstützte Post-Types
             $subcodeA = '';
             $availablePostTypes = self::getSupportedPostTypes();
             $i = 0;
             foreach ($availablePostTypes as $key1 => $value1) {
               $i++;
               if ($i > 1) { $subcodeA .= '<br>'; }
               $subcodeA .= $this->generateCheckBoxWithLabelOfOptionPage('TOCsupportPostType_' . $value1, $value1, sprintf($this->__('If you enable this option, the inclusion of a content directory for the page type (post type) %s is basically permitted.'), '&bdquo;' . $value1 . '&ldquo;'), $this->_TOCsupportedPostTypes[$value1] === true, array('id' => 'TOCsupportPostType_' . $value1));
              }
             $code[] = $this->generateLineOfOptionPage('B', $this->__('supported pagetypes'), $this->__('almost all the bottoms of a wordpress website come from various types of posts.'), $subcodeA);
             return $code;
            });
           $tabs['presentation'] = array('label' => array('nav' => $this->__('presentation'), 'content' => $this->__('presentation of the table of contents')), 'function' => function() {
             $code = array();
             // -> Position
             $options = array();
             $options['beforeContent'] = $this->__('at the beginning of the text');
             $options['afterContent'] = $this->__('at the end of the text');
             $options['beforeFirstHeading'] = $this->__('before the first heading');
             $options['afterFirstHeading'] = $this->__('after the first heading');             
             $subcodeA = $this->generateSelectFieldOfOptionPage('TOCposition', $this->_TOCposition, $options);
             $code[] = $this->generateLineOfOptionPage('A', $this->__('position in the text'), $this->__('Where in the text should the table of contents be represented by default?'), $subcodeA);
             // -> Darstellungsbreite
             $options = array();
             $suboptions = array();
             for ($i = 150; $i <= 500; $i += 25) { $suboptions[$i . 'px'] = $i . 'px'; }
             $options[] = array('label' => $this->__('fix width'), 'options' => $suboptions);
             $suboptions = array();
             $suboptions['default'] = $this->__('automatic') . ' (' . $this->__('default') . ')';
             for ($i = 30; $i <= 100; $i += 5) { $suboptions[$i . '%'] = $i . '%'; }
             $options[] = array('label' => $this->__('relative width'), 'options' => $suboptions);
             $suboptions = array();
             $suboptions['custom'] = $this->__('custom');
             $options[] = array('label' => $this->__('other'), 'options' => $suboptions);
             $TOCpreselectedWidth = 'custom';
             foreach ($options as $key1 => $value1) {
               foreach ($value1['options'] as $key2 => $value2) {
                 if ($key2 === $this->_TOCwidth) { $TOCpreselectedWidth = $this->_TOCwidth; break; }
                }
              }
             $TOCcustomWidth = array('value' => null, 'unit' => null);
             if (preg_match('/^custom$/iu', $TOCpreselectedWidth) > 0) {
               if (preg_match('/(?<value>[0-9]+(\.[0-9]+)?)(?<unit>(%|r?em|px))$/iu', $this->_TOCwidth, $match) > 0) {
                 $TOCcustomWidth = array('value' => str_replace('.', ',', $match['value']), 'unit' => $match['unit']);
                }
              }
             $subcodeA = $this->generateSelectFieldOfOptionPage('TOCpreselectedWidth', $TOCpreselectedWidth, $options);
             $subcodeA .= '<div dependent="TOCpreselectedWidth:custom">';
             $subcodeA .= $this->generateTextFieldOfOptionPage('TOCcustomWidthValue', $TOCcustomWidth['value'], array('class' => 'vA'));
             $options = array('px' => 'px', '%' => '%', 'em' => 'em', 'rem' => 'rem');
             $subcodeA .= $this->generateSelectFieldOfOptionPage('TOCcustomWidthUnit', $TOCcustomWidth['unit'], $options);
             $subcodeA .= '</div>';
             $code[] = $this->generateLineOfOptionPage('A', $this->__('maximum width'), $this->__('How wide can the table of contents be?'), $subcodeA);
             // -> Textumbruch
             $options = array();
             $options['none'] = $this->__('text is interrupted and table of contents has full width');
             $options['center'] = $this->__('text is interrupted and content directory is centered');             
             $options['left'] = $this->__('text flows to the right');
             $options['right'] = $this->__('text flows to the left');
             $subcodeA = $this->generateSelectFieldOfOptionPage('TOCfloating', $this->_TOCfloating, $options);
             $code[] = $this->generateLineOfOptionPage('A', $this->__('text wrapping'), $this->__('How should the text be wrapped around the table of contents?'), $subcodeA);
             // -> Layout
             $options = array();
             for ($i = 1; $i <= 2; $i++) { $options[$i] = sprintf($this->__('Layout %s'), $i); } 
             $subcodeA = $this->generateSelectFieldOfOptionPage('TOClayout', $this->_TOClayout, $options, array('image' => $this->_pluginURL . 'img/layout{value}.png'));
             $code[] = $this->generateLineOfOptionPage('B', $this->__('Layout'), $this->__('we offer several layouts for the presentation of the contents directory.'), $subcodeA);
             // -> Schriftgröße
             $options = array();
             $suboptions = array();
             for ($i = 2; $i > 1; $i -= 0.5) { $suboptions[$i . 'em'] = ($i * 100) . '%'; }
             for ($i = 1; $i > 0.3; $i -= 0.05) { $suboptions[$i . 'em'] = ($i * 100) . '%'; }
             $options[] = array('label' => $this->__('percentage'), 'options' => $suboptions);
             $suboptions = array();
             $suboptions['custom'] = $this->__('custom');
             $options[] = array('label' => $this->__('other'), 'options' => $suboptions);
             $TOCpreselectedFontSize = 'custom';
             foreach ($options as $key1 => $value1) {
               foreach ($value1['options'] as $key2 => $value2) {
                 if ($key2 === $this->_TOCfontsize) { $TOCpreselectedFontSize = $this->_TOCfontsize; break; }
                }
              }
             $TOCcustomFontSize = array('value' => null, 'unit' => null);
               if (preg_match('/(?<value>[0-9]+(\.[0-9]+)?)(?<unit>(pt|%|r?em))$/iu', $this->_TOCfontsize, $match) > 0) {
                 $TOCcustomFontSize = array('value' => str_replace('.', ',', $match['value']), 'unit' => $match['unit']);
                }
             $subcodeA = $this->generateSelectFieldOfOptionPage('TOCpreselectedFontSize', $TOCpreselectedFontSize, $options);
             $subcodeA .= '<div dependent="TOCpreselectedFontSize:custom">';
             $subcodeA .= $this->generateTextFieldOfOptionPage('TOCcustomFontSizeValue', $TOCcustomFontSize['value'], array('class' => 'vA'));
             $options = array('pt' => 'pt', '%' => '%', 'em' => 'em', 'rem' => 'rem');
             $subcodeA .= $this->generateSelectFieldOfOptionPage('TOCcustomFontSizeUnit', $TOCcustomFontSize['unit'], $options);
             $subcodeA .= '</div>';
             $code[] = $this->generateLineOfOptionPage('A', $this->__('maximum fontsize'), $this->__('Select a font size as a basis for the table of contents!'), $subcodeA);
             // -> Überschrift / Unterüberschrift
             $subcodeA = $this->generateCheckBoxWithLabelOfOptionPage('TOCtitleVisible', $this->__('insert the heading!'), $this->__('if you enable this option, a header is displayed above the left.'), $this->_TOCtitleVisible, array('id' => 'TOCtitleVisible'));
             $code[] = $this->generateLineOfOptionPage('B', $this->__('heading'), $this->__('before the links appear in the table of contents, a header can be displayed.'), $subcodeA);
             // -> Verschiebung der Anker nach oben
             $TOCanchorMovementToTop = array('value' => null, 'unit' => null);
             if (preg_match('/(?<value>[0-9]+(\.[0-9]+)?)(?<unit>(pt|%|r?em))$/iu', $this->_TOCanchorMovementToTop, $match) > 0) {
               $TOCanchorMovementToTop = array('value' => str_replace('.', ',', $match['value']), 'unit' => $match['unit']);
              }
             $subcodeA = $this->generateTextFieldOfOptionPage('TOCanchorMovementToTopValue', $TOCanchorMovementToTop['value'], array('class' => 'vB'));
             $options = array('pt' => 'pt', 'em' => 'em', 'rem' => 'rem');
             $subcodeA .= $this->generateSelectFieldOfOptionPage('TOCanchorMovementToTopUnit', $TOCanchorMovementToTop['unit'], $options, array('class' => 'vB'));
             $code[] = $this->generateLineOfOptionPage('B', $this->__('displacement of the anchor'), $this->__('specify how far the jump targets are to be moved towards the headings. this may be necessary to see the headline even when the beams are at the upper edge.'), $subcodeA);
             return $code;
            });
           $tabs['hierarchy'] = array('label' => array('nav' => $this->__('hierarchy'), 'content' => $this->__('Hierarchy in the table of contents')), 'function' => function() {
             $code = array();
             // -> unterstützte Level
             $subcodeA = '';
             $options = array('classic' => $this->__('Use the first four hierarchy levels') . ' (H1 bis H4)', 'gutenberg' => $this->__('Use the first four levels of the hierarchy, but ignore the first one') . ' (H2 bis H4)');
             $subcodeA .= $this->generateSelectFieldOfOptionPage('TOCsupportLevelMode', $this->_TOCsupportLevelMode, $options);
             $code[] = $this->generateLineOfOptionPage('A', $this->__('supported heading types'), $this->__('The headings are subdivided into 6 levels of hierarchy, which determine whether one heading is equivalent, or superior or subordinate, to another.') . "\n" . $this->__('This plugin may take advantage of the first four levels of the table of contents, ignoring the first by default, as it should occur only once per page.'), $subcodeA);
             // -> Hierarchie anzeigen?
             $subcodeA = $this->generateCheckBoxWithLabelOfOptionPage('TOCshowHierachy', $this->__('Visualize the hierarchy visually through different font sizes and distances.'), $this->__('when you enable this option, the hierarchy of the headings is highlighted by indentation and spacing.'), $this->_TOCshowHierachy, array('id' => 'TOCshowHierachy'));
             $code[] = $this->generateLineOfOptionPage('B', $this->__('show hierarchy'), $this->__('Should the hierarchy of the links be displayed in the table of contents?'), $subcodeA);
             // -> Aufzählungsart (inkl. Einrückung der Aufzählung)
             $options = array();
             $options[0] = $this->__('no enumeration');
             $options[1] = sprintf($this->__('listing according to the pattern %s'), '&bdquo;1.1&ldquo;');
             $options[2] = sprintf($this->__('listing according to the pattern %s'), '&bdquo;1.1.&ldquo;');
             $subcodeA = $this->generateSelectFieldOfOptionPage('TOCenumerationType', $this->_TOCenumerationType, $options);
             $code[] = $this->generateLineOfOptionPage('B', $this->__('numbered enumeration'), $this->__('the individual links to a table of contents can be converted into a numbered list. the hierachie is also taken into account when it is displayed optically.'), $subcodeA);
             return $code;
            });
           $tabs['individualControl'] = array('label' => array('nav' => $this->__('individual control'), 'content' => $this->__('individual control of the contents directory')), 'function' => function() {
             $code = array();
             $subcodeA = array();
             $subcodeB = $this->generateCodeBlockOfDokuOnOptionPage('[noTOC]');
             $subcodeA[] = $this->generateTextLineOfDokuOnOptionPage(sprintf($this->__('If you want to completely avoid on a page that is embedded in an iv, place it at any point in the content of the page following shortcode: %s'), $subcodeB));
             $code[] = $this->generateBlockOfDokuOnOptionPage(implode('', $subcodeA));
             return $code;
            });
           $code = array();
           $code[] = ' <form action="options-general.php?page=' . $_REQUEST['page'] . '&x=' . mt_rand(1000, 9999) . '" method="post">';
           $code[] = '  <input type="hidden" name="nonce" value="' . wp_create_nonce($this->_pluginName . '_saveOptions') . '">';
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
           $temp = $this->getAuthorHint(); if ($temp !== null) { $code[] = ' <p id="authorHint">' . $temp . '</p>'; }            
           $code[] = '</div>';
           echo(implode("\n", $code));
          } /* (end function) */
  
      }  /* (end class) */
?>