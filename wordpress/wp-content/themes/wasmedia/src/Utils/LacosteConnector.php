<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov
 * Date: 7/24/2017
 * Time: 15:01
 */

namespace Utils;

use Exception;
use Google\Spreadsheet\SpreadsheetService;
use Google\Spreadsheet\Worksheet;
use InvalidArgumentException;
use Throwable;

class LacosteConnector {

    use GoogleUtils;

    const TABLE_NAME = "Lacoste";
    const LIST_NAME = "Lacoste";

    private $data;
    private $response;

	public function __construct($data) {
		$this->data = $data;

		$this->response = new Response();

        try {
            $this->googleAuthenticate();
	        $spreadsheetService = new SpreadsheetService();

	        $spreadsheet = $spreadsheetService->getSpreadsheetFeed()->getByTitle(self::TABLE_NAME);

	        $this->updateWorksheets($spreadsheet);

            $worksheet = $spreadsheet->getWorksheetByTitle(self::LIST_NAME);
            $cellFeed = $worksheet->getCellFeed();

	        $result = $this->updateWorksheetData($cellFeed);

			$this->response->setData($result);
            $this->response->success();
        } catch (Throwable $exception) {
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
            $spreadsheet->getWorksheetByTitle(self::LIST_NAME);
            $worksheet = true;
        } catch (Exception $e) {
            $worksheet = false;
        } finally {

            if (!$worksheet) {
                $worksheet = $spreadsheet->addWorksheet(self::LIST_NAME, 1000, 50);
                $cellFeed = $worksheet->getCellFeed();

                $this->createPublicCaptions($cellFeed);
            }
        }
    }


    /**
     * @param \Google\Spreadsheet\CellFeed $cellFeed
     */
    private function createPublicCaptions($cellFeed) {
	    $cellFeed->editCell(1, 1, "id");
    }

	/**
	 * @param \Google\Spreadsheet\CellFeed $cellFeed
	 *
	 * @throws InvalidArgumentException
	 */
	private function updateWorksheetData($cellFeed) {

		if (!isset($this->data) || $this->data == '') {
			throw new InvalidArgumentException("Data have not been found");
		}

		$row_nums = null;
		$row_count = 0;

		foreach ($cellFeed->getEntries() as $entry) {
			if ($entry->getContent() === $this->data['id']) {
				$row_nums[] = $entry->getRow();
			}
			$row_count = $entry->getRow();
		}
		if (count($row_nums) > 1) {
			foreach ($row_nums as $row_num){
				if(!$cellFeed->getCell($row_num, 4) || !$cellFeed->getCell($row_num, 4) ){
					continue;
				}
//				if ($this->data['cardName'] && ($cellFeed->getCell($row_num, 4)->getContent() == $this->data['cardName'] || $cellFeed->getCell($row_num, 5)->getContent() == $this->data['nameTo'])){
//					return 'exists';
//				}
			}
			if ($this->data['cardName'] != ''){
				$this->addRow($cellFeed, $row_count + 1);
			} else {
				return 'login';
			}
		} elseif(count($row_nums) == 1) {
			if (!$cellFeed->getCell($row_nums[0], 4)) {
				$this->addRow($cellFeed, $row_nums[0]);
			} else {
				$this->addRow($cellFeed, $row_count + 1);
			}
		} else {
			$this->addRow($cellFeed, $row_count + 1);
		}
	}

	private function addRow($cellFeed, $row_num){
		$cellFeed->editCell($row_num, 1, $this->data['id']);
		$cellFeed->editCell($row_num, 2, $this->data['name']);
		$cellFeed->editCell($row_num, 3, $this->data['email']);
		$cellFeed->editCell($row_num, 4, $this->data['cardName'] ? "https://was.media/wp-content/uploads/static/lacoste-dress-up-6/snippets/" . $this->data['cardName'].'.png' : '');
		$cellFeed->editCell($row_num, 5, $this->data['nameTo']);
		$cellFeed->editCell($row_num, 6, $this->data['picture']);
		return 'added';
	}
}