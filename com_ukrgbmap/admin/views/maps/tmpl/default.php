<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
//JHtml::_('behavior.tooltip');
?>

<form action="index.php?option=com_ukrgbmap&view=maps" method="post" id="adminForm" name="adminForm">
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th >
                <?php echo JText::_('COM_UKRGBMAP_ID') ;?>
            </th>
            <th >
                <?php echo JText::_('COM_UKRGBMAP_MAP_TYPE'); ?>
            </th>
            <th >
                <?php echo JText::_('COM_UKRGBMAP_TITLE'); ?>
            </th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="5">
                <?php echo $this->pagination->getListFooter(); ?>
            </td>
        </tr>
        </tfoot>
        <tbody>
        <?php if (!empty($this->items)) : ?>
            <?php foreach ($this->items as $i => $row) : ?>
                <tr>
                    <td>
                        <?php echo $row->id; ?>
                    </td>
                    <td>
                        <?php echo $row->map_type; ?>
					</td>
                    <td >
                        <?php echo $row->title; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</form>