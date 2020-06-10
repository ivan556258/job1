<?php


namespace App\Exceptions;


use Throwable;

class DoubleRegistration extends \Exception
{
    private $data;

    public function __construct(array $data = [], $message = "", $code = 0, Throwable $previous = null)
    {
        $this->data = $data;
        \Log::debug('Double registration detected.', $data);
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
