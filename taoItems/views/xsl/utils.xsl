<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <!--remove the last occurrence of char in a string with all followings characters-->
  <xsl:template name="stripLast">
    <xsl:param name="string" />
    <xsl:param name="char" />
    <xsl:if test="contains($string, $char)">
      <xsl:value-of select="substring-before($string, $char)" />
      <xsl:call-template name="stripLast">
        <xsl:with-param name="string" select="substring-after($string, $char)" />
        <xsl:with-param name="char" select="$char" />
      </xsl:call-template>
    </xsl:if>
  </xsl:template>
  <!--get the style attribute to have the width of column-->
  <xsl:template name="columnWidth">
    <xsl:param name="match" />
    <xsl:if test="//styles[@type='column'][@match=$match]/key[@name='size']">
      <xsl:attribute name="style">
        width:<xsl:value-of select="//styles[@type='column'][@match=$match]/key[@name='size']" />%;
      </xsl:attribute>
    </xsl:if>
  </xsl:template>
  <!--fist column display-->
  <xsl:template name="firstColumnHeader">
    <th>
      <xsl:call-template name="columnWidth">
        <xsl:with-param name="match" select="'1'" />
      </xsl:call-template>
    </th>
  </xsl:template>
  <!--Count the maximum of tags in a list of parent tags-->
  <xsl:template name="maxResponse">
    <xsl:param name="item" />
    <xsl:param name="max" />
    <xsl:choose>
      <xsl:when test="count($item/responses/response)&gt;$max">
        <xsl:choose>
          <xsl:when test="$item/following-sibling::item">
            <xsl:call-template name="maxResponse">
              <xsl:with-param name="item" select="$item/following-sibling::item[1]" />
              <xsl:with-param name="max" select="count($item/responses/response)" />
            </xsl:call-template>
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="count($item/responses/response)" />
          </xsl:otherwise>
        </xsl:choose>
      </xsl:when>
      <xsl:otherwise>
        <xsl:choose>
          <xsl:when test="$item/following-sibling::item">
            <xsl:call-template name="maxResponse">
              <xsl:with-param name="item" select="$item/following-sibling::item[1]" />
              <xsl:with-param name="max" select="$max" />
            </xsl:call-template>
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="$max" />
          </xsl:otherwise>
        </xsl:choose>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  <!--generate a number of th equal to the param passed-->
  <xsl:template name="multipleColumnHeader">
    <xsl:param name="nb" />
    <xsl:call-template name="multipleColumnHeaderRec">
      <xsl:with-param name="nb" select="$nb" />
      <xsl:with-param name="total" select="$nb" />
    </xsl:call-template>
  </xsl:template>
  <!--get the column size for each header if exists-->
  <xsl:template name="multipleColumnHeaderRec">
    <xsl:param name="nb" />
    <xsl:param name="total" />
    <th>
      <xsl:call-template name="columnWidth">
        <xsl:with-param name="match" select="$total - $nb + 2" />
      </xsl:call-template>
    </th>
    <xsl:if test="$nb&gt;1">
      <xsl:call-template name="multipleColumnHeaderRec">
      <xsl:with-param name="nb" select="$nb - 1" />
      <xsl:with-param name="total" select="$total" />
    </xsl:call-template>
    </xsl:if>
  </xsl:template>
</xsl:stylesheet>