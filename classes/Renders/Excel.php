<?php
/**
 * 2017 Soft Industry
 *
 *   @author    Skorobogatko Alexei <a.skorobogatko@soft-industry.com>
 *   @copyright 2017 Soft-Industry
 *   @license   http://opensource.org/licenses/afl-3.0.php
 *   @since     0.1.0
 */

namespace SI\YandexMarket\Renders;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use SI\YandexMarket\Elements\Common;

/**
 * Excel render.
 *
 * @link https://yandex.ru/support/partnermarket/export/excel-format.html
 * @author skoro
 */
class Excel extends CSV
{
    /**
     * @var PHPExcel
     */
    protected $excel;
    
    /**
     * @var PHPExcel_Worksheet
     */
    protected $sheet;
    
    /**
     * @var integer Current row index.
     */
    protected $index;
    
    /**
     * @inheritdoc
     */
    protected function init()
    {
        if (empty($this->options['filename'])) {
            throw new Exception('Option "filename" is required.');
        }

        $this->excel = new PHPExcel();
        $this->sheet = $this->excel->setActiveSheetIndex(0);
        
        $this->index = 1;
    }
    
    /**
     * @inheritdoc
     */
    public function getDefaultOptions()
    {
        return array(
            'filename' => 'php://output',
            'type' => 'Excel2007',
            'header_style' => array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            ),
        );
    }
    
    /**
     * @inheritdoc
     */
    public function renderElement(Common $element)
    {
        parent::renderElement($element);
        
        $col = 0;
        foreach ($this->row as $name => $value) {
            $this->sheet->setCellValueByColumnAndRow($col++, $this->index, $value);
        }
        
        $this->index++;
    }
    
    /**
     * @inheritdoc
     */
    public function flush()
    {
        $headers = $this->getHeaderColumns();
        foreach ($headers as $col => $title) {
            $this->sheet->setCellValueByColumnAndRow($col, 1, $title);
            $this->sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }
        $this->sheet->getStyleByColumnAndRow(0, 1, $col, 1)
                ->applyFromArray($this->options['header_style']);

        return $this->getExcelWriter()->save($this->options['filename']);
    }
    
    /**
     * @inheritdoc
     */
    public function reset()
    {
        parent::reset();
        $this->index = 1;
    }
    
    /**
     * @return PHPExcel_Writer_IWriter
     */
    protected function getExcelWriter()
    {
        return PHPExcel_IOFactory::createWriter($this->excel, $this->options['type']);
    }
}
