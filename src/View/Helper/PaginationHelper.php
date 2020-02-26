<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
namespace App\View\Helper;

use App\View\Helper\AppHelper;


/**
 * Helper to display pagination.
 *
 * @category Default
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class PaginationHelper extends AppHelper
{
    public $helpers = array('Paginator', 'Html');

    /**
     * Wraps Paginator->sort with default order
     *
     * @param string    Title for the link
     * @param string    The name of the key that the recordset should be sorted. If $key is null $title will be used for the key, and a title will be generated by inflection
     * @param array     options; 'defaultOrders' => array('field' => 'defaultOrder') where defaultOrder = desc/asc
     *
     * @return string   the link to sort
     */
    public function sortDefaultOrder($title, $key = null, $options =  array()){
        // direction is given or no default is specified
        if(isset($this->request->params['direction'])
            || !isset($options['defaultOrders'])
            || !isset($options['defaultOrders'][$key])){
            return $this->Paginator->sort($key, $title, $options);
        }

        $do = $options['defaultOrders'];
        //If no field is specified or the field ordered by is not the current field
        if((isset($this->request->params['sort']) && $this->request->params['sort'] != $key)
           || !isset($this->request->params['sort'])){
            $options['direction'] = $do[$key];
        }

        return $this->Paginator->sort($key, $title, $options);
    }

    /**
     * Display pagination.
     *
     * @param array $extramParams Array containing the extra params that should
     *                            appear in the pagination URL.
     * @param   int $numberLastLinks Custrom values passed to PaginatorHelper::numbers()
     *
     * @return void
     */
    public function display($extramParams = array(), $extraNumbersOptions = array())
    {
        if (!$this->Paginator->hasPrev() && !$this->Paginator->hasNext()) {
            return;
        }
        
        $numberFirstLinks = 1;
        $numberLastLinks = 1;
        $numbersOptions = array(
            'separator' => '',
            // Print up to 6/2 = 3 numbered links in rectangles on each side
            // of the central numbered link, in addition to the
            // first and prev links on the far left, and the next
            // and last links on the far right. We should
            // use a smaller value for this parameter on mobile
            // devices if we ever customize the interface to
            // behave differently based on the display size.
            // Note that the size of the links depends on the number of
            // digits in the number. Unless we can adapt the modulus
            // dynamically based on the number of digits per link, we
            // need to choose the number conservatively so that it will
            // fit the screen "real estate" that we have available for
            // the largest numbers (for example, when we're near the
            // end of the list of English sentences).
            'modulus' => 6,
            'class' => 'pageNumber',
            'first' => $numberFirstLinks,
            'last' => $numberLastLinks,
        );
        $numbersOptions = array_merge($numbersOptions, $extraNumbersOptions);
        ?>
        <ul class="paging">
            <?php
            $prevIcon = $this->Html->tag('md-icon', 'keyboard_arrow_left', [
                'ng-cloak' => true,
                // @translators Appears on “previous page” links
                // mouseover. Keyboard shortcut is between brackets.
                'title' => __('Previous page [Ctrl+←]'),
            ]);
            echo $this->Paginator->prev($prevIcon, ['escape' => false]);
            
            echo $this->Paginator->numbers($numbersOptions);

            $nextIcon = $this->Html->tag('md-icon', 'keyboard_arrow_right', [
                'ng-cloak' => true,
                // @translators Appears on “next page” links
                // mouseover. Keyboard shortcut is between brackets.
                'title' => __('Next page [Ctrl+→]'),
            ]);
            echo $this->Paginator->next($nextIcon, ['escape' => false]);
            ?>
        </ul>
        <?php
    }

    /**
     * Swap sort link templates for ordering by user role
     *
     * User roles are stored as an enum in the database where the highest role
     * (admin) is represented by the smallest value (1). Thus using the default
     * templates for Paginator->sort() would produce the wrong arrows. In order
     * to display the correct arrows, we need to swap the 'sortAsc' and 'sortDesc'
     * templates before calling Paginator->sort().
     *
     * @return string
     **/
    public function sortForRole() {
        $templates = $this->Paginator->getTemplates();
        $this->Paginator->setTemplates([
            'sortAsc' => $this->Paginator->getTemplates('sortDesc'),
            'sortDesc' => $this->Paginator->getTemplates('sortAsc')
        ]);
        $sortLink = $this->Paginator->sort('role', __('Member status'));
        $this->Paginator->setTemplates($templates);
        return $sortLink;
    }
}
?>
