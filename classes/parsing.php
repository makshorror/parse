<?php
require "./vendor/autoload.php";

class Parsing
{
    public $data = [];

    //Метод создание строк массива
    private function rowArray($row0, $row1, $row2, $row3)
    {
        $rowData[] = $row0;
        $rowData[] = $row1;
        $rowData[] = $row2;
        $rowData[] = $row3;

        return $rowData;
    }

    //Метод конвертации цены в Int
    private function converToInt($str)
    {
        return intval(str_replace(",", "", $str));
    }

    //Метод сортировки артикулов
    private function articleSort($key)
    {
        return function ($a, $b) use ($key) {
            return $a[$key] <=> $b[$key];
        };
    }

    //Парсинг Excel-файла
    public function parseFile()
    {
        $parsedArray = [];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("./uploads/excel/parser.xls");
        $data = array_slice($spreadsheet->getActiveSheet()->toArray(), 1, -1);

        //Избавление от NULL
        foreach ($data as $row) {
            if ($row['0'] === null && $row['1'] === null && $row['2'] === null && $row['3'] === null) {
                continue;
            } elseif ($row['0'] === null) continue;
            else {
                $row['2'] ? $price = $this->converToInt($row['2']) : $price = 0;
                $parsedArray[] = $this->rowArray($row['0'], $row['1'], $price, $row['3']);
            }
        }
        usort($parsedArray, $this->articleSort(0));
        $this->data = $parsedArray;
        return $parsedArray;
    }

    //Метод сортировки по артикулу
    public function sortByArticle($min, $max)
    {
        $startSlice = 0;
        $endSlice = 0;
        $sortArticleArray = [];
        if ($min != "" && $max != "") {
            for ($i = 0; $i < count($this->data); $i++) {
                if (mb_strtoupper($this->data[$i]['0']) === mb_strtoupper($min)) {
                    $startSlice = $i;
                }
                if (mb_strtoupper($this->data[$i]['0']) === mb_strtoupper($max)) {
                    $endSlice = $i;
                }
            }
            if ($endSlice - $startSlice == 0) {
                foreach ($this->data as $row) {
                    $article = intval($row['0']);
                    $row['2'] ? $price = $this->converToInt($row['2']) : $price = 0;
                    if ($article >= intval($min) && $article <= intval($max)) $sortArticleArray[] = $this->rowArray($row['0'], $row['1'], $price, $row['3']);
                }
            } else {
                $sortArticleArray = array_slice($this->data, $startSlice, $endSlice - ($startSlice - 1));
            }
            $this->data = $sortArticleArray;
            return $sortArticleArray;


        } else {
            foreach ($this->data as $row) {
                $row['2'] ? $price = $this->converToInt($row['2']) : $price = 0;
                if (mb_strtoupper($row['0']) === mb_strtoupper($min) || mb_strtoupper($row['0']) === mb_strtoupper($max)) $sortArticleArray[] = $this->rowArray($row['0'], $row['1'], $price, $row['3']);
            }
        }
        $this->data = $sortArticleArray;
        return $sortArticleArray;
    }

    //Метод сортировки по названию продукта
    public function sortByProductName($productName)
    {
        $sortProductNameArray = [];
        foreach ($this->data as $row) {
            $row['2'] ? $price = $this->converToInt($row['2']) : $price = 0;
            if (str_contains(mb_strtolower($row['1']), mb_strtolower($productName))) $sortProductNameArray[] = $this->rowArray($row['0'], $row['1'], $price, $row['3']);
        }
        $this->data = $sortProductNameArray;
        return $sortProductNameArray;
    }

    //Метод сортировки по цене
    public function sortByPrice($min, $max)
    {
        $min !== "" ? $minPrice = $min : $minPrice = 0;
        $max !== "" ? $maxPrice = $max : $maxPrice = 42550;
        $sortPriceArray = [];

        foreach ($this->data as $row) {
            $row['2'] ? $price = $this->converToInt($row['2']) : $price = 0;
            if ($price >= $minPrice && $price <= $maxPrice) $sortPriceArray[] = $this->rowArray($row['0'], $row['1'], $price, $row['3']);
        }
        $this->data = $sortPriceArray;
        return $sortPriceArray;
    }

    //Метод лимита строк
    public function limitation($limit)
    {
        return $limit <= 0 ? $this->data : $this->data = array_slice($this->data, 0, $limit);
    }
}