<?php defined( '_JEXEC' ) or die( 'Restricted access' );

class UkrgbmapViewDefaultHtml extends JViewHtml
{
	function render()
	{
		$app = JFactory::getApplication();
		$layout = $this->getLayout();


		$this->params = JComponentHelper::getParams('com_ukrgbmap');

		//retrieve task list from model
		$model = new UkrgbmapModelDefault();

		$this->message = $model->getMessage();

		//display
		return parent::render();
	}
}