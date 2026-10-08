<?php
/**
 * @package     KjeholtEngineering.Component.KjeholtBusiness
 * @subpackage  com_kjeholtbusiness
 *
 * @copyright   Copyright (C) 2026 Kjeholt Engineering and Services AB. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\Projectsummary;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $subprojects;
    protected $totals;

    public function display($tpl = null)
    {
        $this->item       = $this->get('Item');
        $this->subprojects = $this->get('Subprojects');
        $this->totals     = $this->get('Totals');

        return parent::display($tpl);
    }
}
