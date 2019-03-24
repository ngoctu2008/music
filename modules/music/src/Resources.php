<?php

/**
 * @Project NUKEVIET MUSIC 4.X
 * @Author PHAN TAN DUNG <phantandung92@gmail.com>
 * @Copyright (C) 2016 PHAN TAN DUNG. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sun, 26 Feb 2017 14:04:32 GMT
 */

namespace NukeViet\Music;

/**
 * Các tài nguyên của hệ thống NukeViet cần có để module hoạt động
 * Load các tài nguyên này trước khi module có thể hoạt động
 *
 * @since 4.3.00
 */
class Resources implements Settings
{
    /**
     * Ngôn ngữ giao diện đang xử lý.
     *
     * @var string
     */
    private static $langInterface = 'vi';

    /**
     * Ngôn ngữ CSDL đang xử lý.
     *
     * @var string
     */
    private static $langData = 'vi';

    /**
     * Ngôn ngữ CSDL đang xử lý.
     *
     * @var object
     */
    private static $db = null;

    /**
     * Đầu tố bảng dữ liệu của module.
     *
     * @var string
     */
    private static $tablePrefix = '';

    /**
     * Đầu tố bảng dữ liệu của csdl site.
     *
     * @var string
     */
    private static $dbPrefix = '';

    /**
     * Resources::setLangInterface()
     *
     * @param mixed $lang
     * @return
     */
    public static function setLangInterface($lang)
    {
        self::$langInterface = $lang;

        return true;
    }

    /**
     * Resources::setLangData()
     *
     * @param mixed $lang
     * @return
     */
    public static function setLangData($lang)
    {
        self::$langData = $lang;

        return true;
    }

    /**
     * Resources::setDb()
     *
     * @param mixed $db
     * @return
     */
    public static function setDb($db)
    {
        self::$db = $db;

        return true;
    }

    /**
     * Resources::setTablePrefix()
     *
     * @param mixed $table
     * @return
     */
    public static function setTablePrefix($table)
    {
        self::$tablePrefix = $table;

        return true;
    }

    /**
     * @param string $prefix
     * @return boolean
     */
    public static function setDbPrefix($prefix)
    {
        self::$dbPrefix = $prefix;

        return true;
    }

    /**
     * Resources::getLangInterface()
     *
     * @return
     */
    public static function getLangInterface()
    {
        return self::$langInterface;
    }

    /**
     * Resources::getLangData()
     *
     * @return
     */
    public static function getLangData()
    {
        return self::$langData;
    }

    /**
     * Resources::getDb()
     *
     * @return
     */
    public static function getDb()
    {
        return self::$db;
    }

    /**
     * Resources::getTablePrefix()
     *
     * @return
     */
    public static function getTablePrefix()
    {
        return self::$tablePrefix;
    }

    /**
     * @return string
     */
    public static function getDbPrefix()
    {
        return self::$dbPrefix;
    }
}