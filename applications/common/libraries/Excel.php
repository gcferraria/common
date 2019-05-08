<?php

/**
 * @category   Excel
 * @package    CodeIgniter 3.0
 * @subpackage PHP Excel
 * @version    1.0
 * @license    http://www.gnu.org/licenses/     GNU General Public License
 */
defined('BASEPATH') OR exit('No direct script access allowed');

require_once HOMEPATH . "applications/common/third_party/phpexcel/PHPExcel.php";

class Excel extends PHPExcel { 

    public function __construct() 
    {
        parent::__construct();
    }
}