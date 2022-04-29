<?php
require_once 'Hunter/NlsHelper.php';
require_once ABSPATH . 'wp-content/plugins/NlsHunter/renderFunction.php';

/**
 * Description of class-NlsHunter-modules
 *
 * @author nurielmeni
 */
class NlsHunter_modules
{
    private $model;

    public function __construct($model, $version)
    {
        $this->model = $model;
        $this->version = $version;
    }

    public function nlsApplicationForm_render()
    {
        $jobs = $this->model->getJobHunterExecuteNewQuery2();

        ob_start();
        echo render('applyForJobs', [
            'jobOptions' => $this->model->listItemsToSelectOptions($jobs['list'], 'JobCode', 'JobTitle'),
            'total' => $jobs['totalHits'],
            'model' => $this->model,
            'companyOptions' => []
        ]);

        return ob_get_clean();
    }
}
