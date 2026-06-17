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

if (! defined('DOKU_INC')) {
    define('DOKU_INC', realpath(dirname(__FILE__) . '/../../') . '/');
}

if (! defined('DOKU_PLUGIN')) {
    define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
}

require_once DOKU_PLUGIN . 'syntax.php';

class syntax_plugin_rdplink extends DokuWiki_Syntax_Plugin
{

    public function getInfo()
    {
        return [
            'author' => 'Justin Shepard',
            'email'  => 'jshepard@cyberdynamic.net',
            'date'   => '2007-08-01',
            'name'   => 'RDP Link Plugin',
            'desc'   => 'Embed links to auto-generate and open RDP connection files.',
            'url'    => 'http://www.dokuwiki.org/plugin:rdplink',
        ];
    }
    public function getType() { return 'substition'; }
    public function getSort() { return 999; }

public function connectTo($mode)
    {
        $this->Lexer->addSpecialPattern('{rdplink:[^}]+}', $mode, 'plugin_rdplink');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        switch ($state) {
            case DOKU_LEXER_SPECIAL:
                $match = substr($match, 9, -1);
                $smeta = explode('|', $match, 2);
                
                if (!preg_match('/^[A-Za-z0-9.\-]+(:\d+)?$/', $smeta[0])) {
                    msg('rdplink: invalid server format', -1);
                    return [];
                }
                
                if (empty($smeta[1])) {
                    $smeta[1] = $smeta[0];
                }
                    
                return [
                    'server' => $smeta[0],
                    'desc' => $smeta[1]
                ];
        }
        return [];
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode == 'xhtml') {
            $url = DOKU_BASE . "lib/plugins/rdplink/rdp.php?" . buildURLparams(['server' => $data['server']]);
            $renderer->doc .= "<img src=\"" . DOKU_BASE . "lib/plugins/rdplink/rdpicon.png\" alt=\"RDP Icon\" />&nbsp;"
                . "<a href=\"" . $url . "\">" . hsc($data['desc']) . "</a>";
            return true;
        }
        return false;
    }}
