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

class fileZip
{
	protected $entries = array();
	protected $root_dir = null;
	
	protected $ctrl_dir = array();
	protected $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
	protected $old_offset = 0;
	
	protected $fp;
	protected $memory_limit = null;
	
	protected $exclusions = array();
	
	public function __construct($out_fp)
	{
		if (!is_resource($out_fp)) {
			throw new Exception('Output file descriptor is not a resource');
		}
		
		if (!in_array(get_resource_type($out_fp),array('stream','file'))) {
			throw new Exception('Output file descriptor is not a valid resource');
		}
		
		$this->fp = $out_fp;
	}
	
	public function __destruct()
	{
		$this->close();
	}
	
	public function close()
	{
		if ($this->memory_limit) {
			ini_set('memory_limit',$this->memory_limit);
		}
	}
	
	public function addExclusion($reg)
	{
		$this->exclusions[] = $reg;
	}
	
	public function addFile($file,$name=null)
	{
		$file = preg_replace('#[\\\/]+#','/',$file);
		
		if (!$name) {
			$name = $file;
		}
		$name = $this->formatName($name);
		
		if ($this->isExcluded($name)) {
			return;
		}
		
		if (!file_exists($file) || !is_file($file)) {
			throw new Exception(__('File does not exist'));
		}
		if (!is_readable($file)) {
			throw new Exception(__('Cannot read file'));
		}
		
		$info = stat($file);
		
		$this->entries[$name] = array(
			'file'   => $file,
			'is_dir' => false,
			'mtime'  => $info['mtime'],
			'size'   => $info['size']
		);
	}
	
	public function addDirectory($dir,$name=null,$recursive=false)
	{
		$dir = preg_replace('#[\\\/]+#','/',$dir);
		if (substr($dir,-1-1) != '/') {
			$dir .= '/';
		}
		
		if (!$name && $name !== '') {
			$name = $dir;
		}
		
		if ($this->isExcluded($name)) {
			return;
		}
		
		if ($name !== '')
		{
			if (substr($name,-1,1) != '/') {
				$name .= '/';
			}
			
			$name = $this->formatName($name);
			
			if ($name !== '')
			{
				$this->entries[$name] = array(
					'file'   => null,
					'is_dir' => true,
					'mtime'  => time(),
					'size'   => 0
				);
			}
		}
		
		if ($recursive)
		{
			if (!is_dir($dir)) {
				throw new Exception(__('Directory does not exist'));
			}
			if (!is_readable($dir)) {
				throw new Exception(__('Cannot read directory'));
			}
			
			$D = dir($dir);
			while (($e = $D->read()) !== false) {
				if ($e == '.' || $e == '..') {
					continue;
				}
				
				if (is_dir($dir.'/'.$e)) {
					$this->addDirectory($dir.$e,$name.$e,true);
				} elseif (is_file($dir.'/'.$e)) {
					$this->addFile($dir.$e,$name.$e);
				}
			}
		}
	}
	
	public function write()
	{
		foreach ($this->entries as $name => $v)
		{
			if ($v['is_dir']) {
				$this->writeDirectory($name);
			} else {
				$this->writeFile($name,$v['file'],$v['size'],$v['mtime']);
			}
		}
		
		$ctrldir = implode('',$this->ctrl_dir);
		
		fwrite($this->fp,
			$ctrldir.
			$this->eof_ctrl_dir.
			pack('v',sizeof($this->ctrl_dir)).	# total # of entries "on this disk"
			pack('v',sizeof($this->ctrl_dir)).	# total # of entries overall
			pack('V',strlen($ctrldir)).		# size of central dir
			pack('V',$this->old_offset).		# offset to start of central dir
			"\x00\x00"					# .zip file comment length
		);
	}
	
	protected function writeDirectory($name)
	{
		if (!isset($this->entries[$name])) {
			return;
		}
		
		$mdate = $this->makeDate(time());
		$mtime = $this->makeTime(time());
		
		# Data descriptor
		$data_desc =
		"\x50\x4b\x03\x04".
		"\x0a\x00".			# ver needed to extract
		"\x00\x00".			# gen purpose bit flag
		"\x00\x00".			# compression method
		pack('v',$mtime).		# last mod time
		pack('v',$mdate).		# last mod date
		pack('V',0).			# crc32
		pack('V',0).			# compressed filesize
		pack('V',0).			# uncompressed filesize
		pack('v',strlen($name)). # length of pathname
		pack('v',0). 			# extra field length
		$name.				# end of "local file header" segment
		pack('V',0).	 		# crc32
		pack('V',0).			# compressed filesize
		pack('V',0);			# uncompressed filesize
		
		$new_offset = $this->old_offset + strlen($data_desc);
		fwrite($this->fp,$data_desc);
		
		# Add to central record
		$cdrec =
		"\x50\x4b\x01\x02".
		"\x00\x00".				# version made by	
		"\x0a\x00".				# version needed to extract
		"\x00\x00".				# gen purpose bit flag
		"\x00\x00".				# compression method
		pack('v',$mtime).			# last mod time
		pack('v',$mdate).			# last mod date
		pack('V',0).				# crc32
		pack('V',0).				# compressed filesize
		pack('V',0).				# uncompressed filesize
		pack('v',strlen($name)). 	# length of filename
		pack('v',0).				# extra field length
		pack('v',0).				# file comment length
		pack('v',0).				# disk number start
		pack('v',0).				# internal file attributes
		pack('V',16).				# external file attributes  - 'directory' bit set
		pack('V',$this->old_offset).	# relative offset of local header
		$name;
		
		$this->old_offset = $new_offset;
		$this->ctrl_dir[] = $cdrec;
	}
	
	protected function writeFile($name,$file,$size,$mtime)
	{
		if (!isset($this->entries[$name])) {
			return;
		}
		
		$size = filesize($file);
		$this->memoryAllocate($size*3);
		
		$content = file_get_contents($file);
		
		$unc_len	= strlen($content);
		$crc		= crc32($content);
		$zdata	= gzdeflate($content);
		$c_len	= strlen($zdata);
		
		unset($content);
		
		$mdate = $this->makeDate($mtime);
		$mtime = $this->makeTime($mtime);
		
		# Data descriptor
		$data_desc =
		"\x50\x4b\x03\x04".
		"\x14\x00".			# ver needed to extract
		"\x00\x00".			# gen purpose bit flag
		"\x08\x00".			# compression method
		pack('v',$mtime).		# last mod time
		pack('v',$mdate).		# last mod date
		pack('V',$crc).		# crc32
		pack('V',$c_len).		# compressed filesize
		pack('V',$unc_len).		# uncompressed filesize
		pack('v',strlen($name)).	# length of filename
		pack('v',0).			# extra field length
		$name.				# end of "local file header" segment
		$zdata.				# "file data" segment
		pack('V',$crc).		# crc32
		pack('V',$c_len).		# compressed filesize
		pack('V',$unc_len);		# uncompressed filesize
		
		fwrite($this->fp,$data_desc);
		unset($zdata);
		
		$new_offset = $this->old_offset + strlen($data_desc);
		
		# Add to central directory record
		$cdrec =
		"\x50\x4b\x01\x02".
		"\x00\x00".				# version made by
		"\x14\x00".				# version needed to extract
		"\x00\x00".				# gen purpose bit flag
		"\x08\x00".				# compression method
		pack('v',$mtime).			# last mod time
		pack('v',$mdate).			# last mod date
		pack('V',$crc).			# crc32
		pack('V',$c_len).			# compressed filesize
		pack('V',$unc_len).			# uncompressed filesize
		pack('v',strlen($name)).		# length of filename
		pack('v',0).				# extra field length
		pack('v',0).				# file comment length
		pack('v',0).				# disk number start
		pack('v',0).				# internal file attributes
		pack('V',32).				# external file attributes - 'archive' bit set
		pack('V',$this->old_offset).	# relative offset of local header
		$name;

		$this->old_offset = $new_offset;
		$this->ctrl_dir[] = $cdrec;
	}
	
	protected function formatName($name)
	{
		if (substr($name,0,1) == '/') {
			$name = substr($name,1);
		}
		
		return $name;
	}
	
	protected function isExcluded($name)
	{
		foreach ($this->exclusions as $reg) {
			if (preg_match($reg,$name)) {
				return true;
			}
		}
		
		return false;
	}
	
	protected function makeDate($ts)
	{
		$year = date('Y',$ts)-1980;
		if ($year < 0) {
			$year = 0;
		}
		
		$year = sprintf('%07b',$year);
		$month = sprintf('%04b',date('n',$ts));
		$day = sprintf('%05b',date('j',$ts));
		
		return bindec($year.$month.$day);
	}
	
	protected function makeTime($ts)
	{
		$hour = sprintf('%05b',date('G',$ts));
		$minute = sprintf('%06b',date('i',$ts));
		$second = sprintf('%05b',ceil(date('s',$ts)/2));
		
		return bindec($hour.$minute.$second);
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