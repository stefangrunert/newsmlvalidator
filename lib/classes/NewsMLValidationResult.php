<?php

/**
 * Class NewsMLValidation
 *
 * Keeps validation results
 */
class NewsMLValidationResult
{
    public $detections;
    public $guid = '';
    public $validatedStandard = '';
    public $hasError = false;
    public $numErrors = 0;
    public $message = '';
    public $responseBody = '';
    public $errors = [];
    public $passed;


    public function __construct($validatedStandard)
    {
        $this->validatedStandard = $validatedStandard;
    }
}
