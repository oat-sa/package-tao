<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

  <xsl:output method="xml" version="1.0" encoding="utf-8" indent="yes" omit-xml-declaration="yes" />

  <!-- list -->
  <xsl:template match="item" mode="list">
    <xsl:apply-templates select="responses" mode="list" />
  </xsl:template>

  <!-- responses -->
  <xsl:template match="responses" mode="list">
    <table id="itemGroupTable" class="list_table">
      <thead>
        <tr>
          <xsl:call-template name="firstColumnHeader" />
          <!--no match on code cause of list == only one column of input-->
          <xsl:choose>
            <xsl:when test="ancestor::item/header and ancestor::item/@layout != 'slider'">
              <xsl:apply-templates select="ancestor::item/header" mode="matrix" />
            </xsl:when>
            <xsl:otherwise>
              <th>
                <xsl:call-template name="columnWidth">
                  <xsl:with-param name="match" select="'2'" />
                </xsl:call-template>
              </th>
            </xsl:otherwise>
          </xsl:choose>
          <!--after fte things-->
          <th>
            <xsl:call-template name="columnWidth">
              <xsl:with-param name="match" select="'2'" />
            </xsl:call-template>
          </th>
        </tr>
      </thead>
      <tbody>
        <xsl:apply-templates select="response" mode="list" />
        <xsl:variable name='footer'>
          <xsl:value-of select="footer" />
        </xsl:variable>
        <xsl:if test='$footer!=""'>
          <xsl:apply-templates select="footer" mode="list" />
        </xsl:if>
      </tbody>
    </table>
  </xsl:template>
  <!-- response -->
  <xsl:template match="response" mode="list">
    <tr>
      <xsl:call-template name="table_tr_even_odd">
        <xsl:with-param name="position" select="position()" />
      </xsl:call-template>
      <xsl:choose>
        <xsl:when test="input">
          <td>
            <xsl:call-template name="table_tr_td">
              <xsl:with-param name="count" select="count(.)" />
            </xsl:call-template>
            <!--@todo managing by tag now-->
            <xsl:value-of disable-output-escaping="yes" select="substring-before(., '[FTE]')" />
            <xsl:apply-templates select="description" mode="list" />
          </td>
          <td>
            <xsl:apply-templates select="." mode="form" />
          </td>
          <td class="td_after_fte">
            <xsl:call-template name="table_tr_td">
              <xsl:with-param name="count" select="count(.)" />
            </xsl:call-template>
            <!--@todo managing by tag now-->
            <xsl:value-of disable-output-escaping="yes" select="substring-after(., '[FTE]')" />
          </td>
        </xsl:when>
        <xsl:otherwise>
          <td>
            <xsl:call-template name="table_tr_td">
              <xsl:with-param name="count" select="count(.)" />
            </xsl:call-template>
            <xsl:value-of disable-output-escaping="yes" select="label" />
            <xsl:apply-templates select="description" mode="list" />
          </td>
          <td>
            <xsl:if test="ancestor::item/@layout='slider'">
              <xsl:attribute name="class">
				  <xsl:text>td_slider</xsl:text>
			  </xsl:attribute>
              <span class="sliderHeader">
                <xsl:apply-templates select="." mode="sliderHeader" />
              </span>
            </xsl:if>
            <xsl:apply-templates select="." mode="form" />
          </td>
          <td class="td_after_fte"></td>
        </xsl:otherwise>
      </xsl:choose>
    </tr>
  </xsl:template>
  <!--match the response with the header-->
  <xsl:template match="response" mode="sliderHeader">
    <xsl:variable name="code">
      <xsl:value-of select="code" />
    </xsl:variable>
    <xsl:value-of select="ancestor::itemGroup/header[@code=$code]" />
  </xsl:template>
  <!-- response/description -->
  <xsl:template match="response/description" mode="list">
    <p>
      <xsl:call-template name="response_description" />
      <xsl:value-of disable-output-escaping="yes" select="." />
    </p>
  </xsl:template>

  <!-- footer -->
  <xsl:template match="responses/footer" mode="list">
    <xsl:apply-templates select="." mode="generic">
      <xsl:with-param name="column" select="2" />
    </xsl:apply-templates>
  </xsl:template>
</xsl:stylesheet>