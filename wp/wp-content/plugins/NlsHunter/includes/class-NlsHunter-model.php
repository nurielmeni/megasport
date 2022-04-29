<?php
include_once 'Hunter/NlsConfig.php';
require_once 'Hunter/NlsCards.php';
require_once 'Hunter/NlsSecurity.php';
require_once 'Hunter/NlsDirectory.php';
require_once 'Hunter/NlsSearch.php';
require_once 'Hunter/NlsHelper.php';
require_once 'Hunter/NlsFilter.php';
/**
 * Description of class-NlsHunter-modules
 *
 * @author nurielmeni
 */
class NlsHunter_model
{
    const STATUS_OPEN = 1;

    private $nlsSecutity;
    private $auth;
    private $nlsCards;
    private $nlsSearch;
    private $nlsDirectory;
    private $supplierId;

    private $nlsConfig;

    private $nlsFlashCache  = true;
    private $nlsCacheTime  = 20 * 60;

    private $regions;

    public function __construct()
    {
        $this->nlsConfig = new NlsConfig;
        try {
            $this->nlsSecutity = new NlsSecurity($this->nlsConfig);
        } catch (\Exception $e) {
            $this->nlsAdminNotice(
                __('Could not create Model.', 'NlsHunter'),
                __('Error: NlsHunter_model: ', 'NlsHunter')
            );
            return null;
        }
        $this->auth = $this->nlsSecutity->isAuth();
        $this->nlsFlashCache = true;
        $this->nlsCacheTime = 20 * 60;
        $this->supplierId = $this->queryParam('sid', $this->nlsConfig->getNlsSupplierId());

        if (!$this->auth) {
            $username = $this->nlsConfig->getNlsUser();
            $password = $this->nlsConfig->getNlsPassword();
            $this->auth = $this->nlsSecutity->authenticate($username, $password);

            // Check if Auth is OK and convert to object
            if ($this->nlsSecutity->isAuth() === false) {
                //$this->nlsAdminNotice('Authentication Error', 'Can not connect to Niloos Service.');
                //$this->nlsPublicNotice('Authentication Error', 'Can not connect to Niloos Service.');
            }
        }

        // Load data on ajax calls
        if (!wp_doing_ajax()) {
        }
    }

    public function getDefaultLogo()
    {
        return esc_url(plugins_url('NlsHunter/public/images/employer-logo.svg'));
    }

    public function queryParam($param, $default = '', $post = false)
    {
        if ($post) {
            return isset($_POST[$param]) ? $_POST[$param] : $default;
        }
        return isset($_GET[$param]) ? $_GET[$param] : $default;
    }

    public function nlsGetSupplierId()
    {
        return $this->supplierId;
    }

    public function front_add_message()
    {
        add_filter('the_content', 'front_display_message');
    }

    public function front_display_message($msg)
    {
        add_filter('the_content', function ($content) use ($msg) {
            $message = '<div class="absolute top-0 z-20 p-4 bg-danger">' . $msg . '</div>';
            $content = "$message\n\n" . $content;
            return $content;
        });
    }

    public function nlsPublicNotice($title, $notice)
    {
        $cont = '<div class="notice notice-error"><label>' . $title . '</label><p>' . $notice . '</p></div>';

        add_action('the_post', function ($post) use ($cont) {
            echo $cont;
        });
    }

    public function nlsAdminNotice($title, $notice)
    {
        add_action('admin_notices', function () use ($title, $notice) {
            $class = 'notice notice-error';
            printf('<div class="%1$s"><label>%2$s</label><p>%3$s</p></div>', esc_attr($class), esc_html($title), esc_html($notice));
        });
    }

    /**
     * Gets a card by email or phone
     */
    public function getCardByEmailOrCell($email, $cell)
    {
        $card = [];
        if (!empty($email)) {
            $card = $this->nlsCards->ApplicantHunterExecuteNewQuery2('', '', '', '', $email);
        }
        if (count($card) === 0 && !empty($cell)) {
            $card = $this->nlsCards->ApplicantHunterExecuteNewQuery2('', $cell, '', '', '');
        }
        return $card;
    }

    /**
     * Add file to card
     */
    public function insertNewFile($cardId, $file)
    {
        $fileContent = file_get_contents($file['path']);
        return $this->nlsCards->insertNewFile($cardId, $fileContent, $file['name'], $file['ext']);
    }

    /**
     * Init cards service
     */
    public function initCardService()
    {
        try {
            if ($this->auth !== false && !$this->nlsCards) {
                $this->nlsCards = new NlsCards([
                    'auth' => $this->auth,
                ]);
            }
        } catch (\Exception $e) {
            $this->nlsAdminNotice(
                __('Could not init Card Services.', 'NlsHunter'),
                __('Error: Card Services: ', 'NlsHunter')
            );
            $this->nlsPublicNotice(
                __('Could not init Card Services.', 'NlsHunter'),
                __('Error: Card Services: ', 'NlsHunter')
            );
            return null;
        }
        return true;
    }

    /**
     * Init directory service
     */
    public function initDirectoryService()
    {
        try {
            if ($this->auth !== false && !$this->nlsDirectory) {
                $this->nlsDirectory = new NlsDirectory([
                    'auth' => $this->auth
                ]);
            }
        } catch (\Exception $e) {
            $this->nlsAdminNotice(
                __('Could not init Directory Services.', 'NlsHunter'),
                __('Error: Directory Services: ', 'NlsHunter')
            );
            $this->front_display_message(__('Could not init Directory Services.', 'NlsHunter'));
            return null;
        }
        return true;
    }

    /**
     * Init search service
     */
    public function initSearchService()
    {
        try {
            if ($this->auth !== false && !$this->nlsSearch) {
                $this->nlsSearch = new NlsSearch([
                    'auth' => $this->auth,
                ]);
            }
        } catch (\Exception $e) {
            $this->nlsAdminNotice(
                __('Could not init Search Services.', 'NlsHunter'),
                __('Error: Search Services: ', 'NlsHunter')
            );
            $this->nlsPublicNotice(
                __('Could not init Search Services.', 'NlsHunter'),
                __('Error: Search Services: ', 'NlsHunter')
            );
            return null;
        }
        return true;
    }

    public function getJobByJobCode($jobCode)
    {
        return $this->nlsCards->getJobByJobCode($jobCode);
    }

    public function searchJobByJobCode($jobCode)
    {
        if (!$jobCode) return null;
        $resultRowLimit = 1;
        $resultRowOffset = 0;

        $cache_key = 'nls_hunter_job_' . $jobCode;
        if ($this->nlsFlashCache) wp_cache_delete($cache_key);

        $job = wp_cache_get($cache_key);

        if (false === $job) {
            if (!$this->initSearchService()) return ['totalHits' => 0, 'list' => []];

            $filter = new NlsFilter();

            $filter->addSuplierIdFilter($this->nlsGetSupplierId());

            $filterField = new FilterField('JobCode', SearchPhrase::EXACT, $jobCode, NlsFilter::TERMS_NON_ANALAYZED);
            $filter->addWhereFilter($filterField, WhereCondition::C_AND);

            try {
                $job = $this->nlsSearch->JobHunterExecuteNewQuery2(
                    null,
                    $resultRowOffset,
                    $resultRowLimit,
                    $filter
                );
            } catch (Exception $ex) {
                echo $ex->getMessage();
                return null;
            }
        }

        return $job->TotalHits === 1 && property_exists($job, 'Results') && property_exists($job->Results, 'JobInfo') ? $job->Results->JobInfo : null;
    }

    /**
     * Return the categories
     */
    public function categories()
    {
        $this->initDirectoryService();
        $categories = $this->nlsDirectory->getCategories();
        return $categories;
    }

    public function jobScopes()
    {
        $this->initDirectoryService();

        $cacheKey = 'JOB_SCOPES';
        $jobScopes = wp_cache_get($cacheKey);

        if (false === $jobScopes) {
            $jobScopes = $this->nlsDirectory->getJobScopes();
            wp_cache_set($cacheKey, $jobScopes, 'directory', $this->nlsCacheTime);
        }

        return is_array($jobScopes) ? $jobScopes : [];
    }

    public function jobAreas()
    {
        $this->initDirectoryService();

        $cacheKey = 'JOB_AREAS';
        $jobAreas = wp_cache_get($cacheKey);

        if (false === $jobAreas) {
            $jobAreas = $this->nlsDirectory->getProfessionalFields();
            wp_cache_set($cacheKey, $jobAreas, 'directory', $this->nlsCacheTime);
        }

        return is_array($jobAreas) ? $jobAreas : [];
    }

    public function jobRanks()
    {
        $this->initDirectoryService();

        $cacheKey = 'JOB_RANKS';
        $jobRanks = wp_cache_get($cacheKey);

        if (false === $jobRanks) {
            $jobRanks = $this->nlsDirectory->getJobRanks();
            wp_cache_set($cacheKey, $jobRanks, 'directory', $this->nlsCacheTime);
        }

        return is_array($jobRanks) ? $jobRanks : [];
    }

    public function professionalFields()
    {
        $this->initDirectoryService();

        $cacheKey = 'PROFESSIONAL_FIELD';
        $professionalFields = wp_cache_get($cacheKey);

        if (false === $professionalFields) {
            $professionalFields = $this->nlsDirectory->getProfessionalFields();
            wp_cache_set($cacheKey, $professionalFields, 'directory', $this->nlsCacheTime);
        }

        return is_array($professionalFields) ? $professionalFields : [];
    }

    public function regions()
    {
        $this->initDirectoryService();

        $cacheKey = 'REGIONS';
        $regions = wp_cache_get($cacheKey);

        if (false === $regions) {
            $regions = $this->nlsDirectory->getRegions();
            wp_cache_set($cacheKey, $regions, 'directory', $this->nlsCacheTime);
        }

        return is_array($regions) ? $regions : [];
    }

    public function getHotJobs($professionalFields)
    {
        $searchParams = is_array($professionalFields) ? ['' => $professionalFields] : [];

        $res =  $this->getJobHunterExecuteNewQuery2($searchParams, null, 0, NlsConfig::NLS_HOT_JOBS_COUNT);
        return $res['list'];
    }

    public function getJobHunterExecuteNewQuery2($searchParams = [], $hunterId = null, $page = 0, $resultRowLimit = null)
    {
        $resultRowLimit = $resultRowLimit ? $resultRowLimit : $this->nlsConfig->getNlsJobsCount();
        $resultRowOffset = is_int($page) ? $page * $resultRowLimit : 0;
        $region = key_exists('Region', $searchParams) ? $searchParams['Region'] : 0;
        $employer = key_exists('EmployerId', $searchParams) ? $searchParams['EmployerId'] : 0;

        $cache_key = 'nls_hunter_jobs_' . $region . '_' . $employer . '_' . $resultRowOffset . '_' . $resultRowLimit;
        if ($this->nlsFlashCache) wp_cache_delete($cache_key);

        $jobs = wp_cache_get($cache_key);

        if (false === $jobs) {
            if (!$this->initSearchService()) return ['totalHits' => 0, 'list' => []];

            if (!is_array($searchParams)) $jobs = [];
            $filter = new NlsFilter();

            $filter->addSuplierIdFilter($this->nlsGetSupplierId());

            if ($region !== 0) {
                $filterField = new FilterField('RegionId', SearchPhrase::EXACT, $region, NlsFilter::NUMERIC_VALUES);
                $filter->addWhereFilter($filterField, WhereCondition::C_AND);
            }

            if ($employer !== 0) {
                $filterField = new FilterField('EmployerId', SearchPhrase::EXACT, $employer, NlsFilter::TERMS_NON_ANALAYZED);
                $filter->addWhereFilter($filterField, WhereCondition::C_AND);
            }

            try {
                $res = $this->nlsSearch->JobHunterExecuteNewQuery2(
                    $hunterId,
                    $resultRowOffset,
                    $resultRowLimit,
                    $filter
                );
            } catch (Exception $ex) {
                echo $ex->getMessage();
                return null;
            }
        }

        $jobs['totalHits'] = property_exists($res, 'TotalHits') ? $res->TotalHits : 0;
        if ($jobs['totalHits'] === 0) {
            $jobs['list'] = [];
        } else {
            $jobInfo = property_exists($res, 'Results') && property_exists($res->Results, 'JobInfo') ? $res->Results->JobInfo : false;
            $jobs['list'] = !$jobInfo ? [] : (is_array($jobInfo) ? $jobInfo : [$jobInfo]);
        }

        return $jobs;
    }

    public function getCardProfessinalField($cardId)
    {
        if (!$this->initCardService()) return [];

        $professionalFields = $this->nlsCards->cardProfessinalField($cardId);

        return $professionalFields;
    }

    public function filesListGet($parentId)
    {
        if (!$this->initCardService()) return [];

        return $this->nlsCards->filesListGet($parentId);
    }

    /**
     * Get job details by jon id
     * @jobId - the jon id
     */
    public function getJobDetails($jobId)
    {
        if (!$this->initCardService()) return [];

        return $this->nlsCards->jobGet($jobId);
    }

    public function getApplicantCVList($applicantId)
    {
        $cacheKey = 'APPLICANT_CV_' . $applicantId;
        $applicantCvList = wp_cache_get($cacheKey);

        if (false === $applicantCvList) {
            $applicantCvList = [];
            if (!$this->initCardService()) return [];
            $cvList = $this->nlsCards->getCVList($applicantId);

            foreach ($cvList as $cv) {
                $fileInfo = $this->nlsCards->getFileInfo($cv->FileId, $applicantId);
                $applicantCvList[] = $fileInfo->FileGetByFileIdResult;
            }
        }
        return $applicantCvList;
    }

    public function listItemsToSelectOptions($list, $id, $value)
    {
        $options = [];
        if (!$list || !is_array($list) || !$id || !$value) return $options;

        foreach ($list as  $key => $item) {
            if (!is_object($item) || !property_exists($item, $id) || !property_exists($item, $value)) continue;
            $options[] = ['id' => trim($item->$id), 'value' => trim($item->$value)];
        }

        return $options;
    }
}
