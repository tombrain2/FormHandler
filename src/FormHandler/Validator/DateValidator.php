<?php
namespace FormHandler\Validator;

/**
 * This validator will validate a field and make sure it is a proper date.
 */
class DateValidator extends AbstractValidator
{
    /**
     * Check if this field should be required or not
     *
     * @var bool
     */
    protected $required;

    /**
     * Var to remember if the value was valid or not
     *
     * @var bool
     */
    protected $valid = null;

    /**
     * Create a new Date validator. We will check if the date can be parsed by strtotime.
     * @param bool $required
     * @param null $message
     */
    public function __construct($required = true, $message = null)
    {
        if ($message === null) {
            $message = dgettext('formhandler', 'This value is incorrect.');
        }

        $this->setRequired($required);
        $this->setErrorMessage($message);
    }

    /**
     * Check if the given field is valid or not. The date should be parsable by strtotime.
     *
     * @return boolean
     */
    public function isValid()
    {
        $value = $this->field->getValue();

        if ($this->valid === null) {
            if ($value == '' && $this->required == false) {
                $this->valid = true;
                return $this->valid;
            }

            $parsedDate = date_parse($value);

            if ($parsedDate['warning_count'] == 0 &&
                $parsedDate['error_count'] == 0 &&
                isset($parsedDate['year']) &&
                isset($parsedDate['month'])) {
                $this->valid = true;
                return $this->valid;
            }

            $this->valid = false;
            return $this->valid;
        }

        return $this->valid;
    }

    /**
     * Set if this field is required or not.
     *
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = (bool) $required;
    }

    /**
     * Get if this field is required or not.
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }
}
