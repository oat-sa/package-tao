<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <!--generic item header-->
  <xsl:template match="itemGroup" mode="generic">
    <xsl:apply-templates select="." mode="orderSubject" />
    <xsl:apply-templates select="instruction" mode="generic" />
  </xsl:template>

  <!--generic item-->
  <xsl:template match="itemGroup" mode="orderSubject">

    <xsl:choose>
      <xsl:when test="count(description/following-sibling::subject)&gt;0">
        <xsl:apply-templates select="description" mode="generic">
          <xsl:with-param name="pos" select="'top'" />
        </xsl:apply-templates>
        <xsl:apply-templates select="subject" mode="generic">
          <xsl:with-param name="pos" select="'bottom'" />
        </xsl:apply-templates>
      </xsl:when>
      <xsl:otherwise>
        <xsl:apply-templates select="subject" mode="generic">
          <xsl:with-param name="pos" select="'top'" />
        </xsl:apply-templates>
        <xsl:apply-templates select="description" mode="generic">
          <xsl:with-param name="pos" select="'bottom'" />
        </xsl:apply-templates>
      </xsl:otherwise>
    </xsl:choose>

  </xsl:template>
  <!--subject-->
  <xsl:template match="subject" mode="generic">
    <xsl:param name="pos" />
    <xsl:variable name='value'>
      <xsl:value-of disable-output-escaping="yes" select="." />
    </xsl:variable>
    <xsl:if test="$value!=''">
      <p class="question">
        <xsl:if test="$pos='top'">
          <xsl:attribute name="class">
            <xsl:text>question noMarginTop</xsl:text>
          </xsl:attribute>
        </xsl:if>
        <xsl:value-of disable-output-escaping="yes" select="." />
      </p>
    </xsl:if>
  </xsl:template>
  <!-- description of item-->
  <xsl:template match="itemGroup/description" mode="generic">
    <xsl:param name="pos" />
    <p>
      <xsl:call-template name="question_description" />
      <xsl:if test="$pos='top'">
        <xsl:attribute name="class">
          <xsl:text>question_description noMarginTop</xsl:text>
        </xsl:attribute>
      </xsl:if>
      <xsl:value-of select="." disable-output-escaping="yes" />
    </p>
  </xsl:template>
  <!--instruction-->
  <xsl:template match="instruction" mode="generic">
    <p>
      <xsl:call-template name="instruction" />
      <xsl:value-of disable-output-escaping="yes" select="." />
    </p>
  </xsl:template>

</xsl:stylesheet>