<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <!--return the response condition of the response or the general response condition if none-->
  <!--minValue-->
  <xsl:template match="response" mode="minval">
    <xsl:choose>
      <xsl:when test="@minValue">
        <xsl:value-of select="@minValue" />
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="ancestor::itemGroup/responseCondition/@minValue" />
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  <!--maxValue-->
  <xsl:template match="response" mode="maxval">
    <xsl:choose>
      <xsl:when test="@maxValue">
        <xsl:value-of select="@maxValue" />
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="ancestor::itemGroup/responseCondition/@maxValue" />
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  <!--minLength-->
  <xsl:template match="response" mode="minlen">
    <xsl:choose>
      <xsl:when test="@minLength">
        <xsl:value-of select="@minLength" />
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="ancestor::itemGroup/responseCondition/@minLength" />
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  <!--maxLength-->
  <xsl:template match="response" mode="maxlen">
    <xsl:choose>
      <xsl:when test="@maxLength">
        <xsl:value-of select="@maxLength" />
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="ancestor::itemGroup/responseCondition/@maxLength" />
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  <!--format-->
  <xsl:template match="response" mode="format">
    <xsl:choose>
      <xsl:when test="@format">
        <xsl:value-of select="@format" />
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="ancestor::itemGroup/responseCondition/@format" />
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  <!--step-->
  <xsl:template match="response" mode="step">
    <xsl:choose>
      <xsl:when test="@step">
        <xsl:value-of select="@step" />
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="ancestor::itemGroup/responseCondition/@step" />
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
</xsl:stylesheet>