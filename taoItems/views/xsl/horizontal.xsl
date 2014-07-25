<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
  <xsl:output method="xml" version="1.0" encoding="utf-8" indent="yes" omit-xml-declaration="yes" />
  <!-- list -->
  <xsl:template match="item" mode="horizontal">
    <xsl:apply-templates select="responses" mode="horizontal" />
  </xsl:template>
  <!-- responses -->
  <xsl:template match="responses" mode="horizontal">
    <table id="itemGroupTable" class="horizontal_table">
      <thead>
        <tr>
          <xsl:apply-templates select="response" mode="horizontal_label" />
        </tr>
      </thead>
      <tbody>
        <tr>
          <xsl:apply-templates select="response" mode="horizontal_input" />
        </tr>
      </tbody>
    </table>
  </xsl:template>
  <!-- response input-->
  <xsl:template match="response" mode="horizontal_input">
    <td>
      <xsl:apply-templates select="." mode="form" />
    </td>
  </xsl:template>
  <!-- response label-->
  <xsl:template match="response" mode="horizontal_label">
    <th>
      <xsl:call-template name="columnWidth">
        <xsl:with-param name="mach" select="position()" />
      </xsl:call-template>
      <xsl:value-of disable-output-escaping="yes" select="label" />
      <xsl:apply-templates select="description" mode="list" />
    </th>
  </xsl:template>
</xsl:stylesheet>