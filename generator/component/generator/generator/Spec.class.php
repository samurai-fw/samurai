<?php
/**
 * PHP version 5.
 *
 * Copyright (c) 2007-2010, Samurai Framework Project, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 *     * Neither the name of the Samurai Framework Project nor the names of its
 *       contributors may be used to endorse or promote products derived from this
 *       software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @link       http://samurai-fw.org/
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    SVN: $Id: $
 */

Samurai_Loader::loadByClass('Generator');

/**
 * SpecGenerator
 * 
 * @package    Samurai
 * @subpackage Generator
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Generator_Generator_Spec extends Generator
{
    /**
     * specスケルトン名
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_SPEC = 'spec/spec.skeleton.php';

    /**
     * initファイルスケルトン名
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_INIT = 'spec/initialization.skeleton.php';


    /**
     * @implements
     */
    public function generate($spec_name, $skeleton, $params = array())
    {
        /*
        list($class_name, $action_file) = $this->ActionChain->makeNames($action_name);
        //ファイルネームのローカライズ
        $action_file = Samurai_Config::get('generator.directory.samurai') . DS . $action_file;
        //ジェネレイト
        $params['class_name'] = $class_name;
        $result = $this->_generate($action_file, $skeleton, $params);
        return array($result, $action_file);
         */
    }


    /**
     * Initialization用のgenerateメソッド
     *
     * @access     public
     * @param      string  $init_file   Actionファイル
     * @param      string  $skeleton    スケルトン名
     * @param      array   $params      Rendererに渡される値
     * @return     array   結果
     */
    public function generate4Init($init_file, $skeleton, $params = array())
    {
        $result = $this->_generate($init_file, $skeleton, $params);
        return array($result, $init_file);
    }


    /**
     * スケルトンの取得
     *
     * @access     public
     * @param      string  $filename   スケルトン名
     * @return     string  スケルトン名
     */
    public function getSkeleton($filename = NULL)
    {
        if(!$filename) $filename = $this->SKELETON_SPEC;
        return parent::getSkeleton($filename);
    }
}

