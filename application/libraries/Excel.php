<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Excel {

    public function query_to_excel($query, $head = array(), $filename='exceloutput')
    {
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below

        $obj = & get_instance();

        if ($query->num_rows() == 0)
        {
            echo '<p>The table appears to have no data.</p>';
        } 
        else
        {
            if (! empty($head))
            {
                foreach ($head as $title)
                {
                    $headers .= $title . "\t";
                }
            }
            else
            {
                $fields = $query->field_data();
                foreach ($fields as $field)
                {
                    $headers .= $field->name . "\t";
                }
            }

            foreach ($query->result() as $row)
            {
                $line = '';
                foreach ($row as $value)
                {
                    if ((!isset($value)) OR ($value == ""))
                    {
                        $value = "\t";
                    } else
                    {
                        $value = str_replace('"', '""', $value);
                        $value = '"' . $value . '"' . "\t";
                    }
                    $line .= $value;
                }
                $data .= trim($line) . "\n";
            }

            $data = str_replace("\r", "", $data);

            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=$filename.xls");
            echo "$headers\n$data";
        }
    }

    public function array_to_excel($array, $head = array(), $filename='exceloutput')
    {
        $headers = ''; // just creating the var for field headers to append to below
        $data = ''; // just creating the var for field data to append to below

        $obj = & get_instance();

        if ( ! empty($head))
        {
            foreach ($head as $title)
            {
                $headers .= $title . "\t";
            }
        }

        foreach ($array as $row)
        {
            $line = '';
            foreach ($row as $value)
            {
                if ((!isset($value)) OR ($value == ""))
                {
                    $value = "\t";
                }
                else
                {
                    $value = str_replace('"', '""', $value);
                    $value = '"' . $value . '"' . "\t";
                }
                $line .= $value;
            }
            $data .= trim($line) . "\n";
        }

        $data = str_replace("\r", "", $data);

        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$filename.xls");
        echo "$headers\n$data";
    }
    
    public function csv_to_array($file)
    {
        setlocale(LC_ALL, 'en_US.UTF8');
        $result = array();
        $handle = fopen($file,"r");
        while ($row = fgetcsv($handle)) {
            $result[] = $row;
        }
        fclose($handle);

        return $result;
    }

}
