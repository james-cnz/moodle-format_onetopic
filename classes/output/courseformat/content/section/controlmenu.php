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
 * Contains the default section controls output class.
 *
 * @package   format_onetopic
 * @copyright 2022 David Herney Bernal - cirano. https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_onetopic\output\courseformat\content\section;

use context_course;
use core\output\action_menu\link_secondary;
use core\output\pix_icon;
use core\url;
use format_topics\output\courseformat\content\section\controlmenu as controlmenu_format_topics;

/**
 * Base class to render a course section menu.
 *
 * @package   format_onetopic
 * @copyright 2022 David Herney Bernal - cirano. https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class controlmenu extends controlmenu_format_topics {
    /** @var course_format the course format class */
    protected $format;

    /** @var section_info the course section class */
    protected $section;

    /**
     * Generate the edit control items of a section.
     *
     * This method must remain public until the final deprecation of section_edit_control_items.
     *
     * @return array of edit control items
     */
    public function section_control_items() {
        global $USER;

        $controls = parent::section_control_items();

        // Legacy move up and down links.
        $course = $this->format->get_course();
        $this->horizontal = !$course->hidetabsbar && (
            $course->tabsview != \format_onetopic::TABSVIEW_VERTICAL &&
            $course->tabsview != \format_onetopic::TABSVIEW_VERTICALALL
        );
        $this->rtl = right_to_left();
        $controls = $this->add_control_after($controls, 'movesection', 'moveup', $this->get_section_moveup_item());
        $controls = $this->add_control_after($controls, 'moveup', 'movedown', $this->get_section_movedown_item());

        return $controls;
    }

    /**
     * Retrieves the move up for the section control menu.
     *
     * @return link|null The menu item if applicable, otherwise null.
     */
    protected function get_section_moveup_item(): ?link {
        $url = clone($baseurl);
        if ($this->section->section > 1) { // Add a arrow to move section up.
            $url->param('section', $this->section->section);
            $url->param('move', -1);
            $strmoveup = $this->horizontal ? get_string('moveleft') : get_string('moveup');
            $movecontrols['moveup'] = new link_secondary(
                url: $url,
                icon: new pix_icon($this->horizontal ? ($this->rtl ? 't/right' : 't/left') : 'i/up', ''),
                text: $strmoveup,
                attributes: ['class' => 'icon' . ($this->horizontal ? '' : ' moveup')],
            );
        }
    }

    /**
     * Retrieves the move down for the section control menu.
     *
     * @return link|null The menu item if applicable, otherwise null.
     */
    protected function get_section_movedown_item(): ?link {
        $url = clone($baseurl);
        if ($section->section < $this->numsections) { // Add a arrow to move section down.
            $url->param('section', $this->section->section);
            $url->param('move', 1);
            $strmovedown = $this->horizontal ? get_string('moveright') : get_string('movedown');
            $movecontrols['movedown'] = new link_secondary(
                url: $url,
                icon: new pix_icon($this->horizontal ? ($this->rtl ? 't/left' : 't/right') : 'i/down', ''),
                text: $strmovedown,
                attributes: ['class' => 'icon' . ($this->horizontal ? '' : ' movedown')],
            );
        }
    }

    /**
     * Retrieves the permalink item for the section control menu.
     *
     * @return link|null The menu item if applicable, otherwise null.
     */
    protected function get_section_permalink_item(): ?link {
        // Create the permalink according to the Onetopic format.
        if (array_key_exists("permalink", $parentcontrols)) {
            $sectionlink = $format->get_view_url(
                $section,
                ['permalink' => true, 'anchortotabstree' => true]
            );

            $parentcontrols['permalink'] = new link_secondary(
                url: $sectionlink,
                icon: new pix_icon('i/link', ''),
                text: get_string('sectionlink', 'course'),
                attributes: [
                    'class' => 'icon',
                    'data-action' => 'permalink',
                ],
            );
        }
    }

    /**
     * Retrieves the delete item for the section control menu.
     *
     * @return link|null The menu item if applicable, otherwise null.
     */
    protected function get_section_delete_item(): ?link {
        // ToDo: reload the page is a temporal solution. We need control the delete tab action with JS.
        if (array_key_exists("delete", $parentcontrols)) {
            $url = new url('/course/editsection.php', [
                'id' => $section->id,
                'sr' => $section->section - 1,
                'delete' => 1,
                'sesskey' => sesskey(),
            ]);

            $parentcontrols['delete'] = new link_secondary(
                url: $url,
                icon: new pix_icon('i/delete', ''),
                text: get_string('delete'),
                attributes: ['class' => 'editing_delete text-danger'],
            );
        }
    }
}
