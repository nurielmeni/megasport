<?php
include_once ABSPATH . 'wp-content/plugins/NlsHunter/renderFunction.php';
// Set the Modul customizer options
include_once NLS__PLUGIN_PATH . '/includes/customizer/customizerAdjustments.php';
include_once NLS__PLUGIN_PATH . '/includes/Hunter/NlsConfig.php';
include_once 'class-ApplicantDetails.php';

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    NlsHunter
 * @subpackage NlsHunter/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    NlsHunter
 * @subpackage NlsHunter/public
 * @author     Meni Nuriel <nurielmeni@gmail.com>
 */
class NlsHunter_Public
{
    /**
     * Fields names
     */
    const SID = 'sid';

    const JOB_CODE = 'job-code';
    const FULL_NAME = 'fullname';
    const PHONE = 'phone';
    const EMAIL = 'email';
    const CV_FILE = 'cv-file';

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $NlsHunter    The ID of this plugin.
     */
    private $NlsHunter;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /** 
     * Show log messages
     */
    private $debug;

    private $nlsConfig;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $NlsHunter       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($NlsHunter, $version, $debug = false)
    {
        $this->NlsHunter = $NlsHunter;
        $this->version = $version;
        $this->debug = $debug;
        $this->nlsConfig = new NlsConfig;
    }


    private function getSubmitResultUrl()
    {
        $language = get_bloginfo('language');
        return get_permalink(get_page_by_path('submit-success', OBJECT, ['page']));
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in NlsHunter_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The NlsHunter_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style('NlsHunter', plugin_dir_url(__FILE__) . 'css/NlsHunter-public.css', array(), $this->version, 'all');
        //wp_enqueue_style('NlsHunter-responsive', plugin_dir_url(__FILE__) . 'css/NlsHunter-public-responsive.css', array(), $this->version, 'all');
        wp_enqueue_style('sumoselect', plugin_dir_url(__FILE__) . 'css/sumoselect.min.css', array(), $this->version, 'all');
        wp_enqueue_style('front-page-loader', plugin_dir_url(__FILE__) . 'css/loader.css', array(), $this->version, 'all');

        if (is_rtl()) {
            wp_enqueue_style('sumoselect-rtl', plugin_dir_url(__FILE__) . 'css/sumoselect-rtl.css', array(), $this->version, 'all');
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in NlsHunter_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The NlsHunter_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script('nls-form', plugin_dir_url(__FILE__) . 'js/NlsHunterForm.js', array('jquery'), $this->version, false);
        wp_enqueue_script('nls-sumo-select', plugin_dir_url(__FILE__) . 'js/jquery.sumoselect.min.js', array('jquery'), $this->version, false);

        // enqueue and localise scripts for handling Ajax Submit CV, 'nls-form' is the script that will hold the object
        // Don't forget to add the action (apply_cv_function)
        // defined in the  class-NlsHunter-public.php (define_public_hooks)
        wp_localize_script('nls-form', 'frontend_ajax', ['url' => admin_url('admin-ajax.php')]);
    }

    /**
     * Helper function to write log messages
     */
    public function writeLog($message, $level = 'debug')
    {
        if (!$this->debug) return;

        $logFile = ABSPATH . 'wp-content/plugins/NlsHunter/logs/default.log';

        $data = date("Ymd") . ' ' . $level . ' ' . $message;
        file_put_contents($logFile, $data, FILE_APPEND);
    }

    /**
     * Get the CV file for the applicable friend
     * the CV file uploads temporarily and assigned a name
     */
    private function getCvFile($idx)
    {
        if (
            isset($_FILES[self::CV_FILE][$idx]) &&
            isset($_FILES[self::CV_FILE][$idx]['name']) &&
            strlen($_FILES[self::CV_FILE][$idx]['name']) > 0 &&
            strlen($_FILES[self::CV_FILE][$idx]['tmp_name']) > 0 &&
            !$_FILES[self::CV_FILE][$idx]['error'] &&
            $_FILES[self::CV_FILE][$idx]['size'] > 0
        ) {
            $fileExt = pathinfo($_FILES[self::CV_FILE][$idx]['name'])['extension'];
            $tmpCvFile = $this->getTempFile($fileExt);
            move_uploaded_file($_FILES[self::CV_FILE][$idx]['tmp_name'], $tmpCvFile);
            return $tmpCvFile;
        }
        return '';
    }

    /*
     * Apply the friend request
     */
    private function apply_job($fields, $idx)
    {
        $count = 0;

        $files = [];

        // 1. Create NCAI
        $ncaiFile = $this->createNCAI($fields);
        if (!empty($ncaiFile)) array_push($files, $ncaiFile);

        // 2. Get CV File
        $tmpCvFile = $this->getCvFile($idx);
        if (empty($tmpCvFile)) {
            $tmpCvFile = $this->genarateCvFile($fields);
        }

        if (!empty($tmpCvFile)) array_push($files, $tmpCvFile);

        // 3. Send email with file attachments
        $count += $this->sendHtmlMail($files, $fields, 0) ? 1 : 0;

        // 4. Remove temp files

        // Remove the temp CV file and NCAI file from the Upload directory
        foreach ($files as $file) unlink($file);

        return $count;
    }

    /*
     * Return the pager data to the search result module
     */
    public function apply_cv_function()
    {
        $applyCount = 0;
        $friendsCount = isset($_POST['friend-name']) && is_array($_POST['friend-name']) ? count($_POST['friend-name']) : 0;
        if ($friendsCount > 0) {
            for ($i = 0; $i < $friendsCount; $i++) {
                $fields = new ApplicationDetails($_POST, $i);
                $applyCount += $this->apply_job($fields, $i);
            }
        }

        $response = ['sent' => $applyCount];
        if ($applyCount > 0) {
            //$response['location'] = $this->getSubmitResultUrl();
            $response['html'] = $this->sentSuccess($applyCount);
        } else {
            $response['html'] = $this->sentError();
        }
        wp_send_json($response);
    }

    /**
     * Return a temp file path
     * @param $fileExt the file extention (ncai or other)
     */
    private function getTempFile($fileExt)
    {
        $tmpFolder = 'cvTempFiles';
        $upload_dir   = wp_upload_dir();

        if (!empty($upload_dir['basedir'])) {
            $cv_dirname = $upload_dir['basedir'] . '/' . $tmpFolder;
            if (!file_exists($cv_dirname)) {
                wp_mkdir_p($cv_dirname);
            }
        }
        if ($fileExt === 'ncai') {
            return $cv_dirname . DIRECTORY_SEPARATOR . 'NlsCvAnalysisInfo.' . $fileExt;
        }

        do {
            $tempFile = $cv_dirname . 'CV_FILE_' . mt_rand(100, 999) . '.' . $fileExt;
        } while (file_exists($tempFile));

        return $tempFile;
    }

    /**
     * Genarate cv file
     */
    private function genarateCvFile($fields, $i = 0)
    {
        $cvFile = $this->getTempFile('txt');

        // Open the file for writing.
        if (!$handle = fopen($cvFile, 'w')) {
            return '';
        }

        // Write the data
        foreach ($fields as $key => $field) {
            if (empty($field) || strpos($key, 'friend') === false) continue;

            $dataLine = ApplicationDetails::propertyLabel($key) . ': ' . $field . "\r\n";

            if (fwrite($handle, $dataLine) === FALSE) break;
        }

        $dataLine = 'ניסיון: ליד' . "\n\r";
        fwrite($handle, $dataLine);

        $dataLine = 'השכלה: ליד' . "\n\r";
        fwrite($handle, $dataLine);

        // Close the file
        fclose($handle);
        return $cvFile;
    }

    private function getPhoneData($phone)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phone);
        return [
            'CountryCode' => '972',
            'AreaCode' => substr($phoneNumber, 0, 3),
            'PhoneNumber' => substr($phoneNumber, 3, 7),
            'PhoneType' => 'Mobile'
        ];
    }

    private function createNCAI($fields, $i = 0)
    {
        //create xml file
        $xml_obj = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><NiloosoftCvAnalysisInfo xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"></NiloosoftCvAnalysisInfo>');

        // Applying Person
        $applyingPerson = $xml_obj->addChild('ApplyingPerson');
        $applyingPerson->addChild('EntityLocalName', $fields->friendName);

        $phoneData = $this->getPhoneData($fields->friendPhone);
        $phoneInfo = $applyingPerson->addChild('Phones')->addChild('PhoneInfo');
        $phoneInfo->addChild('CountryCode', $phoneData['CountryCode']);
        $phoneInfo->addChild('AreaCode', $phoneData['AreaCode']);
        $phoneInfo->addChild('PhoneNumber', $phoneData['PhoneNumber']);
        $phoneInfo->addChild('PhoneType', $phoneData['PhoneType']);

        $applyingPerson->addChild('SupplierId', $fields->sid);

        // Notes
        $applicant_notes = __('Applicant form data: ', 'NlsHunter') . "\r\n";
        // Change the $fields value for strongSide to include the name and not the id
        foreach ($fields as $key => $field) {
            if (empty($field)) continue;
            $applicant_notes .= ApplicationDetails::propertyLabel($key) . ': ' . $field . "\r\n";
        }
        $xml_obj->addChild('Notes', $applicant_notes);

        // Supplier ID
        $xml_obj->SupplierId = $fields->sid;

        $ncaiFile = $this->getTempFile('ncai');
        $xml_obj->asXML($ncaiFile);
        return $ncaiFile;
    }

    public function sendHtmlMail($files, $fields, $i, $msg = '')
    {
        // Change the $fields value for strongSide to include the name and not the id
        $to = $this->nlsConfig->getNlsToWebmail();
        $bcc = $this->nlsConfig->getNlsBccMail();
        $fromName = 'Mega Sport - FBF Jobs Site';
        $fromMail = 'noreply@cvwebmail.com';

        $headers = ['Content-Type: text/html; charset=UTF-8'];
        array_push($headers, 'From: ' . $fromName . ' <' . $fromMail . '>');
        if (strlen($bcc) > 0) array_push($headers, 'Bcc: ' . $bcc);

        $subject = __('CV Applied from Mega Sport Jobs Site', 'NlsHunter') . ': ';
        $subject .= $fields->friendJobCode ? $fields->friendJobCode : $msg;

        $attachments = $files ?: [];

        $body = render('mail/mailApply', [
            'fields' => $fields,
        ]);

        global $phpmailer;
        add_action('phpmailer_init', function (&$phpmailer) {
            $phpmailer->SMTPKeepAlive = true;
        });

        //add_filter('wp_mail_from', get_option(NlsHunter_Admin::FROM_MAIL));
        //add_filter('wp_mail_from_name', get_option(NlsHunter_Admin::FROM_NAME));

        $result =  wp_mail($to, $subject, $body, $headers, $attachments);
        //$this->writeLog("\nMail Result: $result");

        return $result;
    }

    private function sentSuccess($sent)
    {
        return render('mail/mailSuccess', []);
    }

    private function sentError($msg = '')
    {
        return render('mail/mailError', ['msg' => $msg]);
    }
}
