<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Clearbricks.
# Copyright (c) 2008 Olivier Meunier and contributors.
# All rights reserved.
#
# Clearbricks is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# Clearbricks is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with Clearbricks; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****

class fileUnzip
{
	protected $file_name;
	protected $compressed_list = array();
	protected $eo_central = array();
	
	protected $zip_sig   = "\x50\x4b\x03\x04"; # local file header signature
	protected $dir_sig   = "\x50\x4b\x01\x02"; # central dir header signature
	protected $dir_sig_e = "\x50\x4b\x05\x06"; # end of central dir signature
	protected $fp = null;
	
	protected $memory_limit = null;
	
	public function __construct($file_name)
	{
		$this->file_name = $file_name;
	}
	
	public function __destruct()
	{
		$this->close();
	}
	
	public function close()
	{
		if ($this->fp) {
			fclose($this->fp);
			$this->fp = null;
		}
		
		if ($this->memory_limit) {
			ini_set('memory_limit',$this->memory_limit);
		}
	}
	
	public function getList($stop_on_file=false,$exclude=false)
	{
		if (!empty($this->compressed_list)) {
			return $this->compressed_list;
		}
		
		if (!$this->loadFileListByEOF($stop_on_file,$exclude)) {
			if(!$this->loadFileListBySignatures($stop_on_file,$exclude)) {
				return false;
			}
		}
		
		return $this->compressed_list;
	}
	
	public function unzipAll($target)
	{
		if (empty($this->compressed_list)) {
			$this->getList();
		}
		
		foreach ($this->compressed_list as $k => $v)
		{
			if ($v['is_dir']) {
				continue;
			}
			
			$this->unzip($k,$target.'/'.$k);
		}
	}
	
	public function unzip($file_name,$target=false)
	{
		if (empty($this->compressed_list)) {
			$this->getList($file_name);
		}
		
		if (!isset($this->compressed_list[$file_name])) {
			throw new Exception(sprintf(__('File %s is not compressed in the zip.'),$file_name));
		}
		$details =& $this->compressed_list[$file_name];
		
		if ($details['is_dir']) {
			throw new Exception(sprintf(__('Trying to unzip a folder name %s'),$file_name));
		}
		
		if (!$details['uncompressed_size']) {
			return $this->putContent('',$target);
		}
		
		if ($target) {
			$this->testTargetDir(dirname($target));
		}
		
		fseek($this->fp(),$details['contents_start_offset']);
		
		$this->memoryAllocate($details['compressed_size']);
		return $this->uncompress(
			fread($this->fp(), $details['compressed_size']),
			$details['compression_method'],
			$details['uncompressed_size'],
			$target
		);
	}
	
	public function getFilesList()
	{
		if (empty($this->compressed_list)) {
			$this->getList();
		}
		
		$res = array();
		foreach ($this->compressed_list as $k => $v) {
			if (!$v['is_dir']) {
				$res[] = $k;
			}
		}
		return $res;
	}
	
	public function getDirsList()
	{
		if (empty($this->compressed_list)) {
			$this->getList();
		}
		
		$res = array();
		foreach ($this->compressed_list as $k => $v) {
			if ($v['is_dir']) {
				$res[] = substr($k,0,-1);
			}
		}
		return $res;
	}
	
	public function getRootDir()
	{
		if (empty($this->compressed_list)) {
			$this->getList();
		}
		
		$files = $this->getFilesList();
		$dirs = $this->getDirsList();
		
		$root_files = 0;
		$root_dirs = 0;
		foreach ($files as $v) { if (strpos($v,'/') === false) { $root_files++; }}
		foreach ($dirs as $v)  { if (strpos($v,'/') === false) { $root_dirs++;  }}
		
		if ($root_files == 0 && $root_dirs == 1) {
			return $dirs[0];
		} else {
			return false;
		}
	}
	
	public function isEmpty()
	{
		if (empty($this->compressed_list)) {
			$this->getList();
		}
		
		return count($this->compressed_list) == 0;
	}
	
	public function hasFile($f)
	{
		if (empty($this->compressed_list)) {
			$this->getList();
		}
		
		return isset($this->compressed_list[$f]);
	}
	
	protected function fp()
	{
		if ($this->fp === null) {
			$this->fp = @fopen($this->file_name,'rb');
		}
		
		if ($this->fp === false) {
			throw new Exception('Unable to open file.');
		}
		
		return $this->fp;
	}
	
	protected function putContent($content,$target=false)
	{
		if ($target) {
			$r = @file_put_contents($target,$content);
			if ($r === false) {
				throw new Exception(__('Unable to write destination file.'));
			}
			files::inheritChmod($target);
			return true;
		}
		return $content;
	}
	
	protected function testTargetDir($dir)
	{
		if (is_dir($dir) && !is_writable($dir)) {
			throw new Exception(__('Unable to write in target directory, permission denied.'));
		}
		
		if (!is_dir($dir)) {
			files::makeDir($dir,true);
		}
	}
	
	protected function uncompress($content,$mode,$size,$target=false)
	{
		switch ($mode)
		{
			case 0:
				# Not compressed
				$this->memoryAllocate($size*2);
				return $this->putContent($content,$target);
			case 1:
				throw new Exception('Shrunk mode is not supported.');
			case 2:
			case 3:
			case 4:
			case 5:
				throw new Exception('Compression factor '.($mode-1).' is not supported.');
			case 6:
				throw new Exception('Implode is not supported.');
			case 7:
				throw new Exception('Tokenizing compression algorithm is not supported.');
			case 8:
				# Deflate
				if (!function_exists('gzinflate')) {
					throw new Exception('Gzip functions are not available.');
				}
				$this->memoryAllocate($size*2);
				return $this->putContent(gzinflate($content,$size),$target);
			case 9:
				throw new Exception('Enhanced Deflating is not supported.');
			case 10:
				throw new Exception('PKWARE Date Compression Library Impoloding is not supported.');
			case 12:
				# Bzip2
				if (!function_exists('bzdecompress')) {
					throw new Exception('Bzip2 functions are not available.');
				}
				$this->memoryAllocate($size*2);
				return $this->putContent(bzdecompress($content),$target);
			case 18:
				throw new Exception('IBM TERSE is not supported.');
			default:
				throw new Exception('Unknown uncompress method');
		}
	}
	
	protected function loadFileListByEOF($stop_on_file=false,$exclude=false)
	{
		$fp = $this->fp();
		
		for ($x=0; $x<1024; $x++)
		{
			fseek($fp,-22-$x,SEEK_END);
			$signature = fread($fp,4);
			
			if ($signature == $this->dir_sig_e)
			{
				$dir_list = array();
				
				$eodir = array(
					'disk_number_this'   => unpack('v', fread($fp,2)),
					'disk_number'        => unpack('v', fread($fp,2)),
					'total_entries_this' => unpack('v', fread($fp,2)),
					'total_entries'      => unpack('v', fread($fp,2)),
					'size_of_cd'         => unpack('V', fread($fp,4)),
					'offset_start_cd'    => unpack('V', fread($fp,4))
				);
				
				$zip_comment_len = unpack('v', fread($fp,2));
				$eodir['zipfile_comment'] = $zip_comment_len[1] ? fread($fp,$zip_comment_len) : '';
				
				$this->eo_central = array(
					'disk_number_this'   => $eodir['disk_number_this'][1],
					'disk_number'        => $eodir['disk_number'][1],
					'total_entries_this' => $eodir['total_entries_this'][1],
					'total_entries'      => $eodir['total_entries'][1],
					'size_of_cd'         => $eodir['size_of_cd'][1],
					'offset_start_cd'    => $eodir['offset_start_cd'][1],
					'zipfile_comment'    => $eodir['zipfile_comment']
				);
				
				fseek($fp, $this->eo_central['offset_start_cd']);
				$signature = fread($fp,4);
				
				while ($signature == $this->dir_sig)
				{
					$dir = array();
					$dir['version_madeby']       = unpack("v",fread($fp, 2)); # version made by
					$dir['version_needed']       = unpack("v",fread($fp, 2)); # version needed to extract
					$dir['general_bit_flag']     = unpack("v",fread($fp, 2)); # general purpose bit flag
					$dir['compression_method']   = unpack("v",fread($fp, 2)); # compression method
					$dir['lastmod_time']         = unpack("v",fread($fp, 2)); # last mod file time
					$dir['lastmod_date']         = unpack("v",fread($fp, 2)); # last mod file date
					$dir['crc-32']               = fread($fp,4);              # crc-32
					$dir['compressed_size']      = unpack("V",fread($fp, 4)); # compressed size
					$dir['uncompressed_size']    = unpack("V",fread($fp, 4)); # uncompressed size
					
					$file_name_len               = unpack("v",fread($fp, 2)); # filename length
					$extra_field_len             = unpack("v",fread($fp, 2)); # extra field length
					$file_comment_len            = unpack("v",fread($fp, 2)); # file comment length
					
					$dir['disk_number_start']    = unpack("v",fread($fp, 2)); # disk number start
					$dir['internal_attributes']  = unpack("v",fread($fp, 2)); # internal file attributes-byte1
					$dir['external_attributes1'] = unpack("v",fread($fp, 2)); # external file attributes-byte2
					$dir['external_attributes2'] = unpack("v",fread($fp, 2)); # external file attributes
					$dir['relative_offset']      = unpack("V",fread($fp, 4)); # relative offset of local header
					$dir['file_name']            = $this->cleanFileName(fread($fp, $file_name_len[1]));          # filename
					$dir['extra_field']          = $extra_field_len[1] ? fread($fp, $extra_field_len[1]) : '';   # extra field
					$dir['file_comment']         = $file_comment_len[1] ? fread($fp, $file_comment_len[1]) : ''; # file comment			
					
					$dir_list[$dir['file_name']] = array(
						'version_madeby'       => $dir['version_madeby'][1],
						'version_needed'       => $dir['version_needed'][1],
						'general_bit_flag'     => str_pad(decbin($dir['general_bit_flag'][1]), 8, '0', STR_PAD_LEFT),
						'compression_method'   => $dir['compression_method'][1],
						'lastmod_datetime'     => $this->getTimeStamp($dir['lastmod_date'][1],$dir['lastmod_time'][1]),
						'crc-32'               => str_pad(dechex(ord($dir['crc-32'][3])), 2, '0', STR_PAD_LEFT).
											 str_pad(dechex(ord($dir['crc-32'][2])), 2, '0', STR_PAD_LEFT).
											 str_pad(dechex(ord($dir['crc-32'][1])), 2, '0', STR_PAD_LEFT).
											 str_pad(dechex(ord($dir['crc-32'][0])), 2, '0', STR_PAD_LEFT),
						'compressed_size'      => $dir['compressed_size'][1],
						'uncompressed_size'    => $dir['uncompressed_size'][1],
						'disk_number_start'    => $dir['disk_number_start'][1],
						'internal_attributes'  => $dir['internal_attributes'][1],
						'external_attributes1' => $dir['external_attributes1'][1],
						'external_attributes2' => $dir['external_attributes2'][1],
						'relative_offset'      => $dir['relative_offset'][1],
						'file_name'            => $dir['file_name'],
						'extra_field'          => $dir['extra_field'],
						'file_comment'         => $dir['file_comment']
					);
					$signature = fread($fp, 4);
				}
				
				foreach ($dir_list as $k => $v)
				{
					if ($exclude && preg_match($exclude,$k)) {
						continue;
					}
					
					$i = $this->getFileHeaderInformation($v['relative_offset']);
					
					$this->compressed_list[$k]['file_name']            = $k;
					$this->compressed_list[$k]['is_dir']               = $v['external_attributes1'] == 16 || substr($k,-1,1) == '/';
					$this->compressed_list[$k]['compression_method']   = $v['compression_method'];
					$this->compressed_list[$k]['version_needed']       = $v['version_needed'];
					$this->compressed_list[$k]['lastmod_datetime']     = $v['lastmod_datetime'];
					$this->compressed_list[$k]['crc-32']               = $v['crc-32'];
					$this->compressed_list[$k]['compressed_size']      = $v['compressed_size'];
					$this->compressed_list[$k]['uncompressed_size']    = $v['uncompressed_size'];
					$this->compressed_list[$k]['lastmod_datetime']     = $v['lastmod_datetime'];
					$this->compressed_list[$k]['extra_field']          = $i['extra_field'];
					$this->compressed_list[$k]['contents_start_offset'] = $i['contents_start_offset'];
					
					if(strtolower($stop_on_file) == strtolower($k)) {
						break;
					}
				}
				return true;
			}
		}
		return false;
	}
	
	protected function loadFileListBySignatures($stop_on_file=false,$exclude=false)
	{
		$fp = $this->fp();
		fseek($fp,0);
		
		$return = false;
		while(true)
		{
			$details = $this->getFileHeaderInformation();
			if (!$details) {
				fseek($fp,12-4,SEEK_CUR); # 12: Data descriptor - 4: Signature (that will be read again)
				$details = $this->getFileHeaderInformation();
			}
			if (!$details) {
				break;
			}
			$filename = $details['file_name'];
			
			if ($exclude && preg_match($exclude,$filename)) {
				continue;
			}
			
			$this->compressed_list[$filename] = $details;
			$return = true;
			
			if (strtolower($stop_on_file) == strtolower($filename)) {
				break;
			}
		}
		
		return $return;
	}
	
	protected function getFileHeaderInformation($start_offset=false)
	{
		$fp = $this->fp();
		
		if ($start_offset !== false) {
			fseek($fp,$start_offset);
		}
		
		$signature = fread($fp, 4);
		if ($signature == $this->zip_sig)
		{
			# Get information about the zipped file
			$file = array();
			$file['version_needed']        = unpack("v",fread($fp, 2)); # version needed to extract
			$file['general_bit_flag']      = unpack("v",fread($fp, 2)); # general purpose bit flag
			$file['compression_method']    = unpack("v",fread($fp, 2)); # compression method
			$file['lastmod_time']          = unpack("v",fread($fp, 2)); # last mod file time
			$file['lastmod_date']          = unpack("v",fread($fp, 2)); # last mod file date
			$file['crc-32']                = fread($fp,4);              # crc-32
			$file['compressed_size']       = unpack("V",fread($fp, 4)); # compressed size
			$file['uncompressed_size']     = unpack("V",fread($fp, 4)); # uncompressed size
			
			$file_name_len                 = unpack("v",fread($fp, 2)); # filename length
			$extra_field_len               = unpack("v",fread($fp, 2)); # extra field length
			
			$file['file_name']             = $this->cleanFileName(fread($fp,$file_name_len[1])); # filename
			$file['extra_field']           = $extra_field_len[1] ? fread($fp, $extra_field_len[1]) : ''; # extra field
			$file['contents_start_offset'] = ftell($fp);
			
			# Look for the next file
			fseek($fp, $file['compressed_size'][1], SEEK_CUR);
			
			# Mount file table
			$i = array(
				'file_name'            => $file['file_name'],
				'is_dir'               => substr($file['file_name'],-1,1) == '/',
				'compression_method'   => $file['compression_method'][1],
				'version_needed'       => $file['version_needed'][1],
				'lastmod_datetime'     => $this->getTimeStamp($file['lastmod_date'][1],$file['lastmod_time'][1]),
				'crc-32'               => str_pad(dechex(ord($file['crc-32'][3])), 2, '0', STR_PAD_LEFT).
									 str_pad(dechex(ord($file['crc-32'][2])), 2, '0', STR_PAD_LEFT).
									 str_pad(dechex(ord($file['crc-32'][1])), 2, '0', STR_PAD_LEFT).
									 str_pad(dechex(ord($file['crc-32'][0])), 2, '0', STR_PAD_LEFT),
				'compressed_size'      => $file['compressed_size'][1],
				'uncompressed_size'    => $file['uncompressed_size'][1],
				'extra_field'          => $file['extra_field'],
				'general_bit_flag'     => str_pad(decbin($file['general_bit_flag'][1]), 8, '0', STR_PAD_LEFT),
				'contents_start_offset'=>$file['contents_start_offset']
			);
			return $i;
		}
		return false;
	}
	
	protected function getTimeStamp($date,$time)
	{
		$BINlastmod_date = str_pad(decbin($date), 16, '0', STR_PAD_LEFT);
		$BINlastmod_time = str_pad(decbin($time), 16, '0', STR_PAD_LEFT);
		$lastmod_dateY   = bindec(substr($BINlastmod_date,  0, 7))+1980;
		$lastmod_dateM   = bindec(substr($BINlastmod_date,  7, 4));
		$lastmod_dateD   = bindec(substr($BINlastmod_date, 11, 5));
		$lastmod_timeH   = bindec(substr($BINlastmod_time,   0, 5));
		$lastmod_timeM   = bindec(substr($BINlastmod_time,   5, 6));
		$lastmod_timeS   = bindec(substr($BINlastmod_time,  11, 5)) * 2;
		
		return mktime($lastmod_timeH, $lastmod_timeM, $lastmod_timeS, $lastmod_dateM, $lastmod_dateD, $lastmod_dateY);
	}
	
	protected function cleanFileName($n)
	{
		$n = str_replace('../','',$n);
		$n = preg_replace('#^/+#','',$n);
		return $n;
	}
	
	protected function memoryAllocate($size)
	{
		$mem_used = function_exists('memory_get_usage') ? @memory_get_usage() : 4000000;
		$mem_limit = @ini_get('memory_limit');
		if ($mem_used && $mem_limit)
		{
			$mem_limit = files::str2bytes($mem_limit);
			$mem_avail = $mem_limit-$mem_used-(512*1024);
			$mem_needed = $size;
			
			if ($mem_needed > $mem_avail)
			{
				if (@ini_set('memory_limit',$mem_limit+$mem_needed+$mem_used) === false) {
					throw new Exception(__('Not enough memory to open file.'));
				}
				
				if (!$this->memory_limit) {
					$this->memory_limit = $mem_limit;
				}
			}
		}
	}
}
?>