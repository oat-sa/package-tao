<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

  <xsl:output method="xml" version="1.0" encoding="utf-8" indent="yes" omit-xml-declaration="yes" />

  <!-- Item group -->
  <xsl:template match="itemGroup" mode="multiple">
    <!--3 first instruction management-->
    <xsl:apply-templates select="." mode="generic" />
    <table id="itemGroupTable" class="multipleItemsTable">
      <xsl:apply-templates select="." mode="multipleHeader" />
      <tbody>
        <xsl:apply-templates select="item" mode="multiple" />
      </tbody>
    </table>
  </xsl:template>

  <!-- item -->
  <xsl:template match="item" mode="multiple">
      <tr>
        <xsl:call-template name="table_tr_even_odd">
          <xsl:with-param name="position" select="position()" />
        </xsl:call-template>
        <td class="tdItemId"><xsl:apply-templates select="." mode="getId" />)</td>
        <xsl:apply-templates select="responses" mode="multiple" />
      </tr>
  </xsl:template>

  <!-- responses -->
  <xsl:template match="responses" mode="multiple">
      <xsl:apply-templates select="response" mode="multiple" />
  </xsl:template>

  <!-- response -->
  <xsl:template match="response" mode="multiple">
    <xsl:variable name="code">
      <xsl:apply-templates select="." mode="form_code" />
    </xsl:variable>
    <td>
      <table class="trendUL">
        <tr>
          <td>
            <label>
              <xsl:attribute name="for">
                <xsl:value-of select='$code' />
              </xsl:attribute>
              <xsl:value-of select="label" />
            </label>
        </td>
        <td>
          <xsl:apply-templates select="." mode="form" />
        </td>
      </tr>
    </table>
        <xsl:apply-templates select="description" mode="list" />
    </td>
  </xsl:template>
  <!--put the table header-->
  <xsl:template match="itemGroup" mode="multipleHeader">
    <thead>
      <tr>
        <xsl:call-template name="firstColumnHeader" />
        <xsl:call-template name="multipleColumnHeader">
          <xsl:with-param name="nb">
            <xsl:call-template name="maxResponse">
              <xsl:with-param name="item" select="item[1]" />
              <xsl:with-param name="max" select="'0'" />
            </xsl:call-template>
          </xsl:with-param>
        </xsl:call-template>
      </tr>
    </thead>
  </xsl:template>
</xsl:stylesheet>