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
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\BssAcl;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $subprojects;
    protected $totals;

    public function display($tpl = null)
    {
        $projectId = (int) Factory::getApplication()->input->getInt('id', 0);

        if (!BssAcl::hasAccessAny('project:view')) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $model = $this->getModel();
        $model->setState('projectsummary.id', $projectId);

        $this->item        = $this->get('Item');
        $this->subprojects = $this->get('Subprojects');
        $this->totals      = $this->get('Totals');

        return parent::display($tpl);
    }
}
