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

use core\output\action_menu\link;
use core\output\action_menu\link_secondary;
use core\output\pix_icon;
use format_topics\output\courseformat\content\section\controlmenu as controlmenu_format_topics;

/**
 * Base class to render a course section menu.
 *
 * @package   format_onetopic
 * @copyright 2022 David Herney Bernal - cirano. https://bambuco.co
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class controlmenu extends controlmenu_format_topics {
    /** @var bool Is the tabs view horizontal? */
    protected bool $horizontal;

    /**
     * Generate the edit control items of a section.
     *
     * This method must remain public until the final deprecation of section_edit_control_items.
     *
     * @return array of edit control items
     */
    public function section_control_items() {
        $course = $this->format->get_course();
        $controls = parent::section_control_items();

        // Legacy move up and down links.
        $this->horizontal = !$course->hidetabsbar && (
            $course->tabsview != \format_onetopic::TABSVIEW_VERTICAL &&
            $course->tabsview != \format_onetopic::TABSVIEW_VERTICALALL
        );
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
        if (
            ($this->section->section <= 1)
            || !has_capability('moodle/course:movesections', $this->coursecontext)
        ) {
            return null;
        }

        $url = $this->format->get_update_url(
            action: 'section_move_after',
            ids: [$this->section->id],
            targetsectionid: $this->format->get_section($this->section->section - 2)->id,
            returnurl: $this->baseurl,
        );

        return new link_secondary(
            url: $url,
            icon: new pix_icon($this->horizontal ? (right_to_left() ? 't/right' : 't/left') : 'i/up', ''),
            text: $this->horizontal ? get_string('moveleft') : get_string('moveup'),
            attributes: [
                'class' => 'moveup',
            ],
        );
    }

    /**
     * Retrieves the move down for the section control menu.
     *
     * @return link|null The menu item if applicable, otherwise null.
     */
    protected function get_section_movedown_item(): ?link {
        $numsections = $this->format->get_last_section_number();

        if (
            ($this->section->section == 0)
            || ($this->section->section >= $numsections)
            || !has_capability('moodle/course:movesections', $this->coursecontext)
        ) {
            return null;
        }

        $url = $this->format->get_update_url(
            action: 'section_move_after',
            ids: [$this->section->id],
            targetsectionid: $this->format->get_section($this->section->section + 1)->id,
            returnurl: $this->baseurl,
        );

        return new link_secondary(
            url: $url,
            icon: new pix_icon($this->horizontal ? (right_to_left() ? 't/left' : 't/right') : 'i/down', ''),
            text: $this->horizontal ? get_string('moveright') : get_string('movedown'),
            attributes: [
                'class' => 'movedown',
            ],
        );
    }

    /**
     * Retrieves the permalink item for the section control menu.
     *
     * @return link|null The menu item if applicable, otherwise null.
     */
    protected function get_section_permalink_item(): ?link {
        $link = parent::get_section_permalink_item();

        // Modify the permalink according to the Onetopic format.
        if ($link) {
            $url = $this->format->get_view_url(
                $this->section,
                ['permalink' => true, 'anchortotabstree' => true]
            );
            $link->url = $url;
        }

        return $link;
    }

    /**
     * Retrieves the delete item for the section control menu.
     *
     * @return link|null The menu item if applicable, otherwise null.
     */
    protected function get_section_delete_item(): ?link {
        $link = parent::get_section_delete_item();

        // ToDo: reload the page is a temporal solution. We need control the delete tab action with JS.
        if ($link) {
            $url = $this->format->get_update_url(
                action: 'section_delete',
                ids: [$this->section->id],
                returnurl: $this->format->get_view_url($this->format->get_section($this->section->section - 1)),
            );
            $link->url = $url;
            unset($link->attributes['data-action']);
        }

        return $link;
    }
}
