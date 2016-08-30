<?php
namespace FormHandler\Validator;

/**
 * Check if the value of the field is a valid email address
 */
class EmailValidator extends AbstractValidator
{
    /**
     * Check if this field should be required or not
     *
     * @var bool
     */
    protected $required = true;

    /**
     * Should we also check if the domain of the email address exists?
     *
     * @var bool
     */
    protected $checkIfDomainExists = false;

    /**
     * Create a new email validator
     *
     * @param bool $required
     * @param string $message
     * @param bool $checkIfDomainExists
     */
    public function __construct($required = true, $message = null, $checkIfDomainExists = false)
    {
        if ($message === null) {
            $message = dgettext('formhandler', 'Invalid email address.');
        }

        $this->setErrorMessage($message);
        $this->setRequired($required);
        $this->setCheckIfDomainExist($checkIfDomainExists);
    }

    /**
     * Check if the given field is valid or not.
     * @return bool
     * @throws \Exception
     */
    public function isValid()
    {
        $value = $this->field->getValue();

        if (is_array($value) || is_object($value)) {
            throw new \Exception("This validator only works on scalar types!");
        }

        // required but not given
        if ($this->required && $value == null) {
            return false;
        } // if the field is not required and the value is empty, then it's also valid
        elseif (! $this->required && $value == "") {
            return true;
        }

        // if regex fails...
        // alternative regex (from formhandler)
        // preg_match("/^[0-9A-Za-z_]([-_.]?[0-9A-Za-z_])*@[0-9A-Za-z][-.0-9A-Za-z]*\\.[a-zA-Z]{2,3}[.]?$/", $value)
        if (! preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i', $value)) {
            return false;
        }

        if ($this->checkIfDomainExists) {
            $host = substr(strstr($value, '@'), 1);

            if (function_exists('getmxrr')) {
                $tmp = null;
                if (! getmxrr($host, $tmp)) {
                    // this will catch dns that are not mx.
                    if (! checkdnsrr($host, 'ANY')) {
                        // invalid!
                        return false;
                    }
                }
            } else {
                // tries to fetch the ip address,
                // but it returns a string containing the unmodified hostname on failure.
                if ($host == gethostbyname($host)) {
                    // host is still the same, thus invalid
                    return false;
                }
            }
        }

        return true;
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

    /**
     * Return if we should also check if the domain name exists or not.
     * @return bool
     */
    public function isCheckIfDomainExist()
    {
        return $this->checkIfDomainExists;
    }

    /**
     * Store if we should check if the domain name of the email address exists
     * @param bool $value
     */
    public function setCheckIfDomainExist($value)
    {
        $this->checkIfDomainExists = (bool) $value;
    }
}
