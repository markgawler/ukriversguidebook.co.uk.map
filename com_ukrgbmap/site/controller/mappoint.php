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
        $mapType = $app->input->get('type',Null);
        $radius = $app->input->get('radius',Null);
        $centreLat = $app->input->get('lat',Null);
        $centreLng = $app->input->get('lng',Null);


        if (!is_null($guideId )){
			$points = $model->getByGuideId($guideId);
			echo json_encode($points);
				
		}else if (!is_null($mapType)) {
            $points = $model->getByMapType($mapType);
            echo json_encode($points);

		} else {
            if (!is_null($radius )){
                $points = $model->getByRadius($centreLat, $centreLng, $radius);
                echo json_encode($points);
            }

}
        JFactory::getApplication()->close(); // TODO: fix json view

	}
}
?>