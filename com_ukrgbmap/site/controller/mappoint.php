<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


class UkrgbmapControllerMappoint extends JControllerBase
{
	public function execute()
	{
		$app = JFactory::getApplication();
		$model = new UkrgbmapModelMappoint;
		$guideId = $app->input->get('guideid',Null);

		if (!is_null($guideId )){
			$points = $model->getByGuideId($guideId);
			echo json_encode($points);
				
		}else {
			$mapType = $app->input->get('type',Null);
			if (!is_null($mapType )){
				$points = $model->getByMapType($mapType);
				echo json_encode($points);
			}

		}
        JFactory::getApplication()->close(); // TODO: fix json view

	}
}
?>