<?php

// This file keeps track of upgrades to
// the plagiarism Turnitin module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

/**
 * @global moodle_database $DB
 * @param int $oldversion
 * @return bool
 */
function xmldb_plagiarism_turnitin_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    if ($oldversion < 2011041200) {
        $table = new xmldb_table('turnitin_files');
        $field = new xmldb_field('attempt', XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'similarityscore');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2011041200, 'plagiarism','turnitin');
    }

    if ($oldversion < 2011083100) {

        // Define field apimd5 to be added to turnitin_files
        $table = new xmldb_table('turnitin_files');
        $field = new xmldb_field('apimd5', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'attempt');

        // Conditionally launch add field apimd5
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // turnitin savepoint reached
        upgrade_plugin_savepoint(true, 2011083100, 'plagiarism', 'turnitin');
    }
    if ($oldversion < 2011083102) {
        if (!$DB->record_exists('user_info_field', array('shortname'=>'turnitinteachercoursecache'))) {
            //first insert category
            $newcat = new stdClass();
            $newcat->name = 'plagiarism_turnitin';
            $newcat->sortorder = 999;
            $catid = $DB->insert_record('user_info_category', $newcat);
            //now insert field
            $newfield = new stdClass();
            $newfield->shortname = 'turnitinteachercoursecache';
            $newfield->name = get_string('userprofileteachercache','plagiarism_turnitin');
            $newfield->description = get_string('userprofileteachercache_desc','plagiarism_turnitin');
            $newfield->datatype = 'text';
            $newfield->descriptionformat = 1;
            $newfield->categoryid = $catid;
            $newfield->sortorder = 1;
            $newfield->required = 0;
            $newfield->locked = 1;
            $newfield->visible = 0;
            $newfield->forceunique = 0;
            $newfield->signup = 0;
            $newfield->param1 = 30;
            $newfield->param2 = 5000;

            $DB->insert_record('user_info_field', $newfield);
        }
    }
    return true;
}