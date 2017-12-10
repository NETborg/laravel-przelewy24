<?php
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 13:08
 */

namespace NetborgTeam\P24;


abstract class P24ResponseObject
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


    public function __construct($response=null)
    {
        if ($response) {
            if (is_object($response)) {
                foreach($this->keys as $key) {
                    if (isset($response->{$key})) {
                        $this->{$key} = $response->{$key};
                    }
                }
            } elseif (is_array($response)) {
                foreach($this->keys as $key) {
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


    public function toArray()
    {
        return $this->data;
    }

}