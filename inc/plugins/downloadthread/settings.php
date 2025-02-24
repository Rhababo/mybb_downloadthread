<?php

function downloadthread_settings_install()
{
    global $db;

    $res = $db->simple_select('settinggroups', 'MAX(disporder) as max_disporder');
    $disporder = $db->fetch_field($res, 'max_disporder') + 1;
    $fields = array(
        'name'         => 'downloadthread_settings',
        'title'        => 'Download Thread',
        'description'  => 'Settings for the Download Thread plugin.',
        'disporder'    => intval($disporder),
        'isdefault'    => 0
    );
    $gid = $db->insert_query('settinggroups', $fields);

    $settings = array(
        'forums' => array(
            'title'       => 'Enabled Forums',
            'description' => 'Select the forums from which threads may be downloaded.',
            'optionscode' => 'forumselect',
            'value'       => '-1',
            'disporder' => 1
        ),
        'groups' => array(
            'title'       => 'Enabled Groups',
            'description' => 'Select the groups which may download threads.',
            'optionscode' => 'groupselect',
            'value'       => '-1',
            'disporder' => 2
        ),
        'button_location' => array(
            'title' => 'Button Location',
            'description' => 'Where should the download button be located?',
            'optionscode' => 'radio\n0=Bottom of page\n1=Top and Bottom of page',
            'value' => 1,
            'disporder' => 3
        ),

    );

    $ordernum = 1;
    foreach ($settings as $name => $setting) {
        $insert_settings = array(
            'name'        => $db->escape_string('downloadthread_'.$name),
            'title'       => $db->escape_string($setting['title']),
            'description' => $db->escape_string($setting['description']),
            'optionscode' => (string)$setting['optionscode'],
            'value'       => $db->escape_string($setting['value']),
            'disporder'   => $ordernum,
            'gid'         => $gid,
            'isdefault'   => 0
        );
        $db->insert_query('settings', $insert_settings);
        $ordernum++;
    }
    rebuild_settings();
    
}

function downloadthread_settings_update()
{

}

function downloadthread_settings_uninstall()
{
    global $db;

    $needs_rebuild = false;
    $res = $db->simple_select('settinggroups', 'gid', "name = 'downloadthread_settings'");
    

    while (($gid = $db->fetch_field($res, 'gid')))
    {
        $db->delete_query('settinggroups', "gid='{$gid}'");
        $db->delete_query('settings', "gid='{$gid}'");
        $needs_rebuild = true;
    }

    rebuild_settings();

}
