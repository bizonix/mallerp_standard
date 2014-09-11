<?php
class Base_model extends CI_Model
{
    protected $CI = NULL;
    protected $offset = 0;
    protected $limit = 20;
    protected $has_set_where = FALSE;
    protected $has_set_sort = FALSE;


    public function  __construct()
    {
        parent::__construct();
        $this->load->database();

        $this->CI = & get_instance();
    }

    public function check_exists($table, $where)
    {
        $this->db->where($where);
        $this->db->from($table);
        
        return $this->db->count_all_results() > 0 ? TRUE : FALSE;
    }

    public function update($table, $where, $data)
    {
        $this->db->where($where);
        $this->db->update($table, $data);
    }

    public function delete($table, $where)
    {
        $this->db->delete($table, $where);
    }

    public function get_one($table, $select, $where, $cache = FALSE, $order_field = NULL, $direction = 'ASC')
    {
        if ($cache)
        {
            $this->db->cache_on();
        }
        $this->db->select("$select");
        $this->db->where($where);
        if ($order_field)
        {
            $this->db->order_by($order_field, $direction);
        }
        $query = $this->db->get($table);
        if ($cache)
        {
            $this->db->cache_off();
        }
        $row = $query->row();

        return isset($row->$select) ? $row->$select : NULL;
    }

    public function get_row($table, $where, $select = '*')
    {
        $this->db->select("$select");
        $this->db->where($where);
        $query = $this->db->get($table);

        return $query->row();
    }

    public function get_row_array($table, $where, $select = '*')
    {
        $this->db->select("$select");
        $this->db->where($where);
        $query = $this->db->get($table);

        return $query->row_array();
    }


    public function get_result($table, $select, $where, $order_by = FALSE, $ASC = TRUE)
    {
        $this->db->select("$select");
        $this->db->where($where);
        if ($order_by)
        {
            $direction = 'ASC';
            if ( ! $ASC)
            {
                $direction = 'DESC';
            }
            $this->db->order_by($order_by, $direction);
        }
        $query = $this->db->get($table);
        $result = $query->result();

        return $result;
    }

    public function get_result_array($table, $select, $where)
    {
        $this->db->select("$select");
        $this->db->where($where);
        $query = $this->db->get($table);
        $result = $query->result_array();
        return $result;
    }
    
    
    public function get_default_id($tabel)
    {
        $this->db->select('id');
        $query = $this->db->get($tabel);
        $row = $query->row();

        return isset($row->id) ? $row->id : '';
    }

    public function count($table, $where = null)
    {
        if ($where)
        {
            $this->db->where($where);
        }
        $this->db->from($table);

        return $this->db->count_all_results();
    }
    
    public function replace($table, $where, $replace_column_name, $column_values, $extra = array())
    {
        $result = $this->get_result($table, "$replace_column_name", $where);

        $all_exist = array();
        foreach ($result as $row)
        {
            $all_exist[] = $row->$replace_column_name;
        }

        // remove old data
        $to_remove = array_diff($all_exist, $column_values);
        if ( ! empty($to_remove))
        {
            $this->db->where($where);
            $this->db->where_in("$replace_column_name", $to_remove);
            $this->db->delete($table);
        }
        // add new data
        $to_add = array_diff($column_values, $all_exist);
        foreach ($to_add as $item)
        {
            $where[$replace_column_name] = $item;
            foreach ($extra as $key => $value)
            {
                $where[$key] = $value;
            }
            $this->db->insert($table, $where);
        }
    }

    public function replace_status($table, $where, $replace_column_name, $column_values, $extra = array())
    {
        $result = $this->get_result($table, "$replace_column_name", array_merge($where, array('status' => 1)));

        $all_exist = array();
        foreach ($result as $row)
        {
            $all_exist[] = $row->$replace_column_name;
        }

        // remove old data
        $to_remove = array_diff($all_exist, $column_values);
        if ( ! empty($to_remove))
        {
            $this->db->where($where);
            $this->db->where_in("$replace_column_name", $to_remove);
            $this->db->update($table, array('status' => 0));
        }
        // add new data
        $to_add = array_diff($column_values, $all_exist);
        foreach ($to_add as $item)
        {
            $where[$replace_column_name] = $item;
            foreach ($extra as $key => $value)
            {
                $where[$key] = $value;
            }
            if ($this->check_exists($table, $where))
            {
                $this->update($table, $where, array('status' => 1));
            }
            else
            {
                $this->db->insert($table, $where);
            }
        }
    }

    public function total($table, $key = NULL)
    {
        $this->db->from($table);
        $this->set_where($key);

        return $this->db->count_all_results();
    }

    public function set_total($total, $key = NULL)
    {
        $this->CI->filter->set_total($total, $key);
    }

    public function set_offset($key = NULL)
    {
        $seg_count = $this->CI->uri->total_segments();
        for ($i = request_uri_count() + 1; $i < $seg_count; $i++)
        {
            $page = $this->CI->uri->segment($i);
            if ($page == 'page')
            {
                $this->offset = $this->CI->uri->segment($i + 1);
                if ( ! is_numeric($this->offset) || $this->offset < 0)
                {
                    $this->offset = 0;
                }
                break;
            }
        }
        if ($this->CI->filter->get_limit($key))
        {
            $this->limit = $this->CI->filter->get_limit($key);
        }
        $this->CI->filter->set_offset($this->offset, $key);
    }

    public function set_sort($key = NULL)
    {
        $sorters = $this->filter->get_sorters($key);
        $this->has_set_sort = FALSE;
        if ($sorters)
        {
            $order_by = implode(',', $sorters);
            $this->db->order_by($order_by);
            $this->has_set_sort = TRUE;
        }
    }
    
    public function set_where($key = NULL, $renames = array())
    {
        $filters = array();
        if ($this->filter->get_filters($key))
        {
            $filters = $this->filter->get_filters($key);
        }

        $i = 0;
                
        foreach ($filters as $key => $value)
        {
            $value = trim($value);
            $i++;
            if ($i % 2 == 0 && $not_null)
            {
                $conds = array();
                foreach ($odd_keys as $odd_key)
                {
                    $odd_key = preg_replace('/.*_MULT_/', '', $odd_key);
                                        
                    // rename the key
                    if (array_key_exists($odd_key, $renames))
                    {
                        $odd_key = $renames[$odd_key];
                    }
                    
                    if (empty($odd_key))
                    {
                        continue;
                    }
                    if ($value == 'LIKE')
                    {
                        $conds[] = "$odd_key LIKE '%$odd_value%'";
                    }
                    else
                    {
                        if (empty($value))
                        {
                            $value = '=';
                        }
                        $conds[] = "$odd_key $value '$odd_value'";
                    }
                }
                $where_str = implode(' OR ', $conds);

                if ( !empty ($where_str))
                {
                    $this->db->where('(' . $where_str . ')');
                }
            }
            else
            {
                $not_null = TRUE;
                if ($value === '')
                {
                    $not_null = FALSE;
                }
                $odd_key = str_replace('_DOT_', '.', $key);
                $odd_keys = explode('_OR_', $odd_key);
                $odd_value = trim($value);                
            }
            $this->has_set_where = TRUE;
        }
    }

    public function ac($table, $key, $value)
    {
        $this->db->select($key);
        $this->db->where(array($key => $value));
        $this->db->or_like($key, $value, 'before');
        $this->db->or_like($key, $value);
        $this->db->limit(20);
        $query = $this->db->get($table);
        $result = $query->result();

        $items = array();
        $i = 0;
        foreach ($result as $item)
        {
            $i++;
            $items[] = $item->$key;
        }

        return $items;
    }

    public function duplicate_row($table, $columns, $where)
    {
        $row = $this->get_row($table, $where);
        
        if ($row)
        {
            $data = array();
            foreach ($columns as $c)
            {
                $data[$c] = $row->$c;
            }
            if (count($data))
            {
                $this->db->insert($table, $data);
                return $this->db->insert_id();
            }
        }

        return NULL;
    }

    public function fetch_statuses($type)
    {
        if (empty($type))
        {
            return $data;
        }
        $result = $this->get_result('status_map', 'status_id, status_name', array('type' => $type), 'status_id');
        
        return $result;
    }

    public function fetch_status_name($type, $status_id)
    {
        return $this->get_one('status_map', 'status_name', array('type' => $type, 'status_id' => $status_id));
    }

    public function fetch_status_id($type, $status_name)
    {
        return $this->get_one('status_map', 'status_id', array('type' => $type, 'status_name' => $status_name));
    }
}
?>
