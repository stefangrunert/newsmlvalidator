<?php

/**
 * Class NewsMLValidation
 *
 * Keeps validation results
 */
class NewsMLValidationResult
{
    public $passed;
    public $errors = array();
    public $detections;
    public $guid = '';
    public $validatedStandard = '';
    public $hasError = false;
    public $numErrors = 0;
    public $message = '';
    public $service;


    public function __construct($validatedStandard)
    {
        $this->validatedStandard = $validatedStandard;
    }

    public static function generateMessage($numErrors)
    {
        if ($numErrors > 0) {
            $m = $numErrors . " error";
            if ($numErrors > 1) {
                $m .= "s";
            }
            $m .= " detected.";
            return $m;
        } else {
            return "Good work! No errors detected.";
        }
    }
}
