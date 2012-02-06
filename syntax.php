    <?php
    /**
     * RDP Link Plugin
     *
     * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
     * @author     Justin Shepard <jshepard@cyberdynamic.net>
     */
     
    if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
    if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
    require_once(DOKU_PLUGIN.'syntax.php');
     
    class syntax_plugin_rdplink extends DokuWiki_Syntax_Plugin {
     
        function getInfo(){
            return array(
                'author' => 'Justin Shepard',
                'email'  => 'jshepard@cyberdynamic.net',
                'date'   => '2007-08-01',
                'name'   => 'RDP Link Plugin',
                'desc'   => 'Embed links to auto-generate and open RDP connection files.',
                'url'    => 'http://www.dokuwiki.org/plugin:rdplink',
            );
        }
        function getType(){ return 'substition'; }
        function getSort(){ return 999; }
        function connectTo($mode) { $this->Lexer->addSpecialPattern('{rdplink:.+?}',$mode,'plugin_rdplink'); }
        function handle($match, $state, $pos, &$handler){
            switch ($state) {
              case DOKU_LEXER_SPECIAL :
                $match = substr($match,9,-1);
                $smeta = explode('|',$match,2);
                if(empty($smeta[1])) $smeta[1] = $smeta[0];
                return array(
                    'server'=>$smeta[0],
                    'desc'=>$smeta[1]
                );
                break;
            }
            return array();
        }
     
        function render($mode, &$renderer, $data) {
            if($mode == 'xhtml'){
    //            $renderer->doc .= "<a href=\"" . DOKU_URL . "lib/plugins/rdplink/rdp.php?server=" . $data['server'] . "\"><img src=\"".DOKU_URL."lib/plugins/rdplink/rdpicon.png\" />" . $data['desc'] . "</a>";
                $renderer->doc .= "<img src=\"".DOKU_URL."lib/plugins/rdplink/rdpicon.png\" />&nbsp;<a href=\"" . DOKU_URL . "lib/plugins/rdplink/rdp.php?server=" . $data['server'] . "\">" . $data['desc'] . "</a>";
                return true;
            }
            return false;
        }
    }

