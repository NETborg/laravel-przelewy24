<?php
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 13:08
 */

namespace NetborgTeam\P24;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

abstract class P24ResponseObject implements Arrayable, Jsonable
{

    /**
     * Override with valid object keys array.
     *
     * @var array
     */
    protected $keys =[];

    /**
     * Data storage.
     *
     * @var array
     */
    protected $data = [];

    /**
     * @var array|object|null
     */
    protected $response;


    public function __construct($response=null)
    {
        $this->response = $response;

        if ($response) {
            if (is_object($response)) {
                foreach ($this->keys as $key) {
                    if (isset($response->{$key})) {
                        $this->{$key} = $response->{$key};
                    }
                }
            } elseif (is_array($response)) {
                foreach ($this->keys as $key) {
                    if (isset($response[$key])) {
                        $this->{$key} = $response[$key];
                    }
                }
            }
        }
    }

    public function __set($key, $value)
    {
        if (in_array($key, $this->keys)) {
            $this->data[$key] = $value;
        }
    }


    public function __get($key)
    {
        if (in_array($key, $this->keys)) {
            return isset($this->data[$key]) ? $this->data[$key] : null;
        }

        return null;
    }

    /**
     * Returns response data.
     *
     * @return array|object|null
     */
    public function getResponse()
    {
        return $this->response;
    }


    /**
     * @return array
     */
    public function toArray()
    {
        $out = [];

        foreach ($this->data as $key => $value) {
            if (is_object($value) && $value instanceof Arrayable) {
                $out[$key] = $value->toArray();
                continue;
            }

            $out[$key] = $value;
        }

        return $out;
    }

    /**
     * @param  int         $options
     * @return string|void
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
