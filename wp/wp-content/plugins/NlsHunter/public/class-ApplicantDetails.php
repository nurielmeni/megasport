<?php

class ApplicationDetails
{
    public $sid = '';
    public $friendName = '';
    public $friendPhone = '';
    public $friendJobCode = '';
    public $employeeName = '';
    public $employeePhone = '';
    public $employeeId = '';

    public function __construct($data, $idx = 0)
    {
        foreach ($data as $key => $value) {
            $prop = $this->dashesToCamelCase($key);
            if (!property_exists($this, $prop)) continue;

            $this->$prop = is_array($value) ? $value[$idx] : $value;
        }
    }

    public static function propertyLabel($prop)
    {
        $labels = self::propertyLabels();
        return key_exists($prop, $labels) ? $labels[$prop] : '';
    }

    public static function propertyLabels()
    {
        return [
            'sid' => __('Supplier Id', 'NlsHunter'),
            'friendName' => __('Friend Name', 'NlsHunter'),
            'friendPhone' => __('Friend Phone', 'NlsHunter'),
            'friendJobCode' => __('Friend Job Code', 'NlsHunter'),
            'employeeName' => __('Employee Name', 'NlsHunter'),
            'employeePhone' => __('Employee Phone', 'NlsHunter'),
            'employeeId' => __('Employee Id', 'NlsHunter'),
        ];
    }

    private function dashesToCamelCase($string, $capitalizeFirstCharacter = false)
    {

        $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }
}
