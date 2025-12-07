<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 7/24/2017
 * Time: 15:01
 */

namespace Utils;

use Exception;
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;
use Google\Spreadsheet\SpreadsheetService;
use Google_Client;

class GoogleSheetsConnector {

    use GoogleUtils;

    const APP_NAME = "LOTTERY";
    const GOOGLE_CREDENTIALS = "client-secret.json";

    const TABLE_NAME = "Lottery";

    private $post;

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

            if (isset($_GET["slug"]) && $_GET["slug"] != "") {
                $this->post = get_post(url_to_postid($_GET['slug']));

                if (!$this->post) {
                    throw new Exception("Post have not been found");
                }
            }

            $this->googleAuthenticate();

            $spreadsheet = (new SpreadsheetService)->getSpreadsheetFeed()->getByTitle(self::TABLE_NAME);

            $this->updateWorksheets($spreadsheet);
            $this->updateWorksheetData($spreadsheet);

            $this->response->success();

        } catch (Exception $exception) {
            $this->response->failure();
            $this->response->setError($exception->getMessage());
        }

        echo $this->response->toJSON();

        die();
    }

    /**
     * @param \Google\Spreadsheet\Spreadsheet $spreadsheet
     */
    private function updateWorksheets($spreadsheet) {
        $worksheet = false;

        try {
            $spreadsheet->getWorksheetByTitle($this->post->post_title);
            $worksheet = true;
        } catch (Exception $e) {
            $worksheet = false;
        } finally {

            if (!$worksheet) {
                $worksheet = $spreadsheet->addWorksheet($this->post->post_title);

                $this->createCaptions($worksheet->getCellFeed());
            }
        }
    }

    /**
     * @param \Google\Spreadsheet\Spreadsheet $spreadsheet
     *
     * @throws \Google\Spreadsheet\Exception\WorksheetNotFoundException
     * @throws Exception
     */
    private function updateWorksheetData($spreadsheet) {
        $insert = false;
        $worksheet = $spreadsheet->getWorksheetByTitle($this->post->post_title);
        $cellFeed = $worksheet->getCellFeed();
        $emptyRowNum = $this->getNextEmptyRow($cellFeed);
        $dataArray = array_values((array)$this->data);

        if ($emptyRowNum) {

            for ($c = 1; $c <= count($dataArray); $c++) {

                if ($worksheet->getRowCount() >= $emptyRowNum) {
                    $cellFeed->editCell($emptyRowNum, $c, $dataArray[$c - 1]);
                } else {
                    $insert = true;
                    break;
                }
            }

            if (!$insert){
                $cellFeed->editCell($emptyRowNum, $c, "http://graph.facebook.com/".$dataArray[$c - 2]."/picture");
            }

            if ($insert) {
                $spreadsheetData = [];

                foreach ($this->data as $key => $value) {
                    $cellName = mb_strtolower($key);
                    $spreadsheetData[$cellName] = "'" . $value;
                }

                if (count($spreadsheetData) > 0) {

                    $worksheet->getListFeed()->insert($spreadsheetData);
                }
            }
        }
    }

    /**
     * @param \Google\Spreadsheet\CellFeed $cell
     */
    private function createCaptions($cell) {
        $dataArray = array_keys((array)$this->data);

        for ($i = 1; $i <= count($dataArray); $i++) {
            $cell->editCell(1, $i, $dataArray[$i - 1]);
        }
    }

    /**
     * @param \Google\Spreadsheet\CellFeed $cellFeed
     *
     * @return bool|int
     * @throws Exception
     */
    private function getNextEmptyRow($cellFeed) {
        $dataArray = array_values((array)$this->data);
        $isDuplicate = false;
        $result = 2;

        /** @var \Google\Spreadsheet\CellEntry $entry */
        foreach ($cellFeed->getEntries() as $entry) {

            foreach ($dataArray as $item) {

                if (intval($item) > 0 && $entry->getContent() == $item) {
                    $isDuplicate = true;
                }
            }

            $nextEmpty = $entry->getRow() + 1;
            $result = ($nextEmpty > $result) ? $nextEmpty : $result;
        }

        if ($isDuplicate) {
            throw new Exception("Entry duplicate");
        } else {
            return $result;
        }
    }
}