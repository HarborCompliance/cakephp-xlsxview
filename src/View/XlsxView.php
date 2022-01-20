<?php

declare(strict_types=1);

namespace XlsxView\View;

require 'vendor/autoload.php';

use Cake\View\SerializedView;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class XlsxView extends SerializedView
{
    /**
     * Response type.
     *
     * @var string
     */
    protected $_responseType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    /**
     * Serialize view vars.
     *
     * @param array|string $serialize The name(s) of the view variable(s) that
     *   need(s) to be serialized
     * @return string The serialized data or false.
     */
    protected function _serialize($serialize): string
    {
        try {
            $header = $this->viewVars['header'] ?? null;
            $rows = $this->viewVars['rows'];
            $filename = $this->viewVars['filename'];
            $this->downloadXlsx($header, $rows, $filename);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    protected function downloadXlsx($header = null, $rows, $filename)
    {
        $rows = $this->viewVars['rows'];
        $filename = $this->viewVars['filename'];
        $startRow = 1;

        $spreadsheet = new Spreadsheet();
        if (! empty($header = $this->viewVars['header'])) {
            $spreadsheet->getActiveSheet()->fromArray($header);
            $startRow++;
        }

        foreach ($rows as $row) {
            $startPosition = "A" . $startRow;
            $spreadsheet->getActiveSheet()->fromArray($row->toArray(), null, $startPosition);
            $startRow++;
        }

        if ($spreadsheet) {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            ob_end_clean();
            $writer->save('php://output');
            exit;
        }
    }
}
