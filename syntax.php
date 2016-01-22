    <?php
    /**
     * RDP Link Plugin
     *
     * @license    MIT (http://www.opensource.org/licenses/mit-license.php)
		 * @author     Justin Shepard <jshepard@cyberdynamic.net>
		 *
		 * Permission is hereby granted, free of charge, to any person obtaining
		 * a copy of this software and associated documentation files (the 
		 * "Software"), to deal in the Software without restriction, including
		 * without limitation the rights to use, copy, modify, merge, publish,
		 * distribute, sublicense, and/or sell copies of the Software, and to
		 * permit persons to whom the Software is furnished to do so, subject
		 * to the following conditions:
		 *
		 * The above copyright notice and this permission notice shall be
		 * included in all copies or substantial portions of the Software.
		 *
		 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
		 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
		 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
		 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
		 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
		 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
		 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
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
        function handle($match, $state, $pos, Doku_Handler $handler){
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
     
        function render($mode, Doku_Renderer $renderer, $data) {
            if($mode == 'xhtml'){
    //            $renderer->doc .= "<a href=\"" . DOKU_URL . "lib/plugins/rdplink/rdp.php?server=" . $data['server'] . "\"><img src=\"".DOKU_URL."lib/plugins/rdplink/rdpicon.png\" />" . $data['desc'] . "</a>";
                $renderer->doc .= "<img src=\"".DOKU_URL."lib/plugins/rdplink/rdpicon.png\" />&nbsp;<a href=\"" . DOKU_URL . "lib/plugins/rdplink/rdp.php?server=" . $data['server'] . "\">" . $data['desc'] . "</a>";
                return true;
            }
            return false;
        }
    }

