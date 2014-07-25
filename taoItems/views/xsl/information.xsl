<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns="http://www.w3.org/1999/xhtml" >

	<xsl:output
		method="xml"
		version="1.0"
		encoding="utf-8"
		indent="yes"
		omit-xml-declaration="yes"/>

	<!-- list -->
	<xsl:template
		match="item"
		mode="information">
			<div id="information">
					<xsl:apply-templates select="subject" mode="information" />
			</div>
	</xsl:template>

	<!-- question -->
	<xsl:template
		match="subject"
		mode="information">
		<div class="information">
			<xsl:value-of disable-output-escaping="yes" select="."/>
		</div>
	</xsl:template>

</xsl:stylesheet>