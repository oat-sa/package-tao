<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

  <xsl:output method="xml" version="1.0" encoding="utf-8" indent="yes" omit-xml-declaration="yes" />

  <!-- matrix -->
  <xsl:template match="item" mode="matrix">
    <table id="itemGroupTable" class="matrix_table">
      <thead>
        <tr>
          <xsl:call-template name="firstColumnHeader" />
          <xsl:apply-templates select="header" mode="matrix" />
        </tr>
      </thead>
      <xsl:apply-templates select="responses" mode="matrix" />
    </table>
  </xsl:template>

  <!-- matrix -->
  <xsl:template match="header" mode="matrix">
    <th>
      <xsl:call-template name="columnWidth">
        <xsl:with-param name="mach" select="position()+1" />
      </xsl:call-template>
      <xsl:value-of disable-output-escaping="yes" select="label" />
    </th>
  </xsl:template>

  <!-- responses -->
  <xsl:template match="responses" mode="matrix">
    <tbody>
      <xsl:apply-templates select="response" mode="matrix" />
      <xsl:variable name='footer'>
        <xsl:value-of select="footer" />
      </xsl:variable>
      <xsl:if test='$footer!=""'>
        <xsl:apply-templates select="footer" mode="matrix" />
      </xsl:if>
    </tbody>
  </xsl:template>

  <!-- response -->
  <xsl:template match="response" mode="matrix">

    <tr>
      <xsl:call-template name="table_tr_even_odd">
        <xsl:with-param name="position" select="position()" />
      </xsl:call-template>
      <td>
        <!--<xsl:call-template name="table_tr_td" >-->
        <!--<xsl:with-param name="count" select="count(response)"/>-->
        <!--</xsl:call-template>-->
        <xsl:apply-templates select="label" mode="generic" />
        <xsl:apply-templates select="description" mode="list" />
      </td>
      <xsl:apply-templates select="ancestor::item/header" mode="matrix_row">
        <xsl:with-param name="response" select="." />
      </xsl:apply-templates>

    </tr>
  </xsl:template>

  <!-- label -->
  <xsl:template match="label" mode="generic">
    <!--@todo thing about fte and in text input-->
    <xsl:value-of disable-output-escaping="yes" select="." />
  </xsl:template>

  <!--&lt;!&ndash; response &ndash;&gt;-->
  <!--<xsl:template-->
  <!--match="response"-->
  <!--mode="matrix_header">-->
  <!--<th class="column_header">-->
  <!--<xsl:choose>-->
  <!--<xsl:when test="contains(., '[FTE]')">-->
  <!--<xsl:value-of disable-output-escaping="yes" select="substring-before(., '[FTE]')"/>-->
  <!--<xsl:value-of disable-output-escaping="yes" select="substring-after(., '[FTE]')"/>-->
  <!--</xsl:when>-->
  <!--<xsl:when test="ancestor::itemGroup[@layout='slider']">-->
  <!--</xsl:when>-->
  <!--<xsl:otherwise>-->
  <!--<xsl:value-of disable-output-escaping="yes" select="."/>-->
  <!--</xsl:otherwise>-->
  <!--</xsl:choose>-->
  <!--</th>-->
  <!--</xsl:template>-->

  <!-- response -->
  <xsl:template match="header" mode="matrix_row">
    <xsl:param name="response" />
    <td>
      <xsl:apply-templates select="$response" mode="form">
        <xsl:with-param name="code2" select="code" />
        <xsl:with-param name="position" select="position()" />
      </xsl:apply-templates>
    </td>
  </xsl:template>
  <!-- footer -->
  <xsl:template match="responses/footer" mode="matrix">
    <xsl:apply-templates select="." mode="matrix">
      <xsl:with-param name="column" select="count(ancestor::item/header) + 1" />
    </xsl:apply-templates>
  </xsl:template>
  <!-- footer -->
  <xsl:template match="footer" mode="matrix">
    <param name="column" />
    <tr class='even'>
      <td class='rowSep'>
        <xsl:attribute name="colspan">
          <xsl:value-of select="$column" />
        </xsl:attribute>
      </td>
    </tr>
    <tr class='odd responsesFooter'>
      <td></td>
      <td>
        <xsl:attribute name="colspan">
          <xsl:value-of select="$column" />
        </xsl:attribute>
        <p>
          <xsl:value-of disable-output-escaping="yes" select='.' />
        </p>
      </td>
    </tr>
  </xsl:template>
</xsl:stylesheet>