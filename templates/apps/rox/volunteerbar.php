<?php
/*

Copyright (c) 2007 BeVolunteer

This file is part of BW Rox.

BW Rox is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

BW Rox is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/> or 
write to the Free Software Foundation, Inc., 59 Temple Place - Suite 330, 
Boston, MA  02111-1307, USA.

*/
/**
 * 
 * @author fvanhove@gmx.de, Andreas (lemon-head)
 * @see /htdocs/bw/layout/menus.php method VolMenu
 * 
 * Some of these admin tools allow severe violations of member privacy.
 * We should take care of that as soon as possible!
 */
    $R = MOD_right::get();
    $res = "";
    $request = PRequest::get()->request;
    $link = ""; // FIXME: all link checks should be transfered to be "rox style"
    if (count($request) > 1) {
        $link = $request[0] . '/' . $request[1];
    }
    $words = new MOD_words();

    echo
        '<h3>'.$words->getFormatted("VolunteerAction").'</h3>'.
        '<ul class="linklist">'
    ;
	 echo
		'<a href="volunteer">'. $words->get("Volunteerpage") . '</a></strong></li>';
	
    $array_of_items =
        array(
            array(
                'Words',
                'bw/admin/adminwords.php',
                'AdminWord',
                'Words management'
            ),
            array(
                'Accepter',
                'bw/admin/adminaccepter.php',
                'AdminAccepter('.$numberPersonsToBeAccepted.')',
                'accept new member accounts'
            ),
            array(
                'Accepter',
                'bw/admin/adminmandatory.php',
                'AdminMandatory('.$numberPersonsToBeChecked.')',
                'check member accounts'
            ),
            array(
                'Grep',
                'bw/admin/admingrep.php',
                'AdminGrep',
                'grep files'
            ),
            array(
                'Group',
                'bw/admin/admingroups.php',
                'AdminGroups',
                'manage groups'
            ),
            array(
                'Flags',
                'bw/admin/adminflags.php',
                'AdminFlags',
                'administrate member flags'
            ),
            array(
                'Rights',
                'bw/admin/adminrights.php',
                'AdminRights',
                'manage admin rights of other members'
            ),
            array(
                'Logs',
                'bw/admin/adminlogs.php',
                'AdminLogs',
                'logs of member activity'
            ),
            array(
                'Comments',
                'bw/admin/admincomments.php',
                'AdminComments',
                'manage comments'
            ),
            array(
                'Pannel',
                'bw/admin/adminpanel.php',
                'AdminPanel',
                'managing panel'
            ),
            array(
                'Checker',
                'bw/admin/adminchecker.php',
                'AdminChecker ('.$numberMessagesToBeChecked.'/'.$numberSpamToBeChecked.')',
                'check messages for spam'
            ),
            array(
                'Debug',
                PVars::getObj('env')->baseuri . 'bw/admin/phplog.php?showerror=10',
                'php error log',
                'Show last 10 php errors in log'
            ),
            array(
                'MassMail',
                'bw/admin/adminmassmails.php',
                'mass mails',
                'broadcast messages'
            )
        )
    ;
    foreach($array_of_items as $item) {
        if ($R->hasRight($item[0])) {
            if ($link == $item[1]) {
                echo '<li><strong>'.$item[2].'</strong></li>
                ';
            } else {
                echo '<li><a href="'.$item[1].'" title="'.$item[3].'">'.$item[2].'</a></li>
                ';
            }
        }
    }
?>
</ul>




