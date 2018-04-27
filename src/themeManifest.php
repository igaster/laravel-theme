<?php namespace Igaster\LaravelTheme;

class themeManifest
{

    protected $data = [];

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function get($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    public function has($key)
    {
        return isset($this->data[$key]);
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function remove($key)
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
        }
    }

    public function loadData($data = [])
    {
        $this->data = $data;
    }

    public function validate()
    {
        return true;
        // throw new \Exception("Invalid data");
    }

    public function loadFromFile($filename)
    {
        $json = file_get_contents($filename);
        $data = json_decode($json, true);
        if ($data === null) {
            throw new \Exception("Invalid theme.json file [$filename]");
        }
        $this->data = $data;
    }

    public function saveToFile($filename)
    {
        file_put_contents($filename, json_encode($this->data, JSON_PRETTY_PRINT));
    }

}
