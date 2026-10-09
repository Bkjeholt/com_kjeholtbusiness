<?php
/**
 * @package     KjeholtEngineering.Component.KjeholtBusiness
 * @subpackage  com_kjeholtbusiness
 *
 * @copyright   Copyright (C) 2026 Kjeholt Engineering and Services AB. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\Subproject;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\ProjectAcl;

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $form;
    protected $timecards;
    protected $expenses;
    protected $totals;

    public function display($tpl = null)
    {
        $this->item     = $this->get('Item');
        $this->form     = $this->get('Form');
        $this->timecards = $this->get('Timecards');
        $this->expenses = $this->get('Expenses');
        $this->totals   = $this->get('Totals');

        if ($this->getLayout() === 'edit'
            && !ProjectAcl::canEditSubproject($this->item)) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        return parent::display($tpl);
    }
}
