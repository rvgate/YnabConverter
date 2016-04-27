<?php
class YnabCsvConverter extends stdClass
{
    private $_accounts;

    public function convert($filename)
    {
        /**
         * Convert for each account found in CSV export
         */
        try
        {
            $file_handle = $this->_readImportFile($filename);
            $this->_convertCsv($file_handle);
            fclose($file_handle);
            return $this->_exportAccounts();
        }
        catch (Exception $e)
        {
            print("Failed, with message:\n");
            print("- " . $e->getMessage() . "\n");
            return false;
        }
    }

    private function _readImportFile($filename)
    {
        $handle = fopen($filename, "r");
        if (!$handle)
        {
            throw new Exception("CSV file '{$filename}' could not be read");
        }
        return $handle;
    }

    private function _convertCsv($file_handle)
    {
        $this->_accounts = array();
        while ($line = fgetcsv($file_handle))
        {
            $account_nr = $line[0];
            $this->_accounts[$account_nr][] = $this->_convertLine($line);
        }
    }

    private function _convertLine(array $csv_line)
    {
        return array(
            $this->_convertDate($csv_line[2]),
            trim($this->_getPayee($csv_line)),
            '',
            trim($this->_getDescription($csv_line)),
            $csv_line[3] == 'D' ? $csv_line[4] : '',
            $csv_line[3] == 'C' ? $csv_line[4] : '',
        );
    }

    private function _convertDate($date)
    {
        $year  = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $day   = substr($date, 6, 2);

        return $day . '/' . $month . '/' . $year;
    }

    private function _getPayee(array $csv_line)
    {
        switch ($csv_line[8])
        {
            case "ba": // betaalautomaat
            case "ga": // geldautomaat
                return $csv_line[9] . " - " . $csv_line[10];
                break;
            case "tb": // spaaropdracht?
            case "ei": // europese incasso
            case "cb": // crediteuren betaling (geld ontvangen)
            case "bg": // betaling
            case "db": // betaling aan bank
                return $csv_line[5] . " - " . $csv_line[6];
                break;
            default:
                return $csv_line[5] . " - " . $csv_line[10];
        }
    }

    private function _getDescription(array $parsed_csv_line)
    {
        return $parsed_csv_line[10] . " "
        . $parsed_csv_line[11] . " "
        . $parsed_csv_line[12] . " "
        . $parsed_csv_line[13] . " "
        . $parsed_csv_line[14] . " "
        . $parsed_csv_line[15];
    }

    private function _exportAccounts()
    {
        $files = array();
        foreach ($this->_accounts as $account_nr => $transactions) {
            $files[$account_nr] = implode(PHP_EOL,$this->_exportFile($transactions));
        }
        return $files;
    }

    private function _exportFile(array $transactions)
    {
        $lines = array();
        $lines[] = implode(',', $this->_getFileHeaders());
        foreach ($transactions as $transaction)
        {
            $lines[] = implode(',', $transaction);
        }
        return $lines;
    }

    private function _getFileHeaders()
    {
        return array(
            'Date',
            'Payee',
            'Category',
            'Memo',
            'Outflow',
            'Inflow'
        );
    }
}