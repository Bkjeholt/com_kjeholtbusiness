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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $subprojects;
    protected $totals;

    public function display($tpl = null)
    {
        $user    = Factory::getUser();
        $projectId = (int) Factory::getApplication()->input->getInt('id', 0);

        if (!$user->authorise('project.view', 'com_kjeholtbusiness.project.' . $projectId)
            && !$user->authorise('project.view', 'com_kjeholtbusiness')) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $this->item       = $this->get('Item');
        $this->subprojects = $this->get('Subprojects');
        $this->totals     = $this->get('Totals');

        return parent::display($tpl);
    }
}
