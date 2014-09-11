<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Cache_file extends Cache_file {
	/**
	 * Batch delete from Cache
	 *
	 * @param 	mixed		unique identifier of item in cache
	 */
	public function delete_by_tag($id)
	{
        $files = directory_map($this->_cache_path, 1);
        foreach ($files as $file)
        {
            if (strpos($file, $id) === 0)
            {
                unlink($this->_cache_path.$file);
            }
        }
	}
}