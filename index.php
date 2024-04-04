<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Moodle frontpage.
 *
 * @package    core
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!file_exists('./config.php')) {
    header('Location: install.php');
    die;
}

require_once('config.php');
// require_once($CFG->dirroot .'/course/lib.php');
// require_once($CFG->libdir .'/filelib.php');

require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/course/lib.php');


redirect_if_major_upgrade_required();
require_login();

$hassiteconfig = has_capability('moodle/site:config', context_system::instance());
if ($hassiteconfig && moodle_needs_upgrading()) {
    redirect(new moodle_url('/admin/index.php'));
}

$context = context_system::instance();

// Get the My Moodle page info.  Should always return something unless the database is broken.
if (!$currentpage = my_get_page(null, MY_PAGE_PUBLIC, MY_PAGE_COURSES)) {
    throw new Exception('mymoodlesetup');
}



// $urlparams = array();
// if (!empty($CFG->defaulthomepage) &&
//         ($CFG->defaulthomepage == HOMEPAGE_MY || $CFG->defaulthomepage == HOMEPAGE_MYCOURSES) &&
//         optional_param('redirect', 1, PARAM_BOOL) === 0
// ) {
//     $urlparams['redirect'] = 0;
// }

// $context = context_system::instance();


// Start setting up the page.
$PAGE->set_context($context);
$PAGE->set_url('/');
$PAGE->add_body_classes(['limitedwidth']);
$PAGE->set_pagelayout('frontpage');
$PAGE->set_pagetype('my-index');
$PAGE->blocks->add_region('content');
$PAGE->set_subpage($currentpage->id);
$PAGE->set_title(get_string('mycourses'));
$PAGE->set_heading(get_string('mycourses'));

// $PAGE->add_body_class('limitedwidth');
// $PAGE->set_other_editing_capability('moodle/course:update');
// $PAGE->set_other_editing_capability('moodle/course:manageactivities');
// $PAGE->set_other_editing_capability('moodle/course:activityvisibility');

$PAGE->force_lock_all_blocks();

// Force the add block out of the default area.
// $PAGE->theme->addblockposition  = BLOCK_ADDBLOCK_POSITION_CUSTOM;

// Prevent caching of this page to stop confusion when changing page after making AJAX changes.
// $PAGE->set_cacheable(false);

// require_course_login($SITE);

// $hasmaintenanceaccess = has_capability('moodle/site:maintenanceaccess', context_system::instance());

// If the site is currently under maintenance, then print a message.
// if (!empty($CFG->maintenance_enabled) and !$hasmaintenanceaccess) {
//     print_maintenance_message();
// }

// $hassiteconfig = has_capability('moodle/site:config', context_system::instance());

// if ($hassiteconfig && moodle_needs_upgrading()) {
//     redirect($CFG->wwwroot .'/'. $CFG->admin .'/index.php');
// }

// If site registration needs updating, redirect.
// \core\hub\registration::registration_reminder('/index.php');

// if (get_home_page() != HOMEPAGE_SITE) {
//     // Redirect logged-in users to My Moodle overview if required.
//     $redirect = optional_param('redirect', 1, PARAM_BOOL);
//     if (optional_param('setdefaulthome', false, PARAM_BOOL)) {
//         set_user_preference('user_home_page_preference', HOMEPAGE_SITE);
//     } else if (!empty($CFG->defaulthomepage) && ($CFG->defaulthomepage == HOMEPAGE_MY) && $redirect === 1) {
//         // At this point, dashboard is enabled so we don't need to check for it (otherwise, get_home_page() won't return it).
//         redirect($CFG->wwwroot .'/my/');
//     } else if (!empty($CFG->defaulthomepage) && ($CFG->defaulthomepage == HOMEPAGE_MYCOURSES) && $redirect === 1) {
//         redirect($CFG->wwwroot .'/my/courses.php');
//     } else if (!empty($CFG->defaulthomepage) && ($CFG->defaulthomepage == HOMEPAGE_USER)) {
//         $frontpagenode = $PAGE->settingsnav->find('frontpage', null);
//         if ($frontpagenode) {
//             $frontpagenode->add(
//                 get_string('makethismyhome'),
//                 new moodle_url('/', array('setdefaulthome' => true)),
//                 navigation_node::TYPE_SETTING);
//         } else {
//             $frontpagenode = $PAGE->settingsnav->add(get_string('frontpagesettings'), null, navigation_node::TYPE_SETTING, null);
//             $frontpagenode->force_open();
//             $frontpagenode->add(get_string('makethismyhome'),
//                 new moodle_url('/', array('setdefaulthome' => true)),
//                 navigation_node::TYPE_SETTING);
//         }
//     }
// }

// Trigger event.
course_view(context_course::instance(SITEID));

// $PAGE->set_pagetype('site-index');
// $PAGE->set_docs_path('');
// $editing = $PAGE->user_is_editing();
// $PAGE->blocks->add_region('content');
// $PAGE->set_subpage($currentpage->id);
// $PAGE->set_title(get_string('mycourses'));
// // $PAGE->set_heading($SITE->fullname);
// // $PAGE->set_secondary_active_tab('coursehome');
// $PAGE->set_heading(get_string('mycourses'));
// $PAGE->force_lock_all_blocks();

// Add course management if the user has the capabilities for it.
$coursecat = core_course_category::user_top();
$coursemanagemenu = [];
if ($coursecat && ($category = core_course_category::get_nearest_editable_subcategory($coursecat, ['create']))) {
    // The user has the capability to create course.
    $coursemanagemenu['newcourseurl'] = new moodle_url('/edit.php', ['category' => $category->id]);
}
if ($coursecat && ($category = core_course_category::get_nearest_editable_subcategory($coursecat, ['manage']))) {
    // The user has the capability to manage the course category.
    $coursemanagemenu['manageurl'] = new moodle_url('/management.php', ['categoryid' => $category->id]);
}
if ($coursecat) {
    $category = core_course_category::get_nearest_editable_subcategory($coursecat, ['moodle/course:request']);
    if ($category && $category->can_request_course()) {
        $coursemanagemenu['courserequesturl'] = new moodle_url('/request.php', ['categoryid' => $category->id]);

    }
}

if (!empty($coursemanagemenu)) {
    // Render the course management menu.
    $PAGE->add_header_action($OUTPUT->render_from_template('my/dropdown', $coursemanagemenu));
}




// $courserenderer = $PAGE->get_renderer('core', 'course');

// if ($hassiteconfig) {
//     $editurl = new moodle_url('/course/view.php', ['id' => SITEID, 'sesskey' => sesskey()]);
//     $editbutton = $OUTPUT->edit_button($editurl);
//     $PAGE->set_button($editbutton);
// }

echo $OUTPUT->header();


// $siteformatoptions = course_get_format($SITE)->get_format_options();
// $modinfo = get_fast_modinfo($SITE);
// $modnamesused = $modinfo->get_used_module_names();

// Print Section or custom info.
// if (!empty($CFG->customfrontpageinclude)) {
//     // Pre-fill some variables that custom front page might use.
//     $modnames = get_module_types_names();
//     $modnamesplural = get_module_types_names(true);
//     $mods = $modinfo->get_cms();

//     include($CFG->customfrontpageinclude);

// } else if ($siteformatoptions['numsections'] > 0) {
//     echo $courserenderer->frontpage_section1();
// }
// Include course AJAX.
// include_course_ajax($SITE, $modnamesused);

// echo $courserenderer->frontpage();

// if ($editing && has_capability('moodle/course:create', context_system::instance())) {
//     echo $courserenderer->add_new_course_button();
// }

if (core_userfeedback::should_display_reminder()) {
    core_userfeedback::print_reminder_block();
}

echo $OUTPUT->custom_block_region('content');

echo $OUTPUT->footer();

// Trigger dashboard has been viewed event.
$eventparams = array('context' => $context);
$event = \core\event\mycourses_viewed::create($eventparams);
$event->trigger();
