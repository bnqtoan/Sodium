<?php

namespace Sodium\Model\Aggregate;

use Sodium\Autoloader;
use Sodium\Config;
use Sodium\Exception;
use Sodium\Library\PsdReader\PsdReader;
use Sodium\Sodium;

class Psd Extends AbstractAggregate implements AggregateInterface
{

    public static $is_exportable = FALSE;
    protected $_filename;
    protected $_colors = array();
    private $_image_prefix_name = 'Temp_Psd_File';

    const IMG_FORMAT = 'png';

    public function __construct($psd = '')
    {
        if ($psd != '')
            $this->_filename = $this->_format($psd);
    }

    protected function _format($string)
    {

        $type = self::isValid($string, TRUE);
        switch ($type) {
            case 'psd':
                $string = ltrim($string, 'psd');
                $string = ltrim($string, '(');
                $string = rtrim($string, ')');
                $value = $string;
                break;

            default:
                $value = '';
        }
        if (!file_exists($value))
            throw new Exception('Psd file ' . realpath($value) . ' not exists');
        return $value;
    }

    public static function regex()
    {

        $regex['psd'] = '/^psd\(.*\)$/i';
        return $regex;
    }

    public function getDefaultOutput()
    {
        return array();
    }

    public function getStandardOutput()
    {
        return 'psd()';
    }

    public function getCollection()
    {
        $image = $this->_init_image();
        return $image->getCollection();
    }

    public function getCollectionCount()
    {
        return count($this->getCollection());
    }

    public function getCollectionObj()
    {
        return new Sodium($this->getCollection());
    }

    private function _init_image()
    {
        $image = new PsdReader($this->_filename);
        $image->saveImage($this->_file_path());
        return new Image('img(' . $this->_file_path() . ')');
    }

    private function _file_path()
    {
        $path = Config::get('Default_Path', 'Image');
        return $base_dir = Autoloader::baseDir() . '/' . $path . $this->_image_prefix_name . '.' . self::IMG_FORMAT;
    }

}
