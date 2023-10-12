<?php

function downloadthread_templates_install()
{
    global $db;
    $template_json = json_decode(file_get_contents(__DIR__."/templates.json", false), true);
    foreach ($template_json as $key)
    {
        $my_template[] = array(
            "title" => $db->escape_string($key['title']),
            "template" => $db->escape_string($key['template']),
            "sid" => -1,
            "version" => '1824',
            "dateline" => TIME_NOW
        );

        $my_new_template[] = array(
                "title" => $db->escape_string($key['title']),
                "template" => $db->escape_string($key['template']),
                "sid" => -2,
                "version" => "1824",
                "dateline" => TIME_NOW
            );

    }
    // Now that that theme is done, insert all templates for the global theme.
    $db->insert_query_multiple("templates", $my_template);
    $db->insert_query_multiple("templates", $my_new_template);

    // Template Changes

    require_once MYBB_ROOT.'/inc/adminfunctions_templates.php';
    find_replace_templatesets('showthread', '({\\$search_thread})', "{\$search_thread}\t\t\t{\$downloadthread}");
}

function downloadthread_templates_update()
{
    global $mybb;

    require_once MYBB_ROOT.'/inc/adminfunctions_templates.php';
    //remove previous template position
    find_replace_templatesets('showthread', '(\\r?\\t\\t\\t{\\$downloadthread})', '', 0);

    //0 = bottom of page
    //1 = top and bottom of page
    if($mybb->settings['downloadthread_button_location']=="0")
    {
        find_replace_templatesets('showthread', '({\\$search_thread})', "{\$search_thread}\t\t\t{\$downloadthread}");
    }
    else
    {
        find_replace_templatesets('showthread', '({\\$addpoll})', "{\$addpoll}\t\t\t{\$downloadthread}");
    }
}

function downloadthread_templates_uninstall()
{
    global $db;

    // Revert Template changes.
    require_once MYBB_ROOT.'/inc/adminfunctions_templates.php';
    find_replace_templatesets('showthread', '(\\r?\\t\\t\\t{\\$downloadthread})', '', 0);
    $template_json = json_decode(file_get_contents(__DIR__."/templates.json", false), true);
    $comma = "";
    $delete_string = "";
    foreach ($template_json as $key)
    {
        $delete_string .= $comma . "'" . $db->escape_string($key['title']) . "'";
        $comma = ",";
    }
    $db->delete_query("templates", "title IN(" . $delete_string . ")");
}
