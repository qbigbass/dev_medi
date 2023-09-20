<?php
/**
 * Created by PhpStorm.
 * User: Winer
 * Date: 02.06.2017
 * Time: 16:53
 */



class MeasoftSingleton
{
    /**
     * @var Singleton The reference to *Singleton* instance of this class
     */
    protected static $instance;
    protected $settings;
    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }

    public function setSetting($key,$value){
        $this->settings[$key] = $value;
    }

    public function getSetting($key){
        return $this->settings[$key];
    }
}