<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->filter_order);
$listDirn = $this->escape($this->filter_order_Dir);
?>

<form action="index.php?option=com_ukrgbmap&view=maps" method="post" id="adminForm" name="adminForm">
	<div id="j-sidebar-container" class="span2">
        <?php echo JHtmlSidebar::render(); ?>
	</div>
	<div id="j-main-container" class="span10">
		<div class="row-fluid">
			<div class="span6">
                <?php echo JText::_('COM_UKRGBMAP_MAPS_FILTER'); ?>
                <?php
                echo JLayoutHelper::render(
                    'joomla.searchtools.default',
                    array('view' => $this)
                );
                ?>
			</div>
		</div>
		<table class="table table-striped table-hover">
			<thead>
			<tr>
				<th>
                    <?php echo JText::_('COM_UKRGBMAP_ID'); ?>
				</th>
				<th>
                    <?php echo JHtml::_('grid.checkall'); ?>
				</th>
				<th>
                    <?php echo JHtml::_('grid.sort', 'COM_UKRGBMAP_MAP_TYPE', 'map_type', $listDirn, $listOrder); ?>
				</th>
				<th>
                    <?php echo JHtml::_('grid.sort', 'COM_UKRGBMAP_TITLE', 'title', $listDirn, $listOrder); ?>
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
                <?php foreach ($this->items as $i => $row) :
                    $link = JRoute::_('index.php?option=com_ukrgbmap&task=map.edit&id=' . $row->id);
                    ?>
					<tr>
						<td><?php echo $this->pagination->getRowOffset($i); ?></td>
						<td>
                            <?php echo JHtml::_('grid.id', $i, $row->id); ?>
						</td>

						<td>
							<a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_UKRGBMAP_EDIT_MAP'); ?>">
                                <?php echo JText::_('COM_UKRGBMAP_MAP_TYPE_' . $row->map_type); ?>
							</a>

						</td>

						<td>
							<a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_UKRGBMAP_EDIT_MAP'); ?>">
                                <?php echo $row->title; ?>
							</a>
							<div class="small">
                                <?php
                                $cat = ($row->category_title != null) ? $this->escape($row->category_title) : JText::_('COM_UKRGBMAP_UNCATEGORISED');
                                echo JText::_('JCATEGORY') . ': ' . $cat; ?>
							</div>
						</td>

					</tr>
                <?php endforeach; ?>
            <?php endif; ?>
			</tbody>
		</table>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
        <?php echo JHtml::_('form.token'); ?>
	</div>
</form>