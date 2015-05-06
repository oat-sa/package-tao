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

	<xsl:template
		name="table_tr_even_odd">
		<xsl:param name="position" />
		<!--<xsl:param name="layout" />-->
		<xsl:attribute name="class">
			<xsl:choose>
				<xsl:when test="$position mod 2 = 1">
					<xsl:text>even</xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>odd</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
            <!--<xsl:if test='$layout="slider"'>-->
				<!--<xsl:text> tr_slider</xsl:text>-->
            <!--</xsl:if>-->

				<!--<xsl:text> tr_slider_extended</xsl:text>-->

		</xsl:attribute>
	</xsl:template>

	<xsl:template
		name="table_tr_td">
		<xsl:param name="count" />
		<xsl:attribute name="class">
			<xsl:choose>
        <!--if tag column is present it's override the classic sizes-->
				<xsl:when test="ancestor::itemGroup/styles[@type='column']">

				</xsl:when>
				<xsl:when test="$count&gt;0 and $count&lt;=2">
					<xsl:text>xlarge </xsl:text>
				</xsl:when>
				<xsl:when test="$count&gt;2 and $count&lt;=4">
					<xsl:text>large </xsl:text>
				</xsl:when>
				<xsl:when test="$count&gt;4 and $count&lt;=6">
					<xsl:text>medium </xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>small </xsl:text>
				</xsl:otherwise>
			</xsl:choose>
      <xsl:text>labelText</xsl:text>
    </xsl:attribute>
	</xsl:template>

	<xsl:template
		name="instruction">
		<xsl:attribute name="class">
			<xsl:text>instruction</xsl:text>
		</xsl:attribute>
	</xsl:template>

	<xsl:template
		name="response_description">
		<xsl:attribute name="class">
			<xsl:text>response_description</xsl:text>
		</xsl:attribute>
	</xsl:template>
	
	<xsl:template
		name="question_description">
		<xsl:attribute name="class">
			<xsl:text>question_description</xsl:text>
		</xsl:attribute>
	</xsl:template>

  <!-- footer -->
  <xsl:template
    match="footer"
    mode="generic">
    <param name="column" />
    <tfoot>
      <tr class='even'>
        <td class='rowSep'>
          <xsl:attribute name="colspan">
            <xsl:value-of select="$column" />
          </xsl:attribute>
        </td>
      </tr>
      <tr class='odd'>
        <td>
        </td>
        <td>
          <xsl:attribute name="colspan">
            <xsl:value-of select="$column" />
          </xsl:attribute>
          <p><xsl:value-of disable-output-escaping="yes" select='.' /></p>
        </td>
      </tr>
    </tfoot>
  </xsl:template>
</xsl:stylesheet>