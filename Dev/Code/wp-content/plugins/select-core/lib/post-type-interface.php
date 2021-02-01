<?php
namespace QodeCore\Lib;

/**
 * interface PostTypeInterface
 * @package QodeCore\Lib;
 */
interface PostTypeInterface {
    /**
     * @return string
     */
    public function getBase();

    /**
     * Registers custom post type with WordPress
     */
    public function register();
}