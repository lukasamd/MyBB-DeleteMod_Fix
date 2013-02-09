<?php
/**
 * This file is part of DeleteMod Fix plugin for MyBB.
 * Copyright (C) 2010-2013 Lukasz Tkacz <lukasamd@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */ 

/**
 * Disallow direct access to this file for security reasons
 * 
 */
if (!defined("IN_MYBB")) exit;

/**
 * Create plugin object
 * 
 */
$plugins->objects['deleteModFix'] = new deleteModFix();

/**
 * Standard MyBB info function
 * 
 */
function deleteModFix_info()
{
    global $lang;

    $lang->load("deleteModFix");
    
    $lang->deleteModFixDesc = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:right;">' .
        '<input type="hidden" name="cmd" value="_s-xclick">' . 
        '<input type="hidden" name="hosted_button_id" value="3BTVZBUG6TMFQ">' .
        '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">' .
        '<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">' .
        '</form>' . $lang->deleteModFixDesc;

    return Array(
        'name' => $lang->deleteModFixName,
        'description' => $lang->deleteModFixDesc,
        'website' => 'http://lukasztkacz.com',
        'author' => 'Lukasz Tkacz',
        'authorsite' => 'http://lukasztkacz.com',
        'version' => '1.1',
        'guid' => 'e2dfc9ee1770650353625a3d395c6f20',
        'compatibility' => '16*'
    );
}

/**
 * Standard MyBB activation functions 
 * 
 */
function deleteModFix_activate()
{
}

function deleteModFix_deactivate()
{
}

/**
 * Plugin Class 
 * 
 */
class deleteModFix
{
    // User group and id
    private $gid = 0;
    private $uid = 0;


    /**
     * Constructor - add plugin hooks
     */
    public function __construct()
    {
        global $plugins;

        $plugins->hooks["admin_forum_management_deleteMod"][10]["deleteModFix_getUserData"] = array("function" => create_function('', 'global $plugins; $plugins->objects[\'deleteModFix\']->getUserData();'));
        $plugins->hooks["admin_forum_management_deleteMod_commit"][10]["deleteModFix_saveUserData"] = array("function" => create_function('', 'global $plugins; $plugins->objects[\'deleteModFix\']->saveUserData();'));
    }

    /**
     * Get user group to memory
     */
    public function getUserData()
    {
        global $db, $mybb;

        $this->uid = (int) $mybb->input['id'];
        $isgroup = (int) $mybb->input['isgroup'];
        
        if ($isgroup)
        {
            return;
        }
        
        $result = $db->simple_select('users', 'usergroup', "uid = '{$this->uid}'");
        $this->gid = (int) $db->fetch_field($result, 'usergroup');
    }

    /**
     * Update user data - fix the problem
     */
    public function saveUserData()
    {
        global $db;
        
        if ($this->gid && $this->uid)
        {
			$updatequery = array('usergroup' => $this->gid);
			$db->update_query('users', $updatequery, "uid = '{$this->uid}'");
        }
    }
}