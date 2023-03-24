<?php
/**
 * UKRGB Map
 *
 * @package        com_ukrgbmap Administrator
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<form action="<?php echo JRoute::_('index.php?option=com_ukrgbmap&layout=edit&id=' . (int) $this->item->id); ?>"
	  method="post" name="adminForm" id="adminForm">

    <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_UKRGBMAP_TAB_DETAILS')); ?>

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_UKRGBMAP_MAP_DETAILS'); ?></legend>
			<div class="row-fluid">
				<div class="span12">
                    <?php echo $this->form->renderFieldset('details'); ?>
				</div>
			</div>
		</fieldset>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('COM_UKRGBMAP_TAB_RULES')); ?>
        <?php echo $this->form->getInput('rules'); ?>

		<?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="map.edit"/>
    <?php echo JHtml::_('form.token'); ?>
</form>