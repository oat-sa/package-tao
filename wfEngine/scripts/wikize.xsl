<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- output mode  -->	
<xsl:output omit-xml-declaration="yes" indent="no"/>

<xsl:template match="/">
	<xsl:apply-templates />
</xsl:template>

<!-- section title -->
<xsl:template match="description">
==== <xsl:value-of select="." /> ====
</xsl:template>

<!-- content block by service -->
<xsl:template match="service">
 * **<xsl:value-of select="name" />**
   * //Description//:<xsl:value-of select="description" />
   * //Url//: <xsl:value-of select="location/@url" />
   * //Parameter IN//: <xsl:apply-templates select="location" />
   * //Parameter OUT//: <xsl:apply-templates select="return" />
</xsl:template>

<!-- list input parameters -->
<xsl:template match="location">
	<xsl:variable name="lcount" select="count(param)" />
	<xsl:for-each select="param">
		<xsl:value-of select="@key" /> = <xsl:value-of select="@value" /><xsl:if test="position() &lt; $lcount">, </xsl:if> 
	</xsl:for-each>
</xsl:template>

<!-- list output parameters -->
<xsl:template match="return">
	<xsl:variable name="rcount" select="count(param)" />
	<xsl:for-each select="param">
		<xsl:value-of select="@key" /><xsl:if test="position() &lt; $rcount">, </xsl:if>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>