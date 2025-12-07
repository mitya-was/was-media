<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 2019-03-25
 * Time: 13:24
 */

namespace Utils;

use Exception;
use Google\Spreadsheet\CellEntry;
use Google\Spreadsheet\CellFeed;
use Google\Spreadsheet\Spreadsheet;
use Google\Spreadsheet\SpreadsheetService;
use Google\Spreadsheet\Worksheet;
use InvalidArgumentException;

class ElectionsSheetsConnector {

    use GoogleUtils;

    const APP_NAME = "LOTTERY";
    const GOOGLE_CREDENTIALS = "client-secret.json";

    const TABLE_NAME = "Lottery";
    const LIST_NAME = "Elections";

    private $data;
    private $errors;
    private $response;

    public function __construct($data) {
        $this->errors = [];
        $this->data = $data;
        $this->response = new Response();

        $this->buildLotteryList();
    }

    private function buildLotteryList() {

        try {
            $this->googleAuthenticate();

            $spreadsheet = (new SpreadsheetService)->getSpreadsheetFeed()->getByTitle(self::TABLE_NAME);

            $this->updateWorksheets($spreadsheet);

            $worksheet = $spreadsheet->getWorksheetByTitle(self::LIST_NAME);

            $this->updateWorksheetData($worksheet);

            $this->response->success();

        } catch (Exception $exception) {
            $this->response->failure();
            $this->response->setError($exception->getMessage());
        }

        echo $this->response->toJSON();

        die();
    }

    /**
     * @param Spreadsheet $spreadsheet
     */
    private function updateWorksheets($spreadsheet) {
        $worksheet = false;

        try {
            $spreadsheet->getWorksheetByTitle(self::LIST_NAME);
            $worksheet = true;
        } catch (Exception $e) {
            $worksheet = false;
        } finally {

            if (!$worksheet) {
                $worksheet = $spreadsheet->addWorksheet(self::LIST_NAME, 1000, 26);

                $this->createCaptions($worksheet->getCellFeed());
            }
        }
    }

    /**
     * @param CellFeed $cell
     */
    private function createCaptions($cell) {
        $presidents = [
            'user_id',
            'vo_ve',
            'i_m',
            'n_m',
            'va_vi',
            'g_m',
            'l_b'
        ];

        for ($i = 1; $i <= count($presidents); $i++) {
            $cell->editCell(1, $i, $presidents[$i - 1]);
        }
    }

    /**
     * @param Worksheet $worksheet
     */
    private function updateWorksheetData($worksheet) {

        if (isset($this->data->vote) && $this->data->vote != "") {
            $vote = $this->data->vote;
        } else {
            throw new InvalidArgumentException("Vote have not been found");
        }

        if (isset($this->data->user_id) && $this->data->user_id != "") {
            $election_user = $this->data->user_id;
        } else {
            throw new InvalidArgumentException("User have not been found");
        }

        $row_num = null;
        $col_num = null;

        $cellFeed = $worksheet->getCellFeed();

        /** @var \Google\Spreadsheet\CellEntry $entry */
        foreach ($cellFeed->getEntries() as $entry) {

            if ($entry->getContent() === $election_user) {
                $row_num = $entry->getRow();
            }

            if ($entry->getContent() === $vote) {
                $col_num = $entry->getColumn();
            }
        }

        if ($row_num && $col_num) {
            $cell = $cellFeed->getCell($row_num, $col_num);
            $cell_val = ($cell) ? $cell->getContent() : 0;

            $cellFeed->editCell($row_num, $col_num, intval($cell_val) + 1);

        } elseif ($col_num && $row_num === null) {
            $empty_row_num = $this->getNextEmptyRow($cellFeed);

            if ($worksheet->getRowCount() >= $empty_row_num) {
                $cellFeed->editCell($empty_row_num, 1, $election_user);
                $cellFeed->editCell($empty_row_num, $col_num, 1);
            } else {
                $spreadsheetData = [];
                $title_row = $worksheet->getListFeed()->getEntries()[0]->getValues();

                foreach ($title_row as $key => $value) {

                    if ($key === "userid") {
                        $spreadsheetData[$key] = $election_user;
                        continue;
                    }

                    if ($key === str_replace("_", "", $vote)) {
                        $spreadsheetData[$key] = 1;
                    } else {
                        $spreadsheetData[$key] = "";
                    }
                }

                $worksheet->getListFeed()->insert($spreadsheetData);
            }
        } else {
            throw new InvalidArgumentException("Unexpected behaviour");
        }
    }

    /**
     * @param CellFeed $cellFeed
     *
     * @return bool|int
     */
    private function getNextEmptyRow($cellFeed) {
        $result = 2;

        /** @var CellEntry $entry */
        foreach ($cellFeed->getEntries() as $entry) {
            $nextEmpty = $entry->getRow() + 1;
            $result = ($nextEmpty > $result) ? $nextEmpty : $result;
        }

        return $result;
    }
}