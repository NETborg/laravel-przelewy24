<?php
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 13:18
 */

namespace NetborgTeam\P24;

abstract class P24Response
{
    /**
     * @var mixed
     */
    protected $result;
    /**
     * @var GeneralError
     */
    protected $error;




    public function __construct($response)
    {
        if (is_object($response)) {
            if (isset($response->result)) {
                $this->result = $this->parseResult($response->result);
            }
            if (isset($response->error)) {
                $this->error = new GeneralError($response->error);
            }
        } elseif (is_array($response)) {
            if (isset($response['result'])) {
                $this->result = $this->parseResult($response['result']);
            }
            if (isset($response['error'])) {
                $this->error = new GeneralError($response['error']);
            }
        } else {
            $this->error = new GeneralError();
        }
    }


    /**
     * Override to parse custom results.
     *
     * @param $result
     * @return mixed
     */
    abstract public function parseResult($result);


    /**
     * Returns `true` if error occured.
     *
     * @return bool
     */
    public function isError()
    {
        return ((int) $this->error->errorCode) > 0;
    }


    /**
     * Returns GeneralError instance.
     *
     * @return GeneralError
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * Returns parsed result or `null` if no result received.
     *
     * @return mixed
     */
    public function result()
    {
        return $this->result;
    }
}
