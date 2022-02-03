<?php

declare(strict_types=1);

namespace XlsxView\View;

use Cake\Datasource\EntityInterface;
use Cake\View\SerializedView;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class XlsxView extends SerializedView
{
    /**
     * Response type.
     *
     * @var string
     */
    protected $_responseType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    /**
     * Default config options.
     *
     * Use ViewBuilder::setOption()/setOptions() in your controlle to set these options.
     *
     * - `serialize`: Option to convert a set of view variables into a serialized response.
     *   Its value can be a string for single variable name or array for multiple
     *   names. If true all view variables will be serialized. If null or false
     *   normal view template will be rendered.
     * - `header`: A flat array of header column names.
     *
     * @var array<string, mixed>
     */
    protected $_defaultConfig = [
        'serialize' => null,
        'header' => null,
    ];

    /**
     * Serialize view vars.
     *
     * @param array|string $serialize The name(s) of the view variable(s) that
     *   need(s) to be serialized
     * @return string The serialized data.
     */
    protected function _serialize($serialize): string
    {
        $spreadsheet = new Spreadsheet();
        $activeSheet = $spreadsheet->getActiveSheet();

        $currentRow = 1;

        if (is_array($this->getConfig('header'))) {
            $activeSheet->fromArray($this->getConfig('header'), null, 'A' . $currentRow++);
        }

        foreach ($this->viewVars[$serialize] as $data) {
            if ($data instanceof EntityInterface) {
                $data = $data->toArray();
            }

            $activeSheet->fromArray($data, null, 'A' . $currentRow++);
        }

        // PHPOffice only allows writing files to disk
        // This is a hacky work-around to get the file contents
        $tmpFilename = sprintf('%sxlsxview-%d.xlsx', TMP, microtime(true) * 10000);
        $writer = new Xlsx($spreadsheet);
        $writer->save($tmpFilename);
        $content = file_get_contents($tmpFilename);
        unlink($tmpFilename);

        return $content;
    }
}
