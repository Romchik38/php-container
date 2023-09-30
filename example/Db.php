<?php

namespace Example;

class Db
{
    // key => value;
    private array $data = [];
    public function __construct(array $data)
    {
        if (count($data) > 0) {
            $this->data = $data;
        }
    }
    public function set(string $key, $value): bool
    {
        if (strlen($key) > 0) {
            $this->data[$key] = $value;
            return true;
        }
        return false;
    }
    public function get(string $key){
        $result = array_key_exists($key, $this->data);
        if ($result === false) return;
        return $this->data[$key];
    }
}
