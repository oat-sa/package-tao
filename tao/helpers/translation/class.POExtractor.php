<?php
/**  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class tao_helpers_translation_RDFExtractor
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
class tao_helpers_translation_POExtractor
    extends tao_helpers_translation_RDFExtractor
{

	/**
	 * @param $child
	 * @param $xmlNS
	 * @param $about
	 * @param $tus
	 * @return array
	 */
	protected function processUnit($child, $xmlNS, $about, $tus)
	{
		if ($child->hasAttributeNS($xmlNS, 'lang')) {
			$sourceLanguage = 'en-US';
			$targetLanguage = $child->getAttributeNodeNS($xmlNS, 'lang')->value;
			$source = $child->nodeValue;
			$target = '';//$child->nodeValue;

			$tu = new tao_helpers_translation_POTranslationUnit();
			$tu->setSource($source);
			$tu->setTarget($target);
			$tu->setSourceLanguage($sourceLanguage);
			$tu->setTargetLanguage($targetLanguage);
			$tu->setAnnotations(array($about));
			$tu->setContext($child->namespaceURI . $child->localName);

			$tus[] = $tu;
			return $tus;
		}
		return $tus;
	}

}